<?php

namespace Qlic\Twinfield\Booking\Models;

use Qlic\Twinfield\Booking\Contracts\AddressableContract;
use Qlic\Twinfield\Booking\Contracts\BankableContract;
use Qlic\Twinfield\Booking\Contracts\CustomerContract;

abstract class Customer implements CustomerContract, AddressableContract, BankableContract
{
    //
}
