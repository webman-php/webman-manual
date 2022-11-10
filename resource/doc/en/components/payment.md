# PaymentSDK（V3）


## Project address

 https://github.com/yansongda/pay

## Install

```php
composer require yansongda/pay:~3.1.0 -vvv
```

## Usage 

> Note: the following to paypal sandbox environment for the environment for the documentation, if there are problems, please feedback Oh！

### configuration file

The following configuration file is assumed to be available `config/payment.php`

```php
<?php
/**
 * @desc Payment Profile
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Required - Assigned by paypal app_id
            'app_id' => '20160909004708941',
            // Required-application-private-key string or path
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Required - Application public key certificate path
            'app_public_cert_path' => public_path().'/appCertPublicKey_2016090900470841.crt',
            // Required - Paypal public key certificate path
            'alipay_public_cert_path' => public_path().'/alipayCertPublicKey_RSA2.crt',
            // Required - Paypal root certificate path
            'alipay_root_cert_path' => public_path().'/alipayRootCert.crt',
            // Optional - Sync Callback Address
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Optional - Asynchronous callback address
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Optional - service provider id in service provider mode, use this parameter when mode is Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Optional - Default is normal mode. Optional： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Required - merchant number, service provider merchant number in service provider mode
            'mch_id' => '',
            // Required - Merchant Secret Key
            'mch_secret_key' => '',
            // Required - Merchant private key string or path
            'mch_secret_cert' => '',
            // Required - Merchant public key certificate path
            'mch_public_cert_path' => '',
            // Required
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Optional - Public app_id
            'mp_app_id' => '2016082000291234',
            // Optional - applet app_id
            'mini_app_id' => '',
            // Optional-app's app_id
            'app_id' => '',
            // Optional - Combined Order app_id
            'combine_app_id' => '',
            // optional-combined-order-merchant-number
            'combine_mch_id' => '',
            // Optional-Service Provider mode, sub-public app_id
            'sub_mp_app_id' => '',
            // Optional - in service provider mode, the child app's app_id
            'sub_app_id' => '',
            // Optional - Service provider mode for sub-applications app_id
            'sub_mini_app_id' => '',
            // optional-sub-merchant in service provider modeid
            'sub_mch_id' => '',
            // Optional - path to microsoft public key certificate, optional, highly recommended in php-fpm mode
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Optional - Default is normal mode. Optional： MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Recommend adjusting the production environment level to info and the development environment to  debug
        'type' => 'single', // optional, Optional daily.
        'max_file' => 30, // optional, Valid when type is daily, default 30 days
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // For more configuration items, please refer to  [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ]
];
```
> Note: All payment certificates are placed in the `public` directory of the framework

```php
├── public
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Initialization

Directly call `config` method to initialize
```php
// Get configuration file config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> note：if it isPaymentRequest access to，but the code is more compactconfiguration file `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`，This option defaults to normal mode by default。

### Payment (web page）

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @param Request $request
 * @return string
 */
public function payment(Request $request)
{
    // 1. Get configuration file config/payment.php
    $config = Config::get('payment');

    // 2. Initialization Configuration
    Pay::config($config);

    // 3. Web Payment
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // Jump with get
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Callback

#### Asynchronous callbacks

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc:『Paypal asynchronous notification
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Get configuration file config/payment.php
    $config = Config::get('payment');

    // 2. Initialization Configuration
    Pay::config($config);

    // 3. Paypal callback processing
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Please make your own pair trade_status make judgments and other logic to make judgments，Only transaction notification status is TRADE_SUCCESS 或 TRADE_FINISHED 时，PaymentPo will only recognize the payment as successful for the buyer。
    // 1、The merchant needs to verify that the out_trade_no in this notification data is the order number created in the merchant's system；
    // 2、Determine if total_amount is indeed the actual amount of the order (i.e. the amount of the merchant order at the time of creation)）；
    // 3、Verify that the seller_id (or seller_email) in the notification is the corresponding operator for the out_trade_no document；
    // 4、Verify that the app_id is the merchant itself。
    // 5、Other business logic cases
    // ===================================================================================================

    // 5. Paypal callback processing
    return new Response(200, [], 'success');
}
```
> note：cannotUsagethe plugin itself `return Pay::alipay()->success();`responsePayment宝Callback，If you use middleware there will be middleware issues。soResponsePaymentPo needsUsagewebmanresponse class `support\Response;`

#### Synchronous callbacks

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『Paypal sync notification
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『Paypal sync notification'.json_encode($request->get()));
    return 'success';
}
```
## Full code of the case

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## More content
 
Access official documentation https://pay.yansongda.cn/docs/v3/
