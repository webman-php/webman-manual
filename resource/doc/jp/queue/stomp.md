## Stomp キュー

Stompはシンプルな(ストリーム)テキスト指向のメッセージプロトコルであり、相互運用可能な接続形式を提供し、STOMPクライアントが任意のSTOMPメッセージブローカー（Broker）とやり取りできるようにします。[workerman/stomp](https://github.com/walkor/stomp) はStompクライアントを実装し、主にRabbitMQ、Apollo、ActiveMQなどのメッセージキューシーンで使用されます。

## インストール
`composer require webman/stomp`

## 設定
設定ファイルは `config/plugin/webman/stomp` にあります。

## メッセージの送信
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // キュー
        $queue = 'examples';
        // データ（配列を渡す場合は自分でシリアライズする必要があります。例：json_encode、serializeなどを使用）
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // 送信を実行
        Client::send($queue, $data);

        return response('redis queue test');
    }

}
```
> 他のプロジェクトとの互換性を保つため、Stompコンポーネントは自動的なシリアライズおよび逆シリアライズ機能を提供していません。配列データを送信する場合は、自分でシリアライズし、消費時には自分で逆シリアライズする必要があります。

## メッセージの消費
`app/queue/stomp/MyMailSend.php`を新規作成します（クラス名は任意で、PSR-4仕様に従っていればよい）。
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // キュー名
    public $queue = 'examples';

    // 接続名は stomp.php 内の接続に対応します
    public $connection = 'default';

    // 値が client の場合、$ack_resolver->ack() を呼び出してサーバーに正常に消費されたことを通知する必要があります
    // 値が auto の場合、$ack_resolver->ack() を呼び出す必要はありません
    public $ack = 'auto';

    // 消費
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // データが配列の場合は、自分で逆シリアライズする必要があります
        var_export(json_decode($data, true)); // ['to' => 'tom@gmail.com', 'content' => 'hello'] が出力されます
        // サーバーに正常に消費されたことを通知
        $ack_resolver->ack(); // ackが auto の場合、この呼び出しは省略できます
    }
}
```

# rabbitmqでstompプロトコルを有効にする
rabbitmqはデフォルトではstompプロトコルが有効になっていません。以下のコマンドを実行して有効にする必要があります。
```sh
rabbitmq-plugins enable rabbitmq_stomp
```
有効化後、stompのポートはデフォルトで61613になります。
