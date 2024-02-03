# Payment SDK (V3)

## Project Address

https://github.com/yansongda/pay

## Installation

```php
composer require yansongda/pay ^3.0.0
```

## Usage

> Note: The documentation is written with the Alipay Sandbox environment as an example. If you encounter any issues, please provide feedback!

### Configuration File

Assuming there is a configuration file `config/payment.php`

```php
<?php
/**
 * @desc Payment configuration file
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Required - Alipay assigns the app_id
            'app_id' => '20160909004708941',
            // Required - Application private key, string or path
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Required - Application public key certificate path
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Required - Alipay public key certificate path
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Required - Alipay root certificate path
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Optional - Synchronous callback address
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Optional - Asynchronous callback address
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Optional - Service provider id in service provider mode, used when mode is Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Optional - Default is normal mode. Can be: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Required - Merchant number, service provider mode is the service provider's merchant number
            'mch_id' => '',
            // Required - Merchant secret key
            'mch_secret_key' => '',
            // Required - Merchant private key, string or path
            'mch_secret_cert' => '',
            // Required - Merchant public key certificate path
            'mch_public_cert_path' => '',
            .
            .
            .
        ]
    ],
    'logger' => [
        .
        .
        .
    ],
    'http' => [ // optional
        .
        .
        .
    ],
    '_force' => true,
];
```
> Note: The certificate directory is not specified. The above example is placed in the 'payment' directory of the framework.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```
### Initialization

Initialize using the `config` method directly
```php
// Get the configuration file config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Note: If it is in Alipay Sandbox mode, be sure to remember to enable the configuration file `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, which is defaulting to normal mode.

### Payment (Web)

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
    // 1. Get the configuration file config/payment.php
    $config = Config::get('payment');

    // 2. Initialize the configuration
    Pay::config($config);

    // 3. Web payment
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // Use the get method to redirect
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Callback

#### Asynchronous Callback

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: 'Alipay' Asynchronous Notification
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Get the configuration file config/payment.php
    $config = Config::get('payment');

    // 2. Initialize the configuration
    Pay::config($config);

    // 3. Alipay callback processing
    $result = Pay::alipay()->callback($request->post());

    // 5. Alipay callback processing
    return new Response(200, [], 'success');
}
```
> Note: Do not use the plugin itself `return Pay::alipay()->success();` to respond to the Alipay callback. If you use middleware, there will be middleware problems. So responding to Alipay requires using the webman response class `support\Response;`

#### Synchronous Callback

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 'Alipay' Synchronous Notification
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info(''Alipay' Synchronous Notification'.json_encode($request->get()));
    return 'success';
}
```
## Complete Case Code

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## More Content

Visit the official documentation https://pay.yansongda.cn/docs/v3/