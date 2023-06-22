<?php

namespace Qlic\Twinfield\Booking\Generators;

use Money\Money;
use PhpTwinfield\BaseTransaction;
use PhpTwinfield\BaseTransactionLine;
use PhpTwinfield\Enums\DebitCredit;
use PhpTwinfield\Enums\LineType;
use Qlic\Twinfield\Booking\Contracts\InvoiceContract;

class TotalLineGenerator
{
    /**
     * Creates a VAT line for a transaction based on an invoiceable.
     * @param BaseTransaction $transaction
     * @param InvoiceContract $invoice
     * @param int $lineId
     * @return BaseTransactionLine
     */
    public static function create(BaseTransaction $transaction, InvoiceContract $invoice, int $lineId)
    {
        $line = self::createLine($transaction);

        $line->setId($lineId);

        $line->setLineType(LineType::TOTAL());

        // This also automatically sets the correct debit/credit,
        // based on either the positive or negative amount
        $line->setValue($invoice->getTotalWithVat());

        /**
         * the accounts receivable balance account.
         * When dim1 is omitted, by default the general ledger account will be taken
         * (as entered at the customer in Twinfield).
         * Note: From Qlicnet it looks like dim1 contains the ledger number of the sale
         */
        $line->setDim1($invoice->getTotalLineDim1());

        /**
         * The account receivable
         * For total lines on sales transactions, this is the code of the debitor.
         */
        $line->setDim2($invoice->getTotalLineDim2());

        $line->setDescription($invoice->getDescription());

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
