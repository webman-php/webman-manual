# 支払いSDK（V3）


## プロジェクトアドレス
https://github.com/yansongda/pay

## インストール

```php
composer require yansongda/pay ^3.0.0
```

## 使用法

> 注：以下はアリペイのサンドボックス環境を使用したドキュメント作成です。問題がある場合は、お知らせください。

## 設定ファイル

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
            // 必須-アリペイが割り当てた app_id
            'app_id' => '20160909004708941',
            // 必須-アプリケーションプライベートキー　文字列またはパス
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // 必須-アプリケーション公開証明書のパス
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // 必須-アリペイの公開証明書のパス
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // 必須-アリペイのルート証明書のパス
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // オプション-同期コールバックアドレス
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // オプション-非同期コールバックアドレス
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // オプション-サービスプロバイダモードのサービスプロバイダid、モードが Pay::MODE_SERVICE の場合に使用される
            'service_provider_id' => '',
            // オプション-デフォルトは通常モード。MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE から選択可能
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // 必須-商戸番号、サービスプロバイダモードであればサービスプロバイダの商戸番号
            'mch_id' => '',
            // 必須-商戸秘密鍵
            'mch_secret_key' => '',
            // 必須-商戸プライベートキー 文字列またはパス
            'mch_secret_cert' => '',
            // 必須-商戸公開証明書のパス
            'mch_public_cert_path' => '',
            // 必須
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // オプション-公衆号の app_id
            'mp_app_id' => '2016082000291234',
            // オプション-ミニプログラムの app_id
            'mini_app_id' => '',
            // オプション-app の app_id
            'app_id' => '',
            // オプション-合併 app_id
            'combine_app_id' => '',
            // オプション-合併商戸番号
            'combine_mch_id' => '',
            // オプション-サービスプロバイダモードで、サブ公衆号の app_id
            'sub_mp_app_id' => '',
            // オプション-サービスプロバイダモードで、サブ app の app_id
            'sub_app_id' => '',
            // オプション-サービスプロバイダモードで、サブミニプログラムの app_id
            'sub_mini_app_id' => '',
            // オプション-サービスプロバイダモードで、サブ商戸id
            'sub_mch_id' => '',
            // オプション-微信公開証明書のパス, optional, php-fpm モードでこのパラメータを設定することを強くお勧めします
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // オプション-デフォルトは通常モード。MODE_NORMAL, MODE_SERVICE から選択可能
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // 本番環境では、info 推奨, 開発環境では debug 推奨
        'type' => 'single', // optional, daily 可選
        'max_file' => 30, // optional, daily の場合のデフォルト 30 日
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 追加の構成項目は [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) を参照
    ],
    '_force' => true,
];
```
> 注意：証明書ディレクトリは規定されていません。上記の例はフレームワークの `payment` ディレクトリに配置されているものです。

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## 初期化

直接 `config` メソッドを呼び出して初期化します。
```php
// 設定ファイル config/payment.php を取得
$config = Config::get('payment');
Pay::config($config);
```
> 注意：アリペイのサンドボックスモードの場合、設定ファイルで `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` を有効にする必要があります。このオプションはデフォルトで通常モードになっています。

## 支払い（ウェブ）

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
    // 1. 設定ファイル config/payment.php を取得
    $config = Config::get('payment');

    // 2. 設定の初期化
    Pay::config($config);

    // 3. ウェブ支払い
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // get 方法を使用してリダイレクト
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## 非同期コールバック

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: 『アリペイ』非同期通知
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. 設定ファイル config/payment.php を取得
    $config = Config::get('payment');

    // 2. 設定の初期化
    Pay::config($config);

    // 3. アリペイのコールバック処理
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // trade_status を判断し、その他のロジックを実行してください。トランザクション通知の状態が TRADE_SUCCESS または TRADE_FINISHED のみ、アリペイは購入者が支払い成功と見なします。
    // 1. 商戸は通知データの out_trade_no が商戸システムで作成された注文番号かどうかを確認する必要があります；
    // 2. total_amount が注文の実際の金額（商戸の注文作成時の金額）かを確認する必要があります；
    // 3. 通知中の seller_id（または seller_email） が out_trade_no の対応操作側であることを確認する必要があります；
    // 4. app_id が商戸自身であるかを確認する必要があります；
    // 5. その他のビジネスロジック
    // ===================================================================================================

    // 5. アリペイのコールバック処理
    return new Response(200, [], 'success');
}
```
> 注意：プラグインの `return Pay::alipay()->success();` でアリペイのコールバックに応答することはできません。ミドルウェアを使用すると問題が発生する可能性があります。アリペイに対する応答には webman の応答クラス `support\Response;` を使用する必要があります。
## 同期コールバック

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
## 完全なコード例

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## その他の情報

公式ドキュメントにアクセスしてください。https://pay.yansongda.cn/docs/v3/
