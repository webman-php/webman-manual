# 支付SDK（V3）


## 项目地址

 https://github.com/yansongda/pay

## 安装

```php
composer require yansongda/pay ^3.0.0
```

## 使用 

> 说明：以下以支付宝沙箱环境为环境进行文档编写，若有问题，请及时反馈哦！

## 配置文件

假设有以下配置文件 `config/payment.php`

```php
<?php
/**
 * @desc 支付配置文件
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    '_force' => true, // 注意，这个必须是true
    'alipay' => [
        'default' => [
            // 必填-支付宝分配的 app_id
            'app_id' => '20160909004708941',
            // 必填-应用私钥 字符串或路径
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // 必填-应用公钥证书 路径
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // 必填-支付宝公钥证书 路径
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // 必填-支付宝根证书 路径
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // 选填-同步回调地址
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // 选填-异步回调地址
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => '',
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // 必填-商户号，服务商模式下为服务商商户号
            'mch_id' => '',
            // 必填-商户秘钥
            'mch_secret_key' => '',
            // 必填-商户私钥 字符串或路径
            'mch_secret_cert' => '',
            // 必填-商户公钥证书路径
            'mch_public_cert_path' => '',
            // 必填
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // 选填-公众号 的 app_id
            'mp_app_id' => '2016082000291234',
            // 选填-小程序 的 app_id
            'mini_app_id' => '',
            // 选填-app 的 app_id
            'app_id' => '',
            // 选填-合单 app_id
            'combine_app_id' => '',
            // 选填-合单商户号
            'combine_mch_id' => '',
            // 选填-服务商模式下，子公众号 的 app_id
            'sub_mp_app_id' => '',
            // 选填-服务商模式下，子 app 的 app_id
            'sub_app_id' => '',
            // 选填-服务商模式下，子小程序 的 app_id
            'sub_mini_app_id' => '',
            // 选填-服务商模式下，子商户id
            'sub_mch_id' => '',
            // 选填-微信公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ]
];
```
> 注意：证书目录没有规定，以上示例是放在的框架的 `payment`目录下

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## 初始化

直接调用 `config` 方法初始化
```php
// 获取配置文件 config/payment.php
$config = config('payment');
Pay::config($config);
```
> 注意：如果是支付宝沙箱模式，一定要记得开启配置文件 `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`，该选项默认为默认为正常模式。

## 支付（网页）

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @param Request $request
 * @return string
 */
public function payment(Request $request)
{
    // 1. 初始化配置
    Pay::config(config('payment'));

    // 2. 网页支付
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // 使用get方式跳转
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## 异步回调

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc:『支付宝』异步通知
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. 初始化配置
    Pay::config(config('payment'));

    // 2. 支付宝回调处理
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // 请自行对 trade_status 进行判断及其它逻辑进行判断，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
    // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
    // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
    // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方；
    // 4、验证app_id是否为该商户本身。
    // 5、其它业务逻辑情况
    // ===================================================================================================

    // 5. 支付宝回调处理
    return new Response(200, [], 'success');
}
```
> 注意：不能使用插件本身 `return Pay::alipay()->success();`响应支付宝回调，如果你用到中间件会出现中间件问题。所以响应支付宝需要使用webman的响应类 `support\Response;`

## 同步回调

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『支付宝』同步通知
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『支付宝』同步通知'.json_encode($request->get()));
    return 'success';
}
```

## 更多内容
 
访问官方文档 https://pay.yansongda.cn/docs/v3/
