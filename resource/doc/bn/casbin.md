# Casbin এক্সেস নিয়ন্ত্রণ লাইব্রেরি webman-permission

## বর্ণনা

এটি [PHP-Casbin](https://github.com/php-casbin/php-casbin) এর উপর ভিত্তি করে, এটি একটি শক্তিশালী, দক্ষ ওপেন সোর্স এক্সেস কন্ট্রোল ফ্রেমওয়ার্ক,`ACL`, `RBAC`, `ABAC` এমন একই অ্যাক্সেস কন্ট্রোল মডেল সমর্থন করে।

## প্রকল্প ঠিকানা

https://github.com/Tinywan/webman-permission

## ইনস্টলেশন

```php
composer require tinywan/webman-permission
```
> এই এক্সটেনশনটি PHP 7.1+ এবং [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) এবং অফিসিয়াল ম্যানুয়াল্স সাপোর্ট করে: https://www.workerman.net/doc/webman#/db/others

## কনফিগারেশন

### সার্ভিস রেজিস্ট্রেশন
`config/bootstrap.php` নামে নতুন কনফিগারেশন ফাইল তৈরি করুন এবং তার উপর নিম্নলিখিত কনটেন্ট লিখুন:

```php
// ...
webman\permission\Permission::class,
```
### মডেল কনফিগারেশন ফাইল

`config/casbin-basic-model.conf` নামে নতুন কনফিগারেশন ফাইল তৈরি করুন এবং তার উপর নিম্নলিখিত কনটেন্ট লিখুন:

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```
### পলিসি কনফিগারেশন ফাইল

`config/permission.php` নামে নতুন কনফিগারেশন ফাইল তৈরি করুন এবং তার উপর নিম্নলিখিত কনটেন্ট লিখুন:

```php
<?php

return [
    /*
     *ডিফল্ট অনুমতি
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * মডেল সেটিং
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // অ্যাডাপ্টার
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * ডাটাবেস সেটিং
            */
            'database' => [
                // ডাটাবেস কানেকশন নাম, ডিফল্ট কনফিগারেশন ছাড়া খালি রেখে দিন
                'connection' => '',
                // পলিসি টেবিল নাম (টেবিল প্রিফিক্স বদলা হবে)
                'rules_name' => 'rule',
                // পলিসি টেবিলের পূর্ণিয় নাম
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## দ্রুত শুরু করুন

```php
use webman\permission\Permission;

// ব্যবহারকারীর জন্য অনুমতি যুক্ত করুন
Permission::addPermissionForUser('eve', 'articles', 'read');
// একটি ব্যবহারকারীর জন্য রোল যুক্ত করুন।
Permission::addRoleForUser('eve', 'writer');
// নিয়মাবলী গুলি জন্য অনুমতি যুক্ত করুন
Permission::addPolicy('writer', 'articles','edit');
```

আপনি পরীক্ষা করতে পারেন যে ব্যবহারকারীর কি এই ধরনের অনুমতি আছে

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // ইভকে আর্টিকেল সম্পাদনা করতে অনুমতি দিন
} else {
    // অনুরোধ নিষেধ করুন, একটি ত্রুটি দেখান
}
````
## অনুমোদন মিডলওয়ের

`app/middleware/AuthorizationMiddleware.php` ফাইল তৈরি করুন (এই নথি না থাকলে নিজে তৈরি করুন) নিম্নলিখিত মতে:

```php
<?php

/**
 * অনুমোদন মিডলওয়ের
 * লেখক: শাওবো ওয়ান (টাইনিয়ান)
 * সময়: 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
	public function process(Request $request, callable $next): Response
	{
		$uri = $request->path();
		try {
			$userId = 10086;
			$action = $request->method();
			if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
				throw new \Exception('দুঃখিত, আপনার এই অনুপ্রবেশ অনুমতি নেই');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('অনুমোদন অস্বীকার' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

`config/middleware.php` তে এক্সটেনশন মিডলওয়ার যোগ করুন:

```php
return [
    '' => [
        // ... অন্যান্য মিডলওয়ার এখানে প্রস্তুত করুন
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## ধন্যবাদ

[Casbin](https://github.com/php-casbin/php-casbin) কে ধন্যবাদ, আপনি তার সব নথিটি [অফিসিয়াল ওয়েবসাইট](https://casbin.org/) থেকে দেখতে পারেন।

## লাইসেন্স

এই প্রকল্পটি [অ্যাপাচি 2.0 লাইসেন্স](LICENSE)-এর অধীনে লাইসেন্সকৃত।
