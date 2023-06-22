<?php

namespace Qlic\Twinfield\Booking\Generators;

use App\Models\Organisation;
use Qlic\Twinfield\Booking\Enums\RelationType;

class TwinfieldCodeGenerator
{
    /**
     * Returns a new twinfield code.
     * @param RelationType $type
     * @throws \UnexpectedValueException
     * @return string
     */
    public static function create(RelationType $type): string
    {
        $prefix = config("twinfield-booking.prefixes.{$type->getValue()}");
        if (is_null($prefix)) {
            $message = "Type: {$type->getValue()} is not accounted for in TwinfieldCodeGenerator";
            throw new \UnexpectedValueException($message);
        }

        switch ($type->getValue()) {
            case 'customer':
                $columnToSearchFor = 'twin_debitor_code';
                break;
            case 'supplier':
                $columnToSearchFor = 'twin_creditor_code';
                break;
            default:
                $message = "Type: {$type->getValue()} is not accounted for in TwinfieldCodeGenerator (2)";
                throw new \UnexpectedValueException($message);
        }

        $organisation = Organisation::where($columnToSearchFor, 'LIKE', "{$prefix}%")
            ->orderBy($columnToSearchFor, 'desc')
            ->first();

        if (is_null($organisation)) {
            throw new \UnexpectedValueException("No organisation with {$columnToSearchFor} of type: {$type->getValue()}");
        }

        return ++$organisation->{$columnToSearchFor};
    }
}
