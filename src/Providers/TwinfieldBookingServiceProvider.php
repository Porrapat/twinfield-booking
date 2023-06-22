<?php

namespace Qlic\Twinfield\Booking\Providers;

use Illuminate\Support\ServiceProvider;
use PhpTwinfield\Secure\Provider\OAuthProvider;
use Qlic\Twinfield\Booking\Contracts\InvoiceBookerContract;
use Qlic\Twinfield\Booking\TwinfieldInvoiceBooker;

class TwinfieldBookingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/twinfield-booking.php' => config_path('twinfield-booking.php'),
        ]);

        $this->app->singleton(InvoiceBookerContract::class, function ($app) {
            return new TwinfieldInvoiceBooker(new OAuthProvider([
                'clientId' => config('twinfield.client_id'),
                'clientSecret' => config('twinfield.client_secret'),
                'redirectUri' =>  config('twinfield.redirect_url'),
            ]));
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
