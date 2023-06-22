<?php

namespace Qlic\Twinfield\Booking\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static RelationType CUSTOMER()
 * @method static RelationType SUPPLIER()
 */
class RelationType extends Enum
{
    private const CUSTOMER = 'customer';
    private const SUPPLIER = 'supplier';
}
