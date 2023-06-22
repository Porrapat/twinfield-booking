<?php

namespace Qlic\Twinfield\Booking\Generators;

use PhpTwinfield\BaseTransaction;
use PhpTwinfield\BaseTransactionLine;
use PhpTwinfield\Enums\LineType;
use PhpTwinfield\Exception;
use Qlic\Twinfield\Booking\Contracts\InvoiceLineContract;

class DetailLineGenerator
{
    /**
     * Creates a twinfield detail line with data from the invoice line
     * @param BaseTransaction $transaction
     * @param  InvoiceLineContract $invoiceLine
     * @param int $lineId
     * @return BaseTransactionLine
     */
    public static function create(BaseTransaction $transaction, InvoiceLineContract $invoiceLine, int $lineId)
    {
        $line = self::createLine($transaction);

        $line->setId($lineId);

        $line->setLineType(LineType::DETAIL());

        $line->setValue($invoiceLine->getPriceWithoutVat());

        $line->setComment($invoiceLine->getDescription());

        $line->setDescription(substr($invoiceLine->getDescription(),0, 40));

        try {
            $total = $invoiceLine->getPriceWithVat();
            $line->setVatValue($total->subtract($invoiceLine->getPriceWithoutVat()));
            $line->setVatCode($invoiceLine->getVatCode());
        } catch (Exception $e) {
            \Log::error("Twinfield ERROR | Can't set VAT: {$e->getMessage()}");
        }

        /**
         * The profit and loss account.
         */
        $line->setDim1($invoiceLine->getDetailLineDim1());

        /**
         * The cost center
         */
        $line->setDim2($invoiceLine->getDetailLineDim2());

        /**
         * This is actually dim3
         */
        if (is_string($invoiceLine->getAsset())) {
            $line->setProjectAsset($invoiceLine->getAsset());
        }

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
