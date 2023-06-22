<?php

namespace Qlic\Twinfield\Booking\Contracts;

interface BankableContract
{
    /**
     * Return the bank of the customer/supplier
     * @return BankContract
     */
    public function getBank(): ?BankContract;
}
