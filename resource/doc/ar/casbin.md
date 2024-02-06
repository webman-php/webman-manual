# مكتبة التحكم في الوصول Casbin webman-permission

## الوصف

تعتمد على [PHP-Casbin](https://github.com/php-casbin/php-casbin) ، وهو إطار تحكم وصول مفتوح المصدر قوي وفعال يدعم نماذج التحكم في الوصول مثل `ACL`, `RBAC`, `ABAC`.


## عنوان المشروع

https://github.com/Tinywan/webman-permission


## التثبيت

```php
composer require tinywan/webman-permission
```

> هذا الإضافة تتطلب PHP 7.1+ و [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)، الدليل الرسمي: https://www.workerman.net/doc/webman#/db/others

## الإعداد

### تسجيل الخدمة
قم بإنشاء ملف تكوين جديد `config/bootstrap.php` مع المحتوى المشابه للمثال التالي:

```php
    // ...
    webman\permission\Permission::class,
```

### إعداد ملف النموذج

قم بإنشاء ملف تكوين جديد `config/casbin-basic-model.conf` مع المحتوى المشابه للمثال التالي:
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

### ملف تكوين السياسة

قم بإنشاء ملف تكوين جديد `config/permission.php` مع المحتوى المشابه للمثال التالي:
```php
<?php

return [
    /*
     * صلاحية الافتراضي
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * النموذج المعدّل
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // المحول .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * إعداد قاعدة البيانات.
            */
            'database' => [
                // اسم اتصال قاعدة البيانات ، فارغ للقيمة الافتراضية.
                'connection' => '',
                // اسم جدول السياسة (بلا البادئة)
                'rules_name' => 'rule',
                // اسم جدول السياسة الكامل.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## البدء السريع

```php
use webman\permission\Permission;

// إضافة أذونات للمستخدم
Permission::addPermissionForUser('eve', 'articles', 'read');
// إضافة دور للمستخدم.
Permission::addRoleForUser('eve', 'writer');
// إضافة أذونات للقاعدة
Permission::addPolicy('writer', 'articles','edit');
```

يمكنك التحقق من ما إذا كان لدى المستخدم هذه الأذونات

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // السماح لـ eve بتعديل المقالات
} else {
    // رفض الطلب، وعرض الخطأ
}
````

## وسيط الاعتماد

إنشاء ملف `app/middleware/AuthorizationMiddleware.php` (إذا لم يكن المجلد موجودًا ، فيجب إنشاءه بنفسك) على النحو التالي:

```php
<?php

/**
 * وسيط الاعتماد
 * by ShaoBo Wan (Tinywan)
 * datetime 2021/09/07 14:15
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
				throw new \Exception('عذرًا، ليس لديك صلاحية الوصول إلى هذا الاستجابة');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('استثناء الاعتماد' . $exception->getMessage());
		}
		return $next($request);
	}
}
```
   
إضافة وسيط عام إلى `config/middleware.php` على النحو التالي:

```php
return [
    // الوسطاء العامين
    '' => [
        // ... يتم تجاهل وسطاء مختلفين هنا
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## شكر

[Casbin](https://github.com/php-casbin/php-casbin)، يمكنك الاطلاع على جميع الوثائق على [موقعه الرسمي](https://casbin.org/).

## الترخيص

يتم ترخيص هذا المشروع بموجب [رخصة Apache 2.0](LICENSE).
