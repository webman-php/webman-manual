# 多语言

多语言使用的是 [symfony/translation](https://github.com/symfony/translation) 组件。

## 安装
```
composer require symfony/translation
```

## 建立语言包
webman默认将语言包放在`resource/translations`目录下(如果没有请自行创建)，如需更改目录，请在`config/translation.php`中设置。
每种语言对应其中的一个子文件夹，语言定义默认放到`messages.php`里。示例如下：
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

所有的语言文件都是返回一个数组例如：
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
    // 默认语言
    'locale' => 'zh_CN',
    // 回退语言，当前语言中无法找到翻译时则尝试使用回退语言中的翻译
    'fallback_locale' => ['zh_CN', 'en'],
    // 语言文件存放的文件夹
    'path' => base_path() . '/resource/translations',
];
```

## 翻译

翻译使用`trans()`方法。

创建语言文件 `resource/translations/zh_CN/messages.php` 如下：
```php
return [
    'hello' => '你好 世界!',
];
```

创建文件 `app/controller/UserController.php`
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

访问 `http://127.0.0.1:8787/user/get` 将返回 "你好 世界!"

## 更改默认语言

切换语言使用 `locale()` 方法。

新增语言文件 `resource/translations/en/messages.php` 如下：
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
        // 切换语言
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
访问 `http://127.0.0.1:8787/user/get` 将返回 "hello world!"

你也可以使用`trans()`函数的第4个参数来临时切换语言，例如上面的例子和下面这个是等价的：
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 第4个参数切换语言
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## 为每个请求明确的设置语言
translation是一个单例，这意味着所有请求共享这个实例，如果某个请求使用`locale()`设置了默认语言，则它会影响这个进程的后续所有请求。所以我们应该为每个请求明确的设置语言。例如使用以下中间件

创建文件`app/middleware/Lang.php` (如目录不存在请自行创建) 如下：
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

在 `config/middleware.php` 中添加全局中间件如下：
```php
return [
    // 全局中间件
    '' => [
        // ... 这里省略其它中间件
        app\middleware\Lang::class,
    ]
];
```


## 使用占位符
有时，一条信息包含着需要被翻译的变量，例如
```php
trans('hello ' . $name);
```
遇到这种情况时我们采用占位符来处理。

更改 `resource/translations/zh_CN/messages.php` 如下：
```php
return [
    'hello' => '你好 %name%!',
];
```
翻译的时候将数据通过第二个参数将占位符对应的值传递进去
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## 处理复数
有些语言由于事物数量不同呈现不同的句式，例如`There is %count% apple`，当`%count%`为1时句式正确，当大于1时则错误。

遇到这种情况时我们采用**管道**(`|`)来列出来复数形式。

语言文件 `resource/translations/en/messages.php` 新增`apple_count`如下：
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

我们甚至可以指定数字范围，创建更加复杂的复数规则：
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## 指定语言文件

语言文件默认名字为`messages.php`，实际上你可以创建其它名称的语言文件。

创建语言文件 `resource/translations/zh_CN/admin.php` 如下：
```php
return [
    'hello_admin' => '你好 管理员!',
];
```

通过`trans()`第三个参数来指定语言文件(省略`.php`后缀)。
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## 更多信息
参考 [symfony/translation手册](https://symfony.com/doc/current/translation.html)
