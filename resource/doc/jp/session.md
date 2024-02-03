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
`$request->session();` を使って`Workerman\Protocols\Http\Session` インスタンスを取得し、そのインスタンスのメソッドを使ってセッションデータを追加、変更、削除できます。

> 注意：セッションオブジェクトが破棄されると、セッションデータは自動的に保存されます。そのため、`$request->session()` から返されるオブジェクトをグローバル配列やクラスメンバーに保存してセッションが保存されないようにしないでください。

## すべてのセッションデータを取得
```php
$session = $request->session();
$all = $session->all();
```
返されるのは配列です。セッションデータがない場合は空の配列が返されます。


## セッションから特定の値を取得
```php
$session = $request->session();
$name = $session->get('name');
```
データが存在しない場合はnullが返されます。

また、getメソッドに2番目の引数としてデフォルト値を渡すこともできます。セッション配列に対応する値が見つからない場合はデフォルト値が返されます。例：
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## セッションの保存
一つのデータを保存する場合はsetメソッドを使用します。
```php
$session = $request->session();
$session->set('name', 'tom');
```
setメソッドには返り値がありません。セッションオブジェクトが破棄されるとセッションは自動的に保存されます。

複数の値を保存する場合はputメソッドを使用します。
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
同様に、putメソッドには返り値がありません。

## セッションデータの削除
特定のセッションデータを削除する場合は、forgetメソッドを使用します。
```php
$session = $request->session();
// 1つ削除
$session->forget('name');
// 複数削除
$session->forget(['name', 'age']);
```
また、システムはforgetメソッドとは別に、一つだけ削除するdeleteメソッドも提供しています。
```php
$session = $request->session();
// $session->forget('name'); と同等
$session->delete('name');
```


## セッションの値を取得して削除
```php
$session = $request->session();
$name = $session->pull('name');
```
以下のコードと同じ効果があります
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
対応するセッションが存在しない場合、nullが返されます。


## すべてのセッションデータを削除
```php
$request->session()->flush();
```
返り値はありません。セッションオブジェクトが破棄されると、セッションは自動的に保存されます。

## 対応するセッションデータの存在を判断
```php
$session = $request->session();
$has = $session->has('name');
```
上記の場合、対応するセッションが存在しないか、対応するセッションの値がnullの場合はfalseが返されますが、それ以外の場合はtrueが返されます。

```
$session = $request->session();
$has = $session->exists('name');
```
上記のコードも同様にセッションデータの存在を判断するためのものですが、対応するセッション項目の値がnullの場合もtrueが返されます。

## ヘルパー関数session()
> 2020-12-09 追加

webmanは同じ機能を提供するために、session()というヘルパー関数を提供しています。
```php
// セッションインスタンスを取得
$session = session();
// これは次のコードと等価です
$session = $request->session();

// 特定の値を取得
$value = session('key', 'default');
// これは次のコードと等価です
$value = session()->get('key', 'default');
// これは次のコードと等価です
$value = $request->session()->get('key', 'default');

// セッションに値を設定
session(['key1'=>'value1', 'key2' => 'value2']);
// 次のコードと同等です
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// 次のコードと同等です
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## 設定ファイル
セッションの設定ファイルは`config/session.php` にあり、以下のような内容です：
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class または RedisSessionHandler::class または RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handlerがFileSessionHandler::classの場合は、'file' 、
    // handlerがRedisSessionHandler::classの場合は、'redis'、
    // handlerがRedisClusterSessionHandler::classの場合は、'redis_cluster' つまりRedisクラスター
    'type'    => 'file',

    // handlerごとに異なる設定を使用
    'config' => [
        // fileの場合の設定
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // redisの場合の設定
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

    'session_name' => 'PHPSID', // セッションIDを格納するクッキーの名前
    
    // === 以下の設定は webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // セッションを自動更新するかどうか、デフォルトはオフ
    'lifetime' => 7*24*60*60,          // セッションの有効期限
    'cookie_lifetime' => 365*24*60*60, // セッションIDを格納するクッキーの有効期限
    'cookie_path' => '/',              // セッションIDを格納するクッキーのパス
    'domain' => '',                    // セッションIDを格納するクッキーのドメイン
    'http_only' => true,               // httpOnlyを有効にするかどうか、デフォルトはオン
    'secure' => false,                 // セッションをhttpsでのみ有効にするかどうか、デフォルトはオフ
    'same_site' => '',                 // CSRF攻撃とユーザー追跡を防ぐため、strict/lax/noneのいずれかを指定
    'gc_probability' => [1, 1000],     // セッションのガベージコレクションの確率
];
```
> **注意** 
> webman 1.4.0以降でSessionHandlerの名前空間が変更され、次のようになります：
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## 有効期限の設定
webman-framework < 1.3.14 の場合、webmanのセッションの有効期限は`php.ini`で設定する必要があります。

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

有効期限を1440秒に設定する場合、以下のように設定します。
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```
> **ヒント**
> `php --ini` コマンドを使用して `php.ini` の場所を見つけることができます。
