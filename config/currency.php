<?php

return [

    /*
    |--------------------------------------------------------------------------
    | This configuration file is related to currency
    |--------------------------------------------------------------------------
    |
    | Here you can define how the currency should be presented to the application.
    |
    */

    'symbol' => env('CURRENCY_SYMBOL', '$'),
    'decimal' => env('CURRENCY_DECIMAL', 2),
    'decimal_separator' => env('CURRENCY_DECIMAL_SEPARATOR', '.'),
    'thousand_separator' => env('CURRENCY_THOUSAND_SEPARATOR', ','),
    'is_symbol_prefixed' => env('CURRENCY_IS_SYMBOL_PREFIXED', true)
];
