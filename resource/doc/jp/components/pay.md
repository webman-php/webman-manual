# 支払いSDK

### プロジェクトのアドレス

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
        'ali_public_key' => 'MIIBIj ...', // アリペイの公開鍵
        // 暗号化方式： **RSA2**
        'private_key' => 'MIIEpAI ...', // プライベートキー
        // 公開鍵書式を使用する場合、次の2つのパラメータを構成し、ali_public_keyを .crtで終わるアリペイの公開鍵証明書のパスに変更してください、例（./cert/alipayCertPublicKey_RSA2.crt）
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // アプリケーションの公開鍵証明書のパス
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // アリペイルート証明書のパス
        'log' => [ // オプション
            'file' => './logs/alipay.log',
            'level' => 'info', // 本番環境の場合はinfo、開発環境の場合はdebugをお勧めします
            'type' => 'single', // オプション、デイリーを選択できます。
            'max_file' => 30, // オプション、タイプがデイリーの場合に有効で、デフォルトは30日です。
        ],
        'http' => [ // オプション
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // その他の設定項目は [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)を参照してください
        ],
        'mode' => 'dev', // オプション、このパラメータを設定すると、サンドボックスモードに進入します
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - テスト',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// Laravelフレームワークの場合は、直接 `return $alipay` を使用してください。
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // はい、検証はこれほど簡単です！

        // 注文番号：$data->out_trade_no
        // アリペイ取引番号：$data->trade_no
        // 注文総額：$data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // はい、検証はこれほど簡単です！

            // トレードステータスを判断し、その他のロジックを判断してください。アリペイのビジネス通知では、トレード通知ステータスがTRADE_SUCCESSまたはTRADE_FINISHEDの場合にのみ、アリペイが購入者の支払いを成功したと認めます
            // 1. 商人は、通知データのout_trade_noが商人システムで作成した注文番号かどうかを検証する必要があります。
            // 2. total_amountが実際の注文の金額（つまり、注文が作成された時の金額）であるかどうかを判断してください。
            // 3. 通知のseller_id（またはseller_email）はout_trade_noのこの注文の関連操作者かどうかを確認します（場合によっては、1つの商人に複数のseller_id/seller_emailがあることがあります）。
            // 4. app_idが商人自身であることを確認してください。
            // 5. その他のビジネスロジック状況
            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// Laravelフレームワークの場合は、直接 `return $alipay->success()` を使用してください。
    }
}
```

**Wechat**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // 公衆号 APPID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小プログラム APPID
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // オプション、返金などの場合に使用
        'cert_key' => './cert/apiclient_key.pem',// オプション、返金などの場合に使用
        'log' => [ // オプション
            'file' => './logs/wechat.log',
            'level' => 'info', // 本番環境の場合はinfo、開発環境の場合はdebugをお勧めします
            'type' => 'single', // オプション、デイリーを選択できます。
            'max_file' => 30, // オプション、タイプがデイリーの場合に有効で、デフォルトは30日です。
        ],
        'http' => [ // オプション
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // その他の設定項目は [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)を参照してください
        ],
        'mode' => 'dev', // オプション、dev/hk;`hk`の場合、香港ゲートウェイになります。
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
            $data = $pay->verify(); // はい、検証はこれほど簡単です！

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// Laravelフレームワークの場合は、直接 `return $pay->success()` を使用してください。
    }
}
```

### その他のコンテンツ

https://pay.yanda.net.cn/docs/2.x/overview を参照してください。
