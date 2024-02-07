# イベント処理
`webman/event`は、ビジネスロジックを侵害することなく、いくつかのビジネスロジックを実行するための洗練されたイベントメカニズムを提供し、ビジネスモジュール間の結合を実現します。典型的なシナリオとして、新しいユーザーが登録されたとき、`user.register`というカスタムイベントを発行するだけで、各モジュールがそのイベントを受信し、対応するビジネスロジックを実行できます。

## インストール
`composer require webman/event`

## イベントの購読
イベントの購読は、統一的に`config/event.php`ファイルで設定されます。

```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...その他のイベント処理関数...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...その他のイベント処理関数...
    ]
];
```
**説明：**
- `user.register`、`user.logout`などは、イベント名であり、文字列型で、小文字の単語をドット(`.`)で区切ることをお勧めします。
- 1つのイベントに複数のイベント処理関数を対応させることができます。呼び出し順序は設定の順序です。

## イベント処理関数
イベント処理関数は、任意のクラスメソッド、関数、クロージャ関数などであることができます。例えば、`app/event/User.php`というイベント処理クラスを作成します（ディレクトリが存在しない場合は、手動で作成してください）。

```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## イベントの発行
`Event::emit($event_name, $data);`を使用してイベントを発行します。例えば

```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```

> **ヒント**
> `Event::emit($event_name, $data);`の`$data`パラメータは、配列、クラスインスタンス、文字列など、任意のデータが使用できます。

## ワイルドカードイベントリスナー
ワイルドカード登録リスナーを使用すると、同じリスナーで複数のイベントを処理できます。例えば、`config/event.php`で次のように設定します。

```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
イベント処理関数の2番目のパラメータ`$event_data`を使用して、具体的なイベント名を取得できます。

```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // 具体的なイベント名、例：user.register user.logout など
        var_export($user);
    }
}
```

## イベントブロードキャストを停止
イベント処理関数で`false`を返すと、そのイベントはブロードキャストが停止します。

## クロージャ関数でのイベント処理
イベント処理関数はクラスメソッドであっても、クロージャ関数であってもかまいません。例えば：

```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

## イベントおよびリスナーの表示
`php webman event:list`コマンドを使用して、プロジェクトで設定された全てのイベントおよびリスナーを表示します。

## 注意事項
eventイベント処理は非同期ではなく、遅延ビジネスを処理するのには適していません。遅延ビジネスはメッセージキューを使用するべきです。例えば[webman/redis-queue](https://www.workerman.net/plugin/12)
