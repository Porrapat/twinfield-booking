<?php

namespace Qlic\Twinfield\Booking\Models;

use Qlic\Twinfield\Booking\Contracts\AddressableContract;
use Qlic\Twinfield\Booking\Contracts\BankableContract;
use Qlic\Twinfield\Booking\Contracts\SupplierContract;

abstract class Supplier implements SupplierContract, AddressableContract, BankableContract
{
    //
}
