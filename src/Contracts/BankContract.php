<?php

namespace Qlic\Twinfield\Booking\Contracts;

use Qlic\Twinfield\Booking\Enums\PaymentAddressType;
use Qlic\Twinfield\Booking\Enums\WayOfPaymentType;

interface BankContract
{
    /**
     * Whether this is the default bank account
     * @return boolean
     */
    public function getDefault(): bool;

    /**
     * Country of the bank
     * should be an ISO code (Example: NL)
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * Return the iban account number
     * @return string|null
     */
    public function getIban(): ?string;

    /**
     * Return the account holder of the bank account
     * Should not be longer than 40 chars.
     * @return string
     */
    public function getAccountHolder(): string;

    /**
     * What way the payment is getting paid.
     * Only when creating banks for Suppliers.
     * Only when the payment type is BTL91
     * @return null|WayOfPaymentType
     */
    public function getWayOfPayment(): ?WayOfPaymentType;

    /**
     * The payment address type
     * Only if getWayOfPayment() === WayOfPaymentType::CHEQUE()
     * @return null|PaymentAddressType
     */
    public function getPaymentAddress(): ?PaymentAddressType;
}
