<?php

namespace Qlic\Twinfield\Booking\Generators;

use PhpTwinfield\BaseTransaction;
use PhpTwinfield\BaseTransactionLine;
use PhpTwinfield\Enums\LineType;
use Qlic\Twinfield\Booking\Contracts\InvoiceContract;
use Qlic\Twinfield\Booking\Enums\VatTurnover;

class VatLineGenerator
{
    /**
     * Creates a VAT line for a transaction based on an invoiceable.
     * @param  BaseTransaction $transaction
     * @param  InvoiceContract $invoice
     * @return BaseTransactionLine
     */
    public static function create(BaseTransaction $transaction, InvoiceContract $invoice, int $lineId)
    {
        if (is_null($invoice->getVatTurnover(VatTurnover::TRANSACTION()))) {
            throw new \UnexpectedValueException("The VAT turnover for transaction is required.");
        }

        $line = self::createLine($transaction);

        $line->setId($lineId);

        $line->setLineType(LineType::VAT());

        $line->setValue($invoice->getVatTotal());

        $line->setDim1($invoice->getVatLineDim1());

        /**
         * Amount on which VAT was calculated in the currency of the sales transaction.
         */
        $line->setVatTurnover($invoice->getVatTurnover(VatTurnover::TRANSACTION()));

        /**
         * Amount on which VAT was calculated in base currency.
         */
        $line->setVatBaseTurnover($invoice->getVatTurnover(VatTurnover::BASE()));

        /**
         * Amount on which VAT was calculated in reporting currency
         */
        $line->setVatRepTurnover($invoice->getVatTurnover(VatTurnover::REPORTING())); // Not sure if needed

        // TODO: Reference to the id of the VAT percentage line, where to find this?
        $line->setBaseline($invoice->getVATBaseLine());

        $line->setDebitCredit($invoice->getDebitCredit());

        return $line;
    }

    /**
     * Create a new instance of a transaction line of the passed transaction
     * @param BaseTransaction $transaction
     * @return BaseTransactionLine
     */
    public static function createLine(BaseTransaction $transaction): BaseTransactionLine
    {
        $lineClassName = $transaction->getLineClassName();

        return new $lineClassName;
    }
}
