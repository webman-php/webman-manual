# セッション管理

## 例
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

`$request->session();`を使用して、`Workerman\Protocols\Http\Session`インスタンスを取得し、インスタンスのメソッドを使用してセッションデータを追加、変更、削除します。

> 注意：セッションオブジェクトが破棄されると、セッションデータが自動的に保存されるため、`$request->session()`が返すオブジェクトをグローバル配列やクラスメンバーに保存して、セッションが保存されないようにしてください。

## すべてのセッションデータを取得する
```php
$session = $request->session();
$all = $session->all();
```
返されるのは配列です。セッションデータがない場合は、空の配列が返されます。


## セッション中の値を取得する
```php
$session = $request->session();
$name = $session->get('name');
```
データが存在しない場合は、nullが返されます。

また、getメソッドに2番目の引数としてデフォルト値を渡すことができます。セッション配列に対応する値が見つからない場合は、デフォルト値が返されます。例：
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## セッションを保存する
特定のデータを保存する場合は、setメソッドを使用します。
```php
$session = $request->session();
$session->set('name', 'tom');
```
setメソッドには返り値がありません。セッションオブジェクトが破棄されると、セッションが自動的に保存されます。

複数の値を保存する場合は、putメソッドを使用します。
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
同様に、putにも返り値はありません。

## セッションデータを削除する
特定のあるいは複数のセッションデータを削除する場合は、`forget`メソッドを使用します。
```php
$session = $request->session();
// 1つ削除
$session->forget('name');
// 複数削除
$session->forget(['name', 'age']);
```

また、deleteメソッドも提供されており、`forget`メソッドとの違いは、deleteは1つだけを削除することができることです。
```php
$session = $request->session();
// $session->forget('name'); と同等
$session->delete('name');
```

## セッションデータを取得して削除する
```php
$session = $request->session();
$name = $session->pull('name');
```
以下のコードと同じ効果があります。
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
該当するセッションが存在しない場合は、nullが返されます。

## すべてのセッションデータを削除する
```php
$request->session()->flush();
```
返り値はありません。セッションオブジェクトが破棄されると、セッションが自動的に保存されます。


## 対応するセッションデータが存在するかどうかを判断する
```php
$session = $request->session();
$has = $session->has('name');
```
上記の場合、対応するセッションが存在しない場合や対応するセッション値がnullの場合、falseが返され、それ以外の場合はtrueが返されます。

```php
$session = $request->session();
$has = $session->exists('name');
```
上記のコードもセッションデータが存在するかどうかを判断するためのもので、違いは、対応するセッションの値がnullの場合にもtrueが返されることです。

## ヘルパー関数session()
> 2020-12-09 追加

webmanでは同じ機能を備えたヘルパー関数`session()`が提供されています。
```php
// セッションインスタンスを取得
$session = session();
// 以下は同等
$session = $request->session();

// 特定の値を取得
$value = session('key', 'default');
// 以下と同等
$value = session()->get('key', 'default');
// 以下と同等
$value = $request->session()->get('key', 'default');

// セッションに値を設定
session(['key1'=>'value1', 'key2' => 'value2']);
// 以下と同等
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// 以下と同等
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```


## 設定ファイル
セッションの設定ファイルは`config/session.php`にあり、内容は次のようになります。
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class または RedisSessionHandler::class または RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handlerがFileSessionHandler::classの場合はfile、
    // handlerがRedisSessionHandler::classの場合はredis
    // handlerがRedisClusterSessionHandler::classの場合はredis_cluster、すなわちredisクラスター
    'type'    => 'file',

    // 異なるhandlerに異なる設定を使用する
    'config' => [
        // typeがfileの場合の設定
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // typeがredisの場合の設定
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // セッションIDを保存するクッキーの名前
    
    // === 以下の設定は webman-framework>=1.3.14 workerman>=4.0.37 で利用可能 ===
    'auto_update_timestamp' => false,  // セッションを自動的に更新するかどうか、デフォルトは無効
    'lifetime' => 7*24*60*60,          // セッションの有効期限
    'cookie_lifetime' => 365*24*60*60, // セッションIDを保存するクッキーの有効期限
    'cookie_path' => '/',              // セッションIDを保存するクッキーのパス
    'domain' => '',                    // セッションIDを保存するクッキーのドメイン
    'http_only' => true,               // httpOnlyを有効にするかどうか、デフォルトは有効
    'secure' => false,                 // セッションをhttpsでのみ有効にするかどうか、デフォルトは無効
    'same_site' => '',                 // CSRF攻撃やユーザー追跡を防ぐために使用され、strict/lax/noneを選択できます
    'gc_probability' => [1, 1000],     // セッションをクリーンアップする確率
];
```

> **注意** 
> webman 1.4.0以降では、SessionHandlerの名前空間が変更され、以前の
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> から
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  
> に変更されました。

## 有効期限の設定
webman-framework < 1.3.14 の場合、sessionの有効期限設定は`php.ini`で行う必要があります。

```bash
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

例えば、有効期限を1440秒に設定する場合は、以下のように設定します。
```bash
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **ヒント**
> `php.ini`の位置を見つけるには、`php --ini`コマンドを使用できます。
