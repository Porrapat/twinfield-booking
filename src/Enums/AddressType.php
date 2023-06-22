<?php

namespace Qlic\Twinfield\Booking\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static AddressType INVOICE()
 * @method static AddressType POSTAL()
 * @method static AddressType CONTACT()
 */
class AddressType extends Enum
{
    private const INVOICE = 'invoice';
    private const POSTAL = 'postal';
    private const CONTACT = 'contact';
}
