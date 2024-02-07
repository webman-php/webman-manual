# Casbin অ্যাক্সেস কন্ট্রোল লাইব্রেরি webman-permission

## বর্ণনা

এটি [PHP-Casbin](https://github.com/php-casbin/php-casbin) এর উপর ভিত্তি। এটি একটি শক্তিশালী, দক্ষতাপূর্ণ ও ওপেন সোর্স অ্যাক্সেস কন্ট্রোল ফ্রেমওয়ার্ক, `ACL`, `RBAC`, `ABAC` ইত্যাদি অ্যাক্সেস কন্ট্রোল মডেল সমর্থন করে।

## প্রজেক্ট ঠিকানা

https://github.com/Tinywan/webman-permission

## ইনস্টলেশন

```php
composer require tinywan/webman-permission
```
> এই এক্সটেনশনটি PHP 7.1+ এবং [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) এর প্রয়োজন, অফিসিয়াল ম্যানুয়াল: https://www.workerman.net/doc/webman#/db/others

## কনফিগারেশন

### সার্ভিস রেজিস্ট্রেশন

`config/bootstrap.php` নামে নতুন কনফিগ ফাইল তৈরি করুন এবং নিম্নলিখিত কোড লিখুন:

```php
    // ...
    webman\permission\Permission::class,
```

### মডেল কনফিগারেশন ফাইল

`config/casbin-basic-model.conf` নামে নতুন কনফিগ ফাইল তৈরি করুন এবং নিম্নলিখিত কোড লিখুন:

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

`config/permission.php` নামে নতুন কনফিগ ফাইল তৈরি করুন এবং নিম্নলিখিত কোড লিখুন:

```php
<?php

return [
    /*
     * ডিফল্ট পারমিশন
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * মডেল কনফিগারেশন
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // অ্যাডাপ্টার
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * ডাটাবেস কনফিগারেশন
            */
            'database' => [
                // ডাটাবেস কানেকশন নাম, ডিফল্ট কনফিগারের জন্য খালি রাখুন.
                'connection' => '',
                // রুল টেবিলের নাম (প্রিফিক্স ছাড়া)
                'rules_name' => 'rule',
                // রুল টেবিলের সম্পূর্ণ নাম
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## দ্রুত শুরু করুন

```php
use webman\permission\Permission;

// ব্যবহারকারীকে অনুমতি যোগ করুন
Permission::addPermissionForUser('eve', 'articles', 'read');
// ব্যবহারকারীর জন্য একটি ভূমিকা যোগ করুন।
Permission::addRoleForUser('eve', 'writer');
// নিয়মের জন্য অনুমতি যোগ করুন
Permission::addPolicy('writer', 'articles', 'edit');
```

আপনি পরীক্ষা করতে পারেন কোন ব্যবহারকারীর এই ধরনের অনুমতি আছে কিনা

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // eve কে আর্টিকেলগুলি সম্পাদনা করার অনুমতি দেয়া হয়েছে
} else {
    // অনুরোধ নিষিদ্ধ, একটি ত্রুটি দেখান
}
````

## অনুমতি মিডলওয়্যার

ফাইল `app/middleware/AuthorizationMiddleware.php` তৈরি করুন (যদি ডিরেক্টরি না থাকে, তাহলে নিজে তৈরি করুন) এবং নিম্নলিখিত কোড লিখুন:

```php
<?php

/**
 * অনুমতি মিডলওয়্যার
 * লেখক: শাওবো ওয়ান (টিনিওয়ান)
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
				throw new \Exception('দুঃখিত, আপনার এই পছন্দের অ্যাপ্লিকেশনে অ্যাক্সেস পাওয়া যাবে না');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('অনুমতি অস্বীকৃতি' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

`config/middleware.php` ফাইলে গ্লোবাল মিডলওয়্যার যোগ করুন:

```php
return [
    // গ্লোবাল মিডলওয়্যার
    '' => [
        // ... এখানে অন্যান্য মিডলওয়্যার অপসারণ করা হয়েছে
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## ধন্যবাদ

[Casbin](https://github.com/php-casbin/php-casbin), আপনি এর [অফিসিয়াল ওয়েবসাইট](https://casbin.org/) এ পূর্ণ নথিটি দেখতে পারেন।

## লাইসেন্স

এই প্রকল্পটি [Apache 2.0 লাইসেন্স](LICENSE) এর অধীনে লাইসেন্স করা হয়েছে।
