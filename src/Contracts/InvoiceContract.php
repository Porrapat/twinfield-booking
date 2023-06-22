<?php

namespace Qlic\Twinfield\Booking\Contracts;

use Carbon\Carbon;
use Money\Currency;
use Money\Money;
use PhpTwinfield\BaseTransaction;
use PhpTwinfield\Enums\DebitCredit;
use Qlic\Twinfield\Booking\Enums\VatTurnover;

interface InvoiceContract
{
    /**
     * The invoice number
     * @return string
     */
    public function getInvoiceNumber(): string;

    /**
     * When updating an existing invoice, provide the twinfield number.
     * @return int|null
     */
    public function getTwinfieldNumber(): ?int;

    /**
     * The invoice date in format
     * @return Carbon
     */
    public function getInvoiceDate(): Carbon;

    /**
     * The due date
     * @return Carbon
     */
    public function getDueDate(): Carbon;

    /**
     * A collection with invoice lines
     * @return array<InvoiceLineContract>
     */
    public function getLines(): array;

    /**
     * The currency of the transaction
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * The total of the invoice with vat included
     * @return Money
     */
    public function getTotalWithVat(): Money;

    /**
     * The total of the invoice with vat excluded
     * @return Money
     */
    public function getTotalWithoutVat(): Money;

    /**
     * The VAT total
     * @return Money
     */
    public function getVatTotal(): Money;

    /**
     * Returns amount the vat was calculated on
     * Check the VatTurnover class for more documentation
     * @param  VatTurnover $turnover
     * @return Money|null
     */
    public function getVatTurnover(VatTurnover $turnover): ?Money;

    /**
     * The value of the baseline tag is a reference to the line ID of the VAT rate
     * @return string|null
     */
    public function getVATBaseLine(): ?string;

    /**
     * The dim 1 field for total lines
     * @return string|null
     */
    public function getVatLineDim1(): ?string;

    /**
     * The dim 1 field for total lines
     * @return string|null
     */
    public function getTotalLineDim1(): ?string;

    /**
     * The dim 2 field for total lines
     * @return string|null
     */
    public function getTotalLineDim2(): ?string;

    /**
     * The description of the invoice
     * Note: should be 40 characters or less.
     * @return string|null
     */
    public function getDescription(): string;

    /**
     * Callback with the retrieved transaction
     * @param BaseTransaction $transaction
     */
    public function callback(BaseTransaction $transaction): void;
}
