<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client info
    |--------------------------------------------------------------------------
    |
    | Here you may specify the config for the connection to Twinfield. These
    | settings can be set and found on:
    |
    | https://developers.twinfield.com/clients
    |
    */

    'client_id' => env('TWINFIELD_CLIENT_ID'),

    'client_secret' => env('TWINFIELD_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URL
    |--------------------------------------------------------------------------
    |
    | The supplied config here is requested
    |
    */

    'twinfield.redirect_url' => env('TWINFIELD_REDIRECT_URL'),

    'refresh_token' => env('TWINFIELD_REFRESH_TOKEN'),

    /**
     * Twinfield organisation
     */
    'organization' => env('TWINFIELD_ENVIRONMENT', 'nope'),

    /**
     * Twinfield office
     */
    'office' => env('TWINFIELD_ADMINISTRATION'),

    /**
     * Currency to invoice people with
     * Needs to be compatible with Money\Currency
     */
    'currency' => env('TWINFIELD_CURRENCY', 'EUR'),

    /**
     * Prefixes used for codes in twinfield
     */
    'prefixes' => [
        /**
         * Customer prefix
         * Used when creating customers
         */
        'customer' => '1520',

        /**
         * Supplier prefix
         */
        'supplier' => '1620',
    ],

    /**
     * Config for transactions
     */
    'transactions' => [
        /**
         * Sales transaction specific values
         */
        'sales' => [
            /**
             * The day book which will be used for transactions
             * Example: BNK (for bank)
             */
            'day_book' => 'VRK',
        ],

        /**
         * Purchase transaction specific values
         */
        'purchase' => [
            /**
             * The day book which will be used for transactions
             * Example: BNK (for bank)
             */
            'day_book' => 'INK',
        ]
    ],
];
