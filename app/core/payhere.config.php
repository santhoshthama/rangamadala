<?php
/**
 * PayHere Payment Gateway Configuration
 * 
 * IMPORTANT: Replace these values with your actual PayHere credentials
 * Get them from: https://www.payhere.lk/
 */

return [
    // PayHere Merchant Credentials
    'merchant_id' => '1233878',                    // Replace with your Merchant ID
    'merchant_secret' => 'MzQxMzc4MTAxNjEzNDgzNDg1OTcxMDA5NTY2NDAxOTk4NDMzNDEz',          // Replace with your Merchant Secret
    
    // Payment URLs
    'sandbox' => false,                             // Set to false for production
    'return_url' => ROOT . '/Payment/return',      // URL after payment (success/failure)
    'cancel_url' => ROOT . '/Production_manager/manage_services',      // URL when user cancels
    'notify_url' => ROOT . '/Payment/notify',      // Webhook endpoint for payment status
    
    // Default currency
    'currency' => 'LKR',
    
    // Payment status codes from PayHere
    'status_codes' => [
        2 => 'success',
        0 => 'pending',
        -1 => 'canceled',
        -2 => 'failed',
        -3 => 'chargedback'
    ]
];
