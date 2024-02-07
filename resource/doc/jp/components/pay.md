# 支払いSDK


### プロジェクトの場所

https://github.com/yansongda/pay

### インストール

```php
composer require yansongda/pay -vvv
```

### 使用方法

**アリペイ**

```php
<?php
namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'app_id' => '2016082000295641',
        'notify_url' => 'http://yansongda.cn/notify.php',
        'return_url' => 'http://yansongda.cn/return.php',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuWJKrQ6SWvS6niI+4vEVZiYfjkCfLQfoFI2nCp9ZLDS42QtiL4Ccyx8scgc3nhVwmVRte8f57TFvGhvJD0upT4O5O/lRxmTjechXAorirVdAODpOu0mFfQV9y/T9o9hHnU+VmO5spoVb3umqpq6D/Pt8p25Yk852/w01VTIczrXC4QlrbOEe3sr1E9auoC7rgYjjCO6lZUIDjX/oBmNXZxhRDrYx4Yf5X7y8FRBFvygIE2FgxV4Yw+SL3QAa2m5MLcbusJpxOml9YVQfP8iSurx41PvvXUMo49JG3BDVernaCYXQCoUJv9fJwbnfZd7J5YByC+5KM4sblJTq7bXZWQIDAQAB',
        // 暗号化方式： **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // 公開鍵証明書モードを使用する場合は、以下の2つのパラメーターを設定し、ali_public_keyを.crt拡張子のアリペイの公開鍵証明書パスに変更してください（例：（./cert/alipayCertPublicKey_RSA2.crt）
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //アプリケーション公開鍵証明書パス
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', //アリペイルート証明書パス
        'log' => [ // オプション
            'file' => './logs/alipay.log',
            'level' => 'info', // 本番環境ではレベルを info に変更することをお勧めします。開発環境ではデバッグにします
            'type' => 'single', // optional, dailyを選択できます
            'max_file' => 30, // optional, dailyの場合有効、デフォルトは30日です
        ],
        'http' => [ // オプション
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 他の設定項目については [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) を参照してください
        ],
        'mode' => 'dev', // オプション、このパラメータを設定すると、サンドボックスモードに入ります
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - テスト',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// laravel フレームワークでは、`return $alipay`を直接使用してください
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // はい、これほど簡単に検証します！

        // 注文番号：$data->out_trade_no
        // アリペイ取引番号：$data->trade_no
        // 注文総額：$data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // はい、これほど簡単に検証します！

            // 商人は通知データのout_trade_noが商人システムで作成した注文番号かどうかを確認する必要があります。
            // total_amountは、その注文の実際の金額であるかどうかを判断する必要があります（つまり、商人注文作成時の金額）。
            // 通知のseller_id（またはseller_email)がout_trade_noこの伝票の対応する運用者であることを確認してください（商人には複数のseller_id/seller_emailがある場合があります）。
            // app_idが商人自身であることを確認してください。
            // 他の業務ロジックの状況
            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// laravel フレームワークでは、`return $alipay->success()`を直接使用してください
    }
}
```

**WeChat**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // パブリック号 APPID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // ミニアプリ APPID
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // optional，返金などで使用します
        'cert_key' => './cert/apiclient_key.pem',// optional，返金などで使用します
        'log' => [ // optional
            'file' => './logs/wechat.log',
            'level' => 'info', // 本番環境ではレベルを info に変更することをお勧めします。開発環境ではデバッグにします
            'type' => 'single', // optional, dailyを選択できます
            'max_file' => 30, // optional, dailyの場合有効、デフォルトは30日です
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 他の設定項目については [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) を参照してください
        ],
        'mode' => 'dev', // optional, dev/hk; 'hk' の場合は香港ゲートウェイになります。
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **単位：分**
            'body' => 'test body - テスト',
            'openid' => 'onkVf1FjWS5SBIixxxxxxx',
        ];

        $pay = Pay::wechat($this->config)->mp($order);

        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // はい、これほど簡単に検証します！

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// laravel フレームワークでは、`return $pay->success()`を直接使用してください
    }
}
```

### その他のコンテンツ

https://pay.yanda.net.cn/docs/2.x/overview を訪問してください
