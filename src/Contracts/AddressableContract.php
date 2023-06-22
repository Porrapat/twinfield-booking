<?php

namespace Qlic\Twinfield\Booking\Contracts;

interface AddressableContract
{
    /**
     * Return the addresses of the customer
     * Need to implement the AddressContract
     * @return array
     */
    public function getAddresses(): array;
}
