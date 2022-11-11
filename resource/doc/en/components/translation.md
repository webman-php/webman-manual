# Multi-language

Multi-languageUsed by [symfony/translation](https://github.com/symfony/translation) Component。

## Install
```
composer require symfony/translation
```

## Create Language Packages
webmanBy default, language packages are placed in the `resource/translations` directory (please create your own if you don't have one), if you want to change the directory, please set it in `config/translation.php`。
Each language corresponds to one of the subfolders, and the language definitions are placed in `messages.php` by default. The example is as follows：
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

All language files are returned as an array for example：
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Configure

`config/translation.php`

```php
return [
    // Default Language
    'locale' => 'zh_CN',
    // Fallback language, try to use the translation in the fallback language if the translation cannot be found in the current language
    'fallback_locale' => ['zh_CN', 'en'],
    // the folder where the language files are stored
    'path' => base_path() . '/resource/translations',
];
```

## Translate

Translate using the `trans()` method。

Create the language file `resource/translations/zh_CN/messages.php` as follows：
```php
return [
    'hello' => 'Hello World!',
];
```

Create files `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // Hello World!
        return response($hello);
    }
}
```

Accessing `http://127.0.0.1:8787/user/get` will return "Hello World!"

## Change default language

Switch language using the `locale()` method。

Add new language file `resource/translations/en/messages.php` as follows：
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
        // Switching languages
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
Accessing `http://127.0.0.1:8787/user/get` will return "hello world!"

You can also use the 4th argument of the `trans()` function to temporarily switch languages, for example, the above example and the following one are equivalent：
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 4th parameter switch language
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Explicitly set the language for each request
translationis actually，This means that all requests share this instance，if a request uses`locale()`setDefault Language，then it will affect all subsequent requests for this process。Otherwise in unenabledExplicitly set the language for each request。For example using the following middleware

Create the file `app/middleware/Lang.php` (if the directory does not exist, please create it yourself) as follows：
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

Add the global middleware to `config/middleware.php` as follows：
```php
return [
    // Global Middleware
    '' => [
        // ... Omit other middleware here
        app\middleware\Lang::class,
    ]
];
```


## Use placeholders
Sometimes, a message contains variables that need to be translated, for example
```php
trans('hello ' . $name);
```
We use placeholders to deal with this situation when we encounter it。

Change `resource/translations/zh_CN/messages.php` as follows：
```php
return [
    'hello' => 'Hello %name%!',
];
```
The translation passes the data in through the second parameter with the value corresponding to the placeholder
```php
trans('hello', ['%name%' => 'webman']); // Hello webman!
```

## Handle plurals
Some languages present different syntax due to the number of things, for example `There is %count% apple`, the syntax is correct when `%count%` is 1, and incorrect when it is greater than 1。

When we encounter this situation we use**Pipeline**(`|`)to list the plural form。

Language file `resource/translations/en/messages.php` adds `apple_count` as follows：
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

We can even specify numeric ranges to create more complex plural rules：
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Specify language file

The default name of the language file is `message.php`, but you can actually create language files with other names。

Create the language file `resource/translations/zh_CN/admin.php` as follows：
```php
return [
    'hello_admin' => 'Hello Admin!',
];
```

Specify the language file with the third parameter of `trans()` (omitting the `.php` suffix))。
```php
trans('hello', [], 'admin', 'zh_CN'); // Hello Admin!
```

## More Info
reference [symfony/translationmanual](https://symfony.com/doc/current/translation.html)