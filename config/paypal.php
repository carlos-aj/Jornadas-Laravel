<?php

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
    'sandbox' => [
        'client_id'         => env('PAYPAL_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_SECRET', ''),
        'app_id'            => '',
    ],
    'live' => [
        'client_id'         => env('PAYPAL_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_SECRET', ''),
        'app_id'            => '',
    ],

    'payment_action' => 'Sale', // Can only be 'Sale', 'Authorization' or 'Order'
    'currency'       => 'EUR',
    'notify_url'     => '', // Change this accordingly for your application.
    'locale'         => '', // PayPal supported locale
    'validate_ssl'   => true, // Validate SSL when creating api client.
];
