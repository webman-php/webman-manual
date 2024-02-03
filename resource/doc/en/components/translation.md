# Multilingual Support

Multilingual support is enabled using the [symfony/translation](https://github.com/symfony/translation) component.

## Installation
```
composer require symfony/translation
```

## Building Language Packs
By default, webman places language packs in the `resource/translations` directory (please create if it doesn't exist). To change the directory, configure it in `config/translation.php`. Each language corresponds to a subfolder, with language definitions typically located in `messages.php`. For example:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

All language files return an array, for example:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Configuration

`config/translation.php`

```php
return [
    // Default language
    'locale' => 'zh_CN',
    // Fallback language, used in case the translation is not found in the current language
    'fallback_locale' => ['zh_CN', 'en'],
    // Directory for language files
    'path' => base_path() . '/resource/translations',
];
```

## Translation
Translation is done using the `trans()` method.

Create a language file `resource/translations/zh_CN/messages.php` as follows:
```php
return [
    'hello' => '你好 世界!',
];
```

Create the file `app/controller/UserController.php`
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

Accessing `http://127.0.0.1:8787/user/get` will return "你好 世界!"

## Changing the Default Language
To switch the language, use the `locale()` method.

Create a language file `resource/translations/en/messages.php` as follows:
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
        // Change language
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
Accessing `http://127.0.0.1:8787/user/get` will return "hello world!"

You can also temporarily switch the language using the fourth parameter of the `trans()` function. For example, the previous example is equivalent to the following:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Switch language using the fourth parameter
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Setting Language for Each Request Explicitly
The `translation` is a singleton, meaning all requests share this instance. If a request uses `locale()` to set the default language, it will affect all subsequent requests in the process. Therefore, we should set the language explicitly for each request. For example, using the following middleware

Create the file `app/middleware/Lang.php` (create if it doesn't exist) as follows:
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

Add the global middleware in `config/middleware.php` as follows:
```php
return [
    // Global middleware
    '' => [
        // ... other middlewares are omitted here
        app\middleware\Lang::class,
    ]
];
```

## Using Placeholders
Sometimes, a message contains variables that need to be translated, such as
```php
trans('hello ' . $name);
```
In such cases, placeholders are used for handling.

Modify `resource/translations/zh_CN/messages.php` as follows:
```php
return [
    'hello' => '你好 %name%!',
];
```
When translating, pass the values corresponding to the placeholders through the second parameter
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## Handling Plurals
In some languages, different sentence structures are used based on the count of items, for example, `There is %count% apple`. The sentence is correct when `%count%` is 1, but incorrect when greater than 1.

In such cases, use the **pipe** (`|`) to list plural forms.

Add `apple_count` to the language file `resource/translations/en/messages.php` as follows:
```php
return [
   // ...
   'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

You can even specify number ranges to create more complex plural rules:
```php
return [
   // ...
   'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Specifying the Language File
The language file is named `messages.php` by default, but you can create language files with other names.

Create the language file `resource/translations/zh_CN/admin.php` as follows:
```php
return [
    'hello_admin' => '你好 管理员!',
];
```

Specify the language file using the third parameter of `trans()` (excluding the `.php` suffix).
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## More Information
Refer to the [symfony/translation manual](https://symfony.com/doc/current/translation.html) for more details.