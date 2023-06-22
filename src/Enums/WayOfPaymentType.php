<?php

namespace Qlic\Twinfield\Booking\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static WayOfPaymentType NORMAL()
 * @method static WayOfPaymentType CHEQUE()
 * @method static WayOfPaymentType BASIS()
 */
class WayOfPaymentType extends Enum
{
    /**
     * Check twinfield api documentation for more info.
     */
    private const NORMAL = 0;
    private const CHEQUE = 1;
    private const BASIS = 2;
}
