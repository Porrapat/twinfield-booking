<?php

namespace Qlic\Twinfield\Booking;

use PhpTwinfield\ApiConnectors\BaseApiConnector;
use PhpTwinfield\ApiConnectors\CustomerApiConnector;
use PhpTwinfield\ApiConnectors\ProjectApiConnector;
use PhpTwinfield\ApiConnectors\SupplierApiConnector;
use PhpTwinfield\ApiConnectors\TransactionApiConnector;
use PhpTwinfield\ApiConnectors\VatCodeApiConnector;
use PhpTwinfield\Customer as TwinfieldCustomer;
use PhpTwinfield\Project;
use PhpTwinfield\PurchaseTransaction;
use PhpTwinfield\Secure\OpenIdConnectAuthentication;
use PhpTwinfield\Secure\Provider\OAuthProvider;
use PhpTwinfield\Supplier as TwinfieldSupplier;
use PhpTwinfield\Enums\Destiny;
use PhpTwinfield\Office;
use PhpTwinfield\SalesTransaction;
use Qlic\Twinfield\Booking\Contracts\ProjectContract;
use Qlic\Twinfield\Booking\Models\Customer;
use Qlic\Twinfield\Booking\Contracts\InvoiceBookerContract;
use Qlic\Twinfield\Booking\Contracts\InvoiceContract;
use Qlic\Twinfield\Booking\Enums\AddressType;
use Qlic\Twinfield\Booking\Enums\RelationType;
use Qlic\Twinfield\Booking\Generators\AddressGenerator;
use Qlic\Twinfield\Booking\Generators\DetailLineGenerator;
use Qlic\Twinfield\Booking\Generators\TotalLineGenerator;
use Qlic\Twinfield\Booking\Generators\TwinfieldCodeGenerator;
use Qlic\Twinfield\Booking\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class TwinfieldInvoiceBooker implements InvoiceBookerContract
{
    private $office;
    private $provider;

    public function __construct(OAuthProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Setup connection to the new Twinfield API
     */
    public function getTwinfieldConnection(string $refreshToken, string $officeCode)
    {
        $this->office = Office::fromCode($officeCode);
        return new OpenIdConnectAuthentication(
            $this->provider, $refreshToken,
            $this->office
        );
    }

    /**
     * Create a sales transaction in twinfield.
     * @param InvoiceContract $invoice
     * @param string $refreshToken
     * @param string $officeCode
     * @return \PhpTwinfield\SalesTransaction
     * @throws \PhpTwinfield\Exception
     */
    public function createSalesTransaction(InvoiceContract $invoice, string $refreshToken, string $officeCode)
    {
		try {
			$connector = new TransactionApiConnector($this->getTwinfieldConnection($refreshToken, $officeCode));
			
			// Log input values
            Log::info("Creating sales transaction for invoice: " . json_encode($invoice->toArray()));

			// Prepare transaction
			$transaction = new SalesTransaction;
			$transaction->setDestiny(Destiny::TEMPORARY())
				->setCurrency($invoice->getCurrency())
				->setDateFromString($invoice->getInvoiceDate()->format("Ymd"))
				->setPeriod($invoice->getInvoiceDate()->format("Y/m"))
				->setInvoiceNumber($invoice->getInvoiceNumber())
				// TODO change due date
			 //   ->setDueDate(now()->addMonth())
				->setCode(config('twinfield-booking.transactions.sales.day_book'));

			if (!is_null($invoice->getTwinfieldNumber())) {
				$transaction->setNumber($invoice->getTwinfieldNumber());
			}

			if (!is_null($this->office)) {
				$transaction->setOffice($this->office);
			}

			// Line ID's needed for the   transaction lines
			$lineID = 1;

			// Create total line on transaction
			$transaction->addLine(TotalLineGenerator::create($transaction, $invoice, $lineID));
			$lineID++;

			// Create detail lines
			foreach ($invoice->getLines() as $invoiceLine) {
                // Log each line value
                Log::info("Adding detail line to transaction: " . json_encode($invoiceLine->toArray()));
                
				$transaction->addLine(DetailLineGenerator::create($transaction, $invoiceLine, $lineID));
				$lineID++;
			}

			// Create VAT line
			// Commented this out, because we don't need to explicitly state the vat, because we already did so
			// on the detail lines. Maybe add a trigger or something to enable the Vat line.
			// $transaction->addLine(VatLineGenerator::create($transaction, $invoice, $lineID));

			/** @var SalesTransaction $twinTransaction */
			$twinTransaction = $connector->send($transaction);

            // Log success
            Log::info('Sales transaction created successfully.', [
                'invoice_number' => $invoice->getInvoiceNumber(),
                'twinfield_number' => $twinTransaction->getNumber(),
            ]);

			$invoice->callback($twinTransaction);

			return $twinTransaction;
            
        } catch (\Exception $exception) {
            // Log error
            Log::error('Error creating sales transaction.', [
                'invoice_number' => $invoice->getInvoiceNumber(),
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to allow it to be handled elsewhere
            throw $exception;
        }
    }

    /**
     * Creates or updates a customer in Twinfield.
     * Unique key is the code.
     * @param Customer $customer
     * @return TwinfieldCustomer
     * @throws \PhpTwinfield\Exception
     */
    public function createOrUpdateCustomer(Customer $customer, string $refreshToken, string $officeCode)
    {
        $connector = new CustomerApiConnector($this->getTwinfieldConnection($refreshToken, $officeCode));

        $twinInvoiceAddress = AddressGenerator::create($customer, AddressType::INVOICE());

        $twinCustomer = new TwinfieldCustomer;
        $twinCustomer->setName($customer->getName())
            ->setCode($customer->getCode())
            ->setWebsite($customer->getWebsite())
            ->addAddress($twinInvoiceAddress);

        if (!is_null($twinInvoiceAddress->getCountry())) {
            $twinCustomer->setCountry($twinInvoiceAddress->getCountry());
        }

        if (is_null($customer->getCode())) {
            $twinCustomer->setCode(TwinfieldCodeGenerator::create(RelationType::CUSTOMER()));
        }

        // TODO: Do we only add the invoice address, or do we also add postal and contact addresses?

        if (!is_null($this->office)) {
            $twinCustomer->setOffice($this->office);
        }

        /**
         * @todo Make this opt-in through config or method argument / whatever
         * Commented out the banking feature, since we shoulud only add this if the client actually has
         * an iban available, otherwise we will get a validation error from twinfield.
         */
        // if (!is_null($customer->getBank())) {
        //    $twinCustomer->addBank(BankGenerator::create($customer));
        // }

        $responseCustomer = $connector->send($twinCustomer);

        $customer->callback($responseCustomer);

        return $responseCustomer;
    }

    /**
     * @param Supplier $supplier
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed|TwinfieldCustomer
     * @throws \PhpTwinfield\Exception
     */
    public function createOrUpdateSupplier(Supplier $supplier, string $refreshToken, string $officeCode)
    {
        $connector = new SupplierApiConnector($this->getTwinfieldConnection($refreshToken, $officeCode));

        $twinInvoiceAddress = AddressGenerator::create($supplier, AddressType::INVOICE());

        $twinSupplier = new TwinfieldSupplier;
        $twinSupplier->setName($supplier->getName())
            ->setCode($supplier->getCode())
            ->setWebsite($supplier->getWebsite())
            ->addAddress($twinInvoiceAddress);

        // Current twinfield supplier doesn't support country (yet)
        // if (!is_null($twinInvoiceAddress->getCountry())) {
        //    $twinSupplier->setCountry($twinInvoiceAddress->getCountry());
        // }

        if (is_null($supplier->getCode())) {
            $twinSupplier->setCode(TwinfieldCodeGenerator::create(RelationType::SUPPLIER()));
        }

        // TODO: Do we only add the invoice address, or do we also add postal and contact addresses?

        if (!is_null($this->office)) {
            $twinSupplier->setOffice($this->office);
        }

        /**
         * @todo Make this opt-in through config or method argument / whatever
         * Commented out the banking feature, since we should only add this if the client actually has
         * an iban available, otherwise we will get a validation error from Twinfield.
         */
        // if (!is_null($supplier->getBank())) {
        //     $twinSupplier->addBank(BankGenerator::create($supplier));
        // }

        $reponseSupplier = $connector->send($twinSupplier);

        $supplier->callback($reponseSupplier);

        return $reponseSupplier;
    }

    /**
     * @param ProjectContract $project
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed|TwinfieldCustomer
     */
    public function createOrUpdateProject(ProjectContract $project, string $refreshToken, string $officeCode)
    {
		try {
            $connector = new ProjectApiConnector($this->getTwinfieldConnection($refreshToken, $officeCode));

            // Log the input project in JSON format
            Log::info('Input Project: ', ['project_data' => Arr::wrap($project)]);

            // TODO: test if 'null' shortname (or any field) clears the field, or it fucks up
			$twinProject = new Project;
			$twinProject
				->setName($project->getName())
				->setValidFromString($project->getValidFrom())
				->setValidToString($project->getValidTo())
				->setAuthoriser($project->getAuthoriser())
				->setCustomer($project->getCustomer())
				->setBillable($project->getBillable())
				->setRate($project->getRate());

			if (!is_null($project->getStatus())) {
				$twinProject->setStatus($project->getStatus());
			}

			if (!is_null($project->getQuantities())) {
				$twinProject->setQuantities($project->getQuantities());
			}

			if (!is_null($project->getCode())) {
				$twinProject->setCode($project->getCode());
			}

			if (!is_null($project->getShortName())) {
				$twinProject->setShortName($project->getShortName());
			}

			if (!is_null($project->getInvoiceDescription())) {
				$twinProject->setInvoiceDescription($project->getInvoiceDescription());
			}

			if (!is_null($this->office)) {
				$twinProject->setOffice($this->office);
			}

			$responseProject = $connector->send($twinProject);

            // Log success
            Log::info('Project created or updated successfully.', [
                'project_code' => $project->getCode(),
                'response_project_code' => $responseProject->getCode(),
            ]);

			$project->callback($responseProject);

            return $responseProject;
        } catch (\Exception $exception) {
           // Log error
            Log::error('Error creating or updating project.', [
                'project_code' => $project->getCode(),
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to allow it to be handled elsewhere
            throw $exception;
        }
    }

    /**
     * Returns a list of available vat codes
     * @param string $refreshToken
     * @param string $officeCode
     * @return \Illuminate\Support\Collection
     */
    public function availableVatCodes(string $refreshToken, string $officeCode)
    {
        $connector = new VatCodeApiConnector($this->getTwinfieldConnection($refreshToken, $officeCode));

        return collect($connector->listAll());
    }

    /**
     * @param InvoiceContract $invoice
     * @param string $refreshToken
     * @param string $officeCode
     * @return PurchaseTransaction
     * @throws \PhpTwinfield\Exception
     */
    public function createPurchaseTransaction(InvoiceContract $invoice, string $refreshToken, string $officeCode)
    {
        $connector = new TransactionApiConnector($this->getTwinfieldConnection($refreshToken, $officeCode));

        // Prepare transaction
        $transaction = new PurchaseTransaction;
        $transaction->setDestiny(Destiny::TEMPORARY())
            ->setCurrency($invoice->getCurrency())
            ->setDateFromString($invoice->getInvoiceDate()->format("Ymd"))
            ->setPeriod($invoice->getInvoiceDate()->format("Y/m"))
            ->setCode(config('twinfield-booking.transactions.purchase.day_book'))
            ->setInvoiceNumber($invoice->getInvoiceNumber());

        if (!is_null($invoice->getTwinfieldNumber())) {
            $transaction->setNumber($invoice->getTwinfieldNumber());
        }

        if (!is_null($this->office)) {
            $transaction->setOffice($this->office);
        }

        // Line ID's needed for the transaction lines
        $lineID = 1;

        // Create total line on transaction
        $transaction->addLine(TotalLineGenerator::create($transaction, $invoice, $lineID));
        $lineID++;

        // Create detail lines
        foreach ($invoice->getLines() as $invoiceLine) {
            $transaction->addLine(DetailLineGenerator::create($transaction, $invoiceLine, $lineID));
            $lineID++;
        }

        // Create VAT line
        // Commented this out, because we don't need to explicitly state the vat, because we already did so
        // on the detail lines. Maybe add a trigger or something to enable the Vat line.
        // $transaction->addLine(VatLineGenerator::create($transaction, $invoice, $lineID));

        /** @var PurchaseTransaction $twinTransaction */
        $twinTransaction = $connector->send($transaction);

        $invoice->callback($twinTransaction);

        return $twinTransaction;
    }

    /**
     * Returns an asset usable for twinfield
     * Creates one if it doesn't exist yet
     * @return string
     */
    public function getOrCreateProject()
    {
        return null;
    }
}
