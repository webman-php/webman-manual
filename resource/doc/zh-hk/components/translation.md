# 多語言

多語言使用的是 [symfony/translation](https://github.com/symfony/translation) 組件。

## 安裝
```
composer require symfony/translation
```

## 建立語言包
webman默認將語言包放在`resource/translations`目錄下(如果沒有請自行創建)，如需更改目錄，請在`config/translation.php`中設定。
每種語言對應其中的一個子文件夾，語言定義默認放到`messages.php`裡。示例如下：
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

所有的語言文件都是返回一個數組例如：
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## 配置

`config/translation.php`

```php
return [
    // 默認語言
    'locale' => 'zh_CN',
    // 回退語言，當前語言中無法找到翻譯時則嘗試使用回退語言中的翻譯
    'fallback_locale' => ['zh_CN', 'en'],
    // 語言文件存放的文件夾
    'path' => base_path() . '/resource/translations',
];
```

## 翻譯

翻譯使用`trans()`方法。

創建語言文件 `resource/translations/zh_CN/messages.php` 如下：
```php
return [
    'hello' => '你好 世界!',
];
```

創建文件 `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // 你好 世界!
        return response($hello);
    }
}
```

訪問 `http://127.0.0.1:8787/user/get` 將返回 "你好 世界!"

## 更改默認語言

切換語言使用 `locale()` 方法。

新增語言文件 `resource/translations/en/messages.php` 如下：
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 切換語言
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
訪問 `http://127.0.0.1:8787/user/get` 將返回 "hello world!"

你也可以使用`trans()`函數的第4個參數來臨時切換語言，例如上面的例子和下面這個是等價的：
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 第4個參數切換語言
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## 為每個請求明確的設置語言
translation是一個單例，這意味著所有請求共享這個實例，如果某個請求使用`locale()`設置了默認語言，則它會影響這個進程的後續所有請求。所以我們應該為每個請求明確的設置語言。例如使用以下中間件

創建文件`app/middleware/Lang.php` (如目錄不存在請自行創建) 如下：
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

在 `config/middleware.php` 中添加全局中間件如下：
```php
return [
    // 全局中間件
    '' => [
        // ... 這裡省略其它中間件
        app\middleware\Lang::class,
    ]
];
```


## 使用占位符
有時，一條信息包含着需要被翻譯的變數，例如
```php
trans('hello ' . $name);
```
遇到這種情況時我們採用占位符來處理。

更改 `resource/translations/zh_CN/messages.php` 如下：
```php
return [
    'hello' => '你好 %name%!',
];
```
翻譯的時候將數據通過第二個參數將占位符對應的值傳遞進去
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## 處理複數
有些語言由於事物數量不同呈現不同的句式，例如`There is %count% apple`，當`%count%`為1時句式正確，當大於1時則錯誤。

遇到這種情況時我們採用**管道**(`|`)來列出來複數形式。

語言文件 `resource/translations/en/messages.php` 新增`apple_count`如下：
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

我們甚至可以指定數字範圍，創建更加複雜的複數規則：
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## 指定語言文件

語言文件默認名字為`messages.php`，實際上你可以創建其它名稱的語言文件。

創建語言文件 `resource/translations/zh_CN/admin.php` 如下：
```php
return [
    'hello_admin' => '你好 管理員!',
];
```

通過`trans()`第三個參數來指定語言文件(省略`.php`後綴)。
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理員!
```

## 更多信息
參考 [symfony/translation手冊](https://symfony.com/doc/current/translation.html)