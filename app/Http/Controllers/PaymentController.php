<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal;

class PaymentController extends Controller
{
    private PayPal $paypal;

    public function __construct(PayPal $paypal)
    {
        $this->paypal = $paypal;
        $this->paypal->setApiCredentials($this->buildPaypalConfig(config('paypal')));
        $this->paypal->setAccessToken($this->paypal->getAccessToken());
    }

    public function createOrder(Request $request)
    {
        $paypalOrder = $this->paypal->createOrder([
            'intent'=> 'CAPTURE',
            'application_context' => [
                'brand_name' => config('app.name'),
                'landing_page' => 'BILLING',
                "user_action" => "PAY_NOW",
            ],
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => '100.00'
                    ],
                ],
            ],
        ]);

        // Thêm logic lưu thông tin order vào DB
        return $paypalOrder;
    }

    public function captureOrder($orderId)
    {
        $capturedOrder = $this->paypal->capturePaymentOrder($orderId);

        // Thêm logic cập nhập thông tin order vào DB

        return $capturedOrder;
    }

    public function createSubscription()
    {
        $subscription =  $this->paypal->createSubscription([
            "intent"=> "SUBSCRIPTION",
            "plan_id" => 'P-54D952024E7319535MB2GZOI', // Thay Plan ID của bạn vào đây
            'application_context' => [
                'brand_name' => config('app.name'),
                'landing_page' => 'BILLING',
                "user_action" => "SUBSCRIBE_NOW",
                "payment_method" => [
                    "payer_selected" => "PAYPAL",
                    "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED"
                ],
            ],
        ]);

        // Thêm logic lưu thông tin subscription vào DB

        return $subscription;
    }

    public function captureSubscription($subscriptionId)
    {
        $subscription = $this->paypal->showSubscriptionDetails($subscriptionId);

        // Thêm logic update thông tin subscription vào DB

        return $subscription;
    }

    public function buildPaypalConfig($config)
    {
        return [
            'mode' => data_get($config, 'mode'),
            'sandbox' => [
                'client_id' => data_get($config, 'client_id'),
                'client_secret' => data_get($config, 'client_secret'),
                'app_id' => data_get($config, 'app_id'),
            ],
            'live' => [
                'client_id' => data_get($config, 'client_id'),
                'client_secret' => data_get($config, 'client_secret'),
                'app_id' => data_get($config, 'app_id'),
            ],

            'payment_action' => data_get($config, 'payment_action'),
            'currency' => data_get($config, 'currency'),
            'notify_url' => data_get($config, 'notify_url'),
            'locale' => data_get($config, 'locale'),
            'validate_ssl' => data_get($config, 'validate_ssl'),
            'webhook_id' => data_get($config, 'webhook_id'),
        ];
    }
}
