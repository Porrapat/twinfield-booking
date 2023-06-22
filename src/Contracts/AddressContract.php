<?php

namespace Qlic\Twinfield\Booking\Contracts;

use Qlic\Twinfield\Booking\Enums\AddressType;

interface AddressContract
{
    /**
     * The type of address
     * @return AddressType
     */
    public function getType(): AddressType;

    /**
     * The company name
     * @return string
     */
    public function getCompanyName(): string;

    /**
     * The country in short format, example: NL
     * @return string
     */
    public function getCountry(): string;

    /**
     * The city of the address
     * @return string
     */
    public function getCity(): string;

    /**
     * The zipcode of the address
     * @return string
     */
    public function getZipcode(): string;

    /**
     * The phone number of the Customer/Supplier
     * @return string
     */
    public function getPhone(): string;

    /**
     * The email of the Customer/Supplier
     * @return null|string
     */
    public function getEmail(): ?string;

    /**
     * The VAT number of the Customer/Supplier
     * @return string
     */
    public function getVatNumber(): string;

    /**
     * The chamber of commerce number of the Customer/Supplier
     * @return string
     */
    public function getCocNumber(): string;

    /**
     * The 'first line' of the address.
     * Often this will be the street name
     * @return string|null
     */
    public function getAddressFirstLine(): ?string;

    /**
     * The 'second line' of the address.
     * @return string|null
     */
    public function getAddressSecondLine(): ?string;

    /**
     * The person we are addressing when sending mail to this person/entity
     * Example: Administration department
     * @return string|null
     */
    public function getAttention(): ?string;
}
