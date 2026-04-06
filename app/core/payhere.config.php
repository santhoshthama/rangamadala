<?php

return [
    'merchant_id' => 'YOUR_MERCHANT_ID',
    'merchant_secret' => 'YOUR_MERCHANT_SECRET',
    'sandbox' => true,
    'return_url' => ROOT . '/Payment/paymentReturn',
    'cancel_url' => ROOT . '/Production_manager/manage_services',
    'notify_url' => ROOT . '/Payment/notify',
    'currency' => 'LKR',
    'status_codes' => [
        2 => 'success',
        0 => 'pending',
        -1 => 'canceled',
        -2 => 'failed',
        -3 => 'chargedback'
    ]
];
