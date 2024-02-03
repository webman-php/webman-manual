# 支払いSDK（V3）

## プロジェクトアドレス

https://github.com/yansongda/pay

## インストール

```php
composer require yansongda/pay ^3.0.0
```

## 使用法

> 注：以下は、ドキュメントの作成時にアリペイサンドボックス環境を想定しています。何か問題があれば、すぐにフィードバックしてください！

### 設定ファイル

次のような設定ファイル `config/payment.php` があると仮定します。

```php
<?php
/**
 * @desc 支払い設定ファイル
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // 必填-アリペイ割り当ての app_id
            'app_id' => '20160909004708941',
            // 必須-アプリケーション秘密鍵文字列またはパス
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // 必須-アプリケーション公開鍵証明書パス
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // 必須-アリペイ公開鍵証明書パス
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // 必須-アリペイルート証明書パス
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // 任意-同期コールバックアドレス
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // 任意-非同期コールバックアドレス
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // 任意-サービスプロバイダモードのサービスプロバイダID、mode が Pay::MODE_SERVICE の場合にこのパラメタを使用します。
            'service_provider_id' => '',
            // 任意-デフォルトは通常モードです。MODE_NORMAL、MODE_SANDBOX、MODE_SERVICE のいずれかに選択できます。
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // 必須-商人番号、サービスプロバイダモードではサービスプロバイダ商人番号です。
            'mch_id' => '',
            // 必須-商人秘密鍵
            'mch_secret_key' => '',
            // 必須-商人秘密証明書文字列またはパス
            'mch_secret_cert' => '',
            // 必須-商人公開証明書パス
            'mch_public_cert_path' => '',
            // 必須
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // 任意-公衆の app_id
            'mp_app_id' => '2016082000291234',
            // 任意-小プログラムの app_id
            'mini_app_id' => '',
            // 任意-app の app_id
            'app_id' => '',
            // 任意-合計 app_id
            'combine_app_id' => '',
            // 任意-合計商人番号
            'combine_mch_id' => '',
            // 任意-サービスプロバイダモードでは、サブ公衆の app_id
            'sub_mp_app_id' => '',
            // 任意-サービスプロバイダモードでは、サブ app の app_id
            'sub_app_id' => '',
            // 任意-サービスプロバイダモードでは、サブ小プログラムの app_id
            'sub_mini_app_id' => '',
            // 任意-サービスプロバイダモードでは、サブ商人ID
            'sub_mch_id' => '',
            // 任意-ウィーチャット公開証明書パス、オプションですが、PHP-FPMモードで設定することを強くお勧めします。
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // 任意-デフォルトは通常モードです。MODE_NORMAL、MODE_SERVICE のいずれかに選択できます。
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
        // より多くの構成項目については [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) を参照してください。
    ],
    '_force' => true,
];
```
> 注意：証明書のディレクトリは規定されていません。上記の例では、フレームワークの `payment` ディレクトリに配置されています。

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### 初期化

直接 `config` メソッドを呼び出して初期化します。
```php
// 設定ファイル config/payment.php を取得する
$config = Config::get('payment');
Pay::config($config);
```
> 注意：アリペイサンドボックスモードの場合、必ず設定ファイルに `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` を有効にする必要があります。この選択肢はデフォルトで通常モードになっています。

### 支払い（ウェブ）

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
    // 1. 設定ファイル config/payment.php を取得する
    $config = Config::get('payment');

    // 2. 設定の初期化
    Pay::config($config);

    // 3. ウェブ支払い
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // 方法を取得する
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### コールバック

#### 非同期コールバック

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc:『アリペイ』非同期通知
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. 設定ファイル config/payment.php を取得する
    $config = Config::get('payment');

    // 2. 設定の初期化
    Pay::config($config);

    // 3. アリペイコールバック処理
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // trade_status の判断とその他のロジックを自分で行う必要があります。取引通知の状態が TRADE_SUCCESS または TRADE_FINISHED の場合にのみ、アリペイが購入者の支払いを成功と見なします。以下のロジックを実行する必要があります。
    // 1、売り手は通知データ内のout_trade_noが売り手システムで作成された注文番号かどうかを検証する必要があります。
    // 2、total_amountが注文の実際の金額（売り手注文作成時の金額）と等しいかどうかを判断します。
    // 3、通知のseller_id（またはseller_email）がout_trade_noのデータの対応する操作者であるかどうかを確認します。
    // 4、app_idが売り手自身のものであるかどうかを確認します。
    // 5、その他のビジネスロジック
    // ===================================================================================================

    // 5. アリペイコールバック処理
    return new Response(200, [], 'success');
}
```
> 注意：プラグインの `return Pay::alipay()->success();` を使用してアリペイコールバックに応答することはできません。ミドルウェアを使用している場合、問題が発生する可能性があります。そのため、アリペイへの応答にはwebmanのレスポンスクラス `support\Response` を使用する必要があります。

#### 同期コールバック

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『アリペイ』同期通知
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『アリペイ』同期通知'.json_encode($request->get()));
    return 'success';
}
```

## 完全な例のコード

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## その他の内容

公式ドキュメントを参照してください https://pay.yansongda.cn/docs/v3/
