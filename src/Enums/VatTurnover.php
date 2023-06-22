<?php

namespace Qlic\Twinfield\Booking\Enums;

use MyCLabs\Enum\Enum;

/**
 * base:        Amount on which VAT was calculated in base currency.
 * reporting:   Amount on which VAT was calculated in reporting currency
 * transaction: Amount on which VAT was calculated in the currency of the sales transaction
 *
 * @method static VatTurnover BASE()
 * @method static VatTurnover REPORTING()
 * @method static VatTurnover TRANSACTION()
 */
class VatTurnover extends Enum
{
    private const BASE = 'BASE';
    private const REPORTING = 'REPORTING';
    private const TRANSACTION = 'TRANSACTION';
}
