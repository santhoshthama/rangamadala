<?php
/**
 * PayHere Payment Gateway Helper
 * 
 * Handles hash generation and payment verification
 */

class PayHereHelper
{
    private $config;
    
    public function __construct()
    {
        $this->config = require(__DIR__ . '/payhere.config.php');
    }
    
    /**
     * Generate hash for payment request
     * 
     * hash = MD5(merchant_id + order_id + amount + currency + MD5(merchant_secret))
     */
    public function generateHash($order_id, $amount, $currency = 'LKR')
    {
        $merchant_id = $this->config['merchant_id'];
        $merchant_secret = $this->config['merchant_secret'];
        
        $hash = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                number_format($amount, 2, '.', '') . 
                $currency .  
                strtoupper(md5($merchant_secret))
            )
        );
        
        return $hash;
    }
    
    /**
     * Verify webhook signature from PayHere
     * 
     * Verify the md5sig sent by PayHere matches our locally generated signature
     */
    public function verifyPaymentNotification($merchant_id, $order_id, $payhere_amount, $payhere_currency, $status_code, $md5sig)
    {
        $merchant_secret = $this->config['merchant_secret'];
        
        $local_md5sig = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                $payhere_amount . 
                $payhere_currency . 
                $status_code . 
                strtoupper(md5($merchant_secret))
            )
        );
        
        return $local_md5sig === $md5sig;
    }
    
    /**
     * Get payment status message
     */
    public function getStatusMessage($status_code)
    {
        return $this->config['status_codes'][$status_code] ?? 'unknown';
    }
    
    /**
     * Get config value
     */
    public function getConfig($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
