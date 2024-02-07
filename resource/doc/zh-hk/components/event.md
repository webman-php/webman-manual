# event事件處理
`webman/event` 提供一種精巧的事件機制，可實現在不侵入程式碼的情況下執行一些業務邏輯，實現業務模組之間的解耦。典型的場景如一個新用戶註冊成功時，只要發佈一個自定義事件如`user.register`，各個模組遍能收到該事件執行相應的業務邏輯。

## 安裝
`composer require webman/event`

## 訂閱事件
訂閱事件統一通過文件`config/event.php`來配置
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...其它事件處理函數...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...其它事件處理函數...
    ]
];
```
**說明：**
- `user.register` `user.logout` 等是事件名稱，字串類型，建議小寫單詞並以點(`.`)分割
- 一個事件可以對應多個事件處理函數，調用順序為配置的順序

## 事件處理函數
事件處理函數可以是任意的類方法、函數、閉包函數等。
例如創建事件處理類 `app/event/User.php` (目錄不存在請自行創建)
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

## 發佈事件
使用`Event::emit($event_name, $data);`發佈事件，例如
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

> **提示**
> `Event::emit($event_name, $data);`參數$data可以是任意的數據，例如數組、類實例、字串等

## 通配符事件監聽
通配符註冊監聽允許您在同一個監聽器上處理多個事件，例如`config/event.php`裡配置
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
我們可以通過事件處理函數第二個參數`$event_data`獲得具體的事件名
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // 具體的事件名，如 user.register user.logout 等
        var_export($user);
    }
}
```

## 停止事件廣播
當我們在事件處理函數裡返回`false`時，該事件將停止廣播

## 閉包函數處理事件
事件處理函數可以是類方法，也可以是閉包函數例如

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

##  查看事件及監聽器
使用命令 `php webman event:list` 查看項目配置的所有事件及監聽器

## 注意事項
event事件處理並不是異步的，event不適合處理慢業務，慢業務應該用消息隊列處理，例如[webman/redis-queue](https://www.workerman.net/plugin/12)
