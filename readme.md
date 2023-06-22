# Twinfield booking

## Installation

First add the repository to your composer.json, so composer can find this package.

```json
"repositories": [
    {
        "type": "vcs",
        "url": "ssh://git@gitlab.qlic.nl/packages/twinfield-booking.git"
    }
],
```

Then add the package as a dependency.

```bash
composer require qlic/twinfield-booking
```

In case you are running Laravel < 5.5
Add the following provider to your service providers (in `config/app.php`)

```php
Qlic\Twinfield\Booking\Providers\TwinfieldBookingServiceProvider::class,
```

In case you need to tweak things, you can publish the config file.

```bash
php artisan vendor:publish --provider="Qlic\Twinfield\Booking\Providers\TwinfieldBookingServiceProvider"
```

## Usage

Typehint `InvoiceBookerContract` and use it's methods.

Example:

```php
<?php

use Qlic\Twinfield\Booking\Contracts\InvoiceBookerContract;
use Qlic\Twinfield\Booking\Contracts\InvoiceContract;
// use your Invoice class here

class ABC
{
    private $invoiceBooker;

    public function __construct(InvoiceBookerContract $invoiceBooker)
    {
        $this->invoiceBooker = $invoiceBooker;
    }

    public function random(Invoice $invoice)
    {
        $this->invoiceBooker->createTransaction(new InvoiceWrapper($invoice));
    }
}

// Write a wrapper implementing the invoice contract
class InvoiceWrapper implements InvoiceContract
{
    private $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    // Implement all methods here
}
```