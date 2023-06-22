<?php

namespace Qlic\Twinfield\Booking\Generators;

use PhpTwinfield\CustomerBank;
use PhpTwinfield\SupplierBank;
use Qlic\Twinfield\Booking\Contracts\BankableContract;
use Qlic\Twinfield\Booking\Contracts\BankContract;
use Qlic\Twinfield\Booking\Models\Customer;
use Qlic\Twinfield\Booking\Models\Supplier;

class BankGenerator
{
    /**
     * Creates a new CustomerBank
     * @param BankableContract $bankable
     * @return CustomerBank|SupplierBank
     */
    public static function create(BankableContract $bankable)
    {
        $twinBank = null;
        $bank = $bankable->getBank();
        $class = get_class($bankable);

        if (!$bank instanceof BankContract) {
            throw new \UnexpectedValueException("Customer bank must implement CustomerBankContract");
        }

        if (strlen($bank->getAccountHolder()) > 40) {
            throw new \UnexpectedValueException("Account holder can't be longer than 40 chars.");
        }

        if ($bankable instanceof Customer) {
            $twinBank = new CustomerBank;
        }

        if ($bankable instanceof Supplier) {
            $twinBank = new SupplierBank;
        }

        if(is_null($twinBank)) {
            $message = "Class {$class} should extends one of the Models.";
            \Log::error($message);
            throw new \UnexpectedValueException($message);
        }

        $twinBank->setIban($bank->getIban())
            ->setDefault($bank->getDefault())
            ->setAscription($bank->getAccountHolder())
            ->setCountry($bank->getCountry());

        return $twinBank;
    }
}
