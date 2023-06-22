<?php

namespace Qlic\Twinfield\Booking\Contracts;

use PhpTwinfield\Customer;

interface CustomerContract
{
    /**
     * Gets the code from the customer in twinfield
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * Return the name of the customer
     * @return string
     */
    public function getName(): string;

    /**
     * The website of the customer
     * @return string|null
     */
    public function getWebsite(): ?string;

    /**
     * This method will be called when a customer has been successfully created
     * It passes through the created customer
     * Return value will be returned from InvoiceBooker
     * @param Customer $customer
     */
    public function callback(Customer $customer);
}
