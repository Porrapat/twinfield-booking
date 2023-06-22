<?php

namespace Qlic\Twinfield\Booking\Contracts;

use PhpTwinfield\Supplier;

interface SupplierContract
{
    /**
     * Gets the code from the supplier in twinfield
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * Return the name of the supplier
     * @return string
     */
    public function getName(): string;

    /**
     * The website of the supplier
     * @return string|null
     */
    public function getWebsite(): ?string;

    /**
     * This method will be called when a supplier has been successfully created
     * It passes through the created supplier
     * Return value will be returned from InvoiceBooker
     * @param Supplier $supplier
     */
    public function callback(Supplier $supplier);
}
