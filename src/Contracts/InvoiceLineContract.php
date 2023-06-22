<?php

namespace Qlic\Twinfield\Booking\Contracts;

use Money\Money;
use PhpTwinfield\Enums\DebitCredit;

interface InvoiceLineContract
{
    /**
     * Returns the price without vat
     * @return Money
     */
    public function getPriceWithoutVat(): Money;

    /**
     * Returns the price with vat
     * @return Money
     */
    public function getPriceWithVat(): Money;

    /**
     * The VAT code we want to book this line on
     * @return null|string
     */
    public function getVatCode(): ?string;

    /**
     * The project or asset this line is for
     * @return string
     */
    public function getAsset(): ?string;

    /**
     * Description for the transaction
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * The dim 1 field for detail lines
     * @return string|null
     */
    public function getDetailLineDim1(): string;

    /**
     * The dim 2 field for detail lines
     *
     * @return string|null
     */
    public function getDetailLineDim2(): ?string;
}
