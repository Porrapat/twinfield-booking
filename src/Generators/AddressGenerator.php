<?php

namespace Qlic\Twinfield\Booking\Generators;

use PhpTwinfield\CustomerAddress;
use PhpTwinfield\SupplierAddress;
use Qlic\Twinfield\Booking\Contracts\AddressableContract;
use Qlic\Twinfield\Booking\Contracts\AddressContract;
use Qlic\Twinfield\Booking\Enums\AddressType;
use Qlic\Twinfield\Booking\Models\Customer;
use Qlic\Twinfield\Booking\Models\Supplier;

class AddressGenerator
{
    /**
     * Creates a new CustomerAddress
     * @param AddressableContract $addressable
     * @param  AddressType $type
     * @return CustomerAddress|SupplierAddress
     */
    public static function create(AddressableContract $addressable, AddressType $type)
    {
        $twinModelAddress = null;
        $class = get_class($addressable);

        // Create the address of the type asked if possible
        foreach ($addressable->getAddresses() as $address) {
            if (!$address instanceof AddressContract) {
                throw new \UnexpectedValueException("Addresses need to implement AddressContract.");
            }

            if ($type->equals($address->getType())) {
                if ($addressable instanceof Customer) {
                    $twinModelAddress = new CustomerAddress;
                }

                if ($addressable instanceof Supplier) {
                    $twinModelAddress = new SupplierAddress;
                }

                if (is_null($twinModelAddress)) {
                    $message = "Class {$class} should extends one of the Models.";
                    \Log::error($message);
                    throw new \UnexpectedValueException($message);
                }

                $twinModelAddress->setDefault(false)
                    ->setType($type->getValue())
                    ->setName($address->getCompanyName())
                    ->setCountry($address->getCountry())
                    ->setCity($address->getCity())
                    ->setPostcode($address->getZipcode())
                    ->setTelephone($address->getPhone())
                    ->setEmail($address->getEmail())
                    ->setField1($address->getAttention())
                    ->setField2($address->getAddressFirstLine())
                    ->setField3($address->getAddressSecondLine())
                    ->setField4($address->getVatNumber())
                    ->setField5($address->getCocNumber());
                break;
            }
        }

        if (is_null($twinModelAddress)) {
            $message = "Class: {$class} doesn't have any addresses of type {$type->getValue()}.";
            \Log::error($message);
            throw new \UnexpectedValueException($message);
        }

        return $twinModelAddress;
    }
}
