# Multilingual Support

Webman 使用 [symfony/translation](https://github.com/symfony/translation) 组件来实现多语言支持。

## 安装
```shell
composer require symfony/translation
```

## 创建语言包
默认情况下，webman 将语言包存储在 `resource/translations` 目录中（如果目录不存在则创建）。如果要更改目录，可以在 `config/translation.php` 中进行配置。每种语言对应目录中的一个子文件夹，语言定义通常存储在 `messages.php` 文件中。以下是一个示例目录结构：

```text
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

所有语言文件应返回一个数组，例如：

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
    // 回退语言 - 如果找不到当前语言的翻译，则尝试使用回退语言的翻译
    'fallback_locale' => ['zh_CN', 'en'],
    // 存储语言文件的文件夹
    'path' => base_path() . '/resource/translations',
];
```

## 翻译文本
可以使用 `trans()` 方法来翻译文本。

创建一个语言文件 `resource/translations/zh_CN/messages.php`，内容如下：

```php
return [
    'hello' => '你好 世界!',
];
```

创建一个文件 `app/controller/UserController.php`：

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

访问 `http://127.0.0.1:8787/user/get` 将返回 "你好 世界!"。

## 更改默认语言
要更改语言，请使用 `locale()` 方法。

添加一个新的语言文件 `resource/translations/en/messages.php`，内容如下：

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
        // 切换到英语
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```

访问 `http://127.0.0.1:8787/user/get` 将返回 "hello world!"。

还可以使用 `trans()` 函数的第四个参数临时切换语言。例如，以下两个示例是等价的：

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 使用第四个参数切换语言
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## 为每个请求设置语言
翻译是一个单例，这意味着所有请求共享同一个实例。如果请求使用 `locale()` 设置默认语言，它将影响进程中的所有后续请求。因此，我们应该显式为每个请求设置语言。例如，使用以下中间件：

创建文件 `app/middleware/Lang.php`（如果目录不存在则创建）：

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

在 `config/middleware.php` 中添加全局中间件：

```php
return [
    // 全局中间件
    '' => [
        // ... 其他中间件
        app\middleware\Lang::class,
    ]
];
```

## 使用占位符
有时，消息中包含需要翻译的变量，例如：

```php
trans('hello ' . $name);
```

在这种情况下，可以使用占位符来处理。

修改 `resource/translations/zh_CN/messages.php`，内容如下：

```php
return [
    'hello' => '你好 %name%!',
];
```

在翻译时，可以通过第二个参数传递占位符的值：

```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## 处理复数形式
在某些语言中，基于对象数量需要使用不同的句子结构。例如，当 `%count%` 为 1 时，“There is %count% apple” 是正确的，但当大于 1 时不正确。

为了处理这个问题，使用 **管道**（`|`）列出复数形式。

在语言文件 `resource/translations/en/messages.php` 中添加 `apple_count`：

```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

甚至可以指定数字范围来创建更复杂的复数形式规则：

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
默认情况下，语言文件名为 `messages.php`，但可以创建其他文件名。

创建语言文件 `resource/translations/zh_CN/admin.php`，内容如下：

```php
return [
    'hello_admin' => '你好 管理员!',
];
```

使用 `trans()` 的第三个参数来指定语言文件（不含 `.php` 扩展名）：

```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## 更多信息
更多信息，请参阅 [symfony/translation 文档](https://symfony.com/doc/current/translation.html)。
