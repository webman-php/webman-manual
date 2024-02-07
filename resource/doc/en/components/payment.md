# Payment SDK (V3)

## Project Repository

https://github.com/yansongda/pay

## Installation

```php
composer require yansongda/pay ^3.0.0
```

## Usage 

> Note: The following documentation is written based on the Alipay sandbox environment. If there are any issues, please provide timely feedback!

### Configuration File

Assuming there is the following configuration file `config/payment.php`

```php
<?php
/**
 * @desc Payment Configuration File
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Required - Alipay assigned app_id
            'app_id' => '20160909004708941',
            // Required - Application private key (string or path)
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Required - Application public key certificate (path)
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Required - Alipay public key certificate (path)
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Required - Alipay root certificate (path)
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Optional - Synchronous callback address
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Optional - Asynchronous callback address
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Optional - Service provider id in service provider mode, used when mode is Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Optional - Default is normal mode. Options are: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Required - Merchant number, service provider mode is service provider merchant number
            'mch_id' => '',
            // Required - Merchant secret key
            'mch_secret_key' => '',
            // Required - Merchant private key (string or path)
            'mch_secret_cert' => '',
            // Required - Merchant public key certificate path
            'mch_public_cert_path' => '',
            // Required
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Optional - app_id of the public account
            'mp_app_id' => '2016082000291234',
            // Optional - app_id of the mini program
            'mini_app_id' => '',
            // Optional - app_id of the app
            'app_id' => '',
            // Optional - combine app_id
            'combine_app_id' => '',
            // Optional - combine merchant number
            'combine_mch_id' => '',
            // Optional - app_id of the sub public account in service provider mode
            'sub_mp_app_id' => '',
            // Optional - app_id of the sub app in service provider mode
            'sub_app_id' => '',
            // Optional - app_id of the sub mini program in service provider mode
            'sub_mini_app_id' => '',
            // Optional - sub merchant id in service provider mode
            'sub_mch_id' => '',
            // Optional - WeChat public key certificate path, optional. Strongly recommend configuring this parameter in php-fpm mode
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Optional - Default is normal mode. Options are: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Suggest adjusting to 'info' in production environment, and 'debug' in development environment
        'type' => 'single', // Optional, available options are daily.
        'max_file' => 30, // Optional, valid when type is daily, default is 30 days
    ],
    'http' => [ // Optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // For more configuration options, please refer to [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```

> Note: The certificate directory is not specified. The above example is placed in the framework's `payment` directory.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Initialization

Initialize by directly calling the `config` method

```php
// Get the configuration file config/payment.php
$config = Config::get('payment');
Pay::config($config);
```

> Note: If you are using the Alipay sandbox mode, remember to enable the configuration option `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, which is set to normal mode by default.

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
        '_method' => 'get' // Use the GET method for redirection
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
 * @desc: Alipay asynchronous notification
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Get the configuration file config/payment.php
    $config = Config::get('payment');

    // 2. Initialize the configuration
    Pay::config($config);

    // 3. Process Alipay callback
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Please judge the trade_status and other logic by yourself. Alipay will only recognize the buyer's successful payment when the transaction notification status is TRADE_SUCCESS or TRADE_FINISHED.
    // 1. The merchant needs to verify whether the out_trade_no in this notification data is the order number created in the merchant system;
    // 2. Determine whether the total_amount is indeed the actual amount of the order (i.e. the amount when the merchant order was created);
    // 3. Verify whether the seller_id (or seller_email) in the notification corresponds to the operator of this order with the out_trade_no;
    // 4. Verify whether app_id is the merchant itself.
    // 5. Other business logic situations
    // ===================================================================================================

    // 5. Process Alipay callback
    return new Response(200, [], 'success');
}
```

> Note: Do not use the plugin's own `return Pay::alipay()->success();` to respond to Alipay callbacks, as it will cause issues with middleware. Therefore, to respond to Alipay, you need to use Webman's response class `support\Response;`

#### Synchronous Callback

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Alipay synchronous notification
 * @param Request $request
 * @return string
 */
public function alipayReturn(Request $request)
{
    Log::info('Alipay synchronous notification'.json_encode($request->get()));
    return 'success';
}
```

## Complete Example Code

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## For More Information

Visit the official documentation at https://pay.yansongda.cn/docs/v3/
