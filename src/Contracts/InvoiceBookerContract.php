<?php

namespace Qlic\Twinfield\Booking\Contracts;

use Qlic\Twinfield\Booking\Models\Customer;
use Qlic\Twinfield\Booking\Models\Supplier;

interface InvoiceBookerContract
{

    /**
     * Setup Twinfield connection (oAuth)
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed
     */
    public function getTwinfieldConnection(string $refreshToken, string $officeCode);

    /**
     * Creates a sales transaction
     * @param InvoiceContract $invoice
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed
     */
    public function createSalesTransaction(InvoiceContract $invoice, string $refreshToken, string $officeCode);

    /**
     * Creates a purchase transaction
     * @param InvoiceContract $invoice
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed
     */
    public function createPurchaseTransaction(InvoiceContract $invoice, string $refreshToken, string $officeCode);

    /**
     * Creates or updates a customer
     * @param Customer $customer
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed
     */
    public function createOrUpdateCustomer(Customer $customer, string $refreshToken, string $officeCode);

    /**
     * Creates or updates a customer
     * @param Supplier $supplier
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed
     */
    public function createOrUpdateSupplier(Supplier $supplier, string $refreshToken, string $officeCode);

    /**
     * Creates or updates a customer
     * @param ProjectContract $project
     * @param string $refreshToken
     * @param string $officeCode
     * @return mixed
     */
    public function createOrUpdateProject(ProjectContract $project, string $refreshToken, string $officeCode);


    /**
     * @param string $refreshToken
     * @param string $officeCode
     * @return \Illuminate\Support\Collection
     */
    public function availableVatCodes(string $refreshToken, string $officeCode);
}
