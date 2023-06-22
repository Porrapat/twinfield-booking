<?php

namespace Qlic\Twinfield\Booking\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static WayOfPaymentType BANK_PAYER()
 * @method static WayOfPaymentType PAYER()
 * @method static WayOfPaymentType BENEFICIARY()
 */
class PaymentAddressType extends Enum
{
    /**
     * Check twinfield api documentation for more info.
     */
    private const BANK_PAYER = 1;
    private const PAYER = 2;
    private const BENEFICIARY = 3;
}
