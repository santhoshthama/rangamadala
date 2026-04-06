<?php

class PayHereHelper
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/payhere.config.php';
    }

    public function generateHash($orderId, $amount, $currency = 'LKR')
    {
        $merchantId = $this->config['merchant_id'] ?? '';
        $merchantSecret = $this->config['merchant_secret'] ?? '';

        return strtoupper(md5(
            $merchantId .
            $orderId .
            number_format((float)$amount, 2, '.', '') .
            $currency .
            strtoupper(md5($merchantSecret))
        ));
    }

    public function verifyPaymentNotification($merchantId, $orderId, $payhereAmount, $payhereCurrency, $statusCode, $md5sig)
    {
        $merchantSecret = $this->config['merchant_secret'] ?? '';
        $localMd5Sig = strtoupper(md5(
            $merchantId .
            $orderId .
            $payhereAmount .
            $payhereCurrency .
            $statusCode .
            strtoupper(md5($merchantSecret))
        ));

        return $localMd5Sig === $md5sig;
    }

    public function getStatusMessage($statusCode)
    {
        return $this->config['status_codes'][$statusCode] ?? 'unknown';
    }

    public function getConfig($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
