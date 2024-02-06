# ไลบรารีควบคุมการเข้าถึง Casbin ของ webman-permission

## คำอธิบาย

มันขึ้นอยู่กับ [PHP-Casbin](https://github.com/php-casbin/php-casbin),  framework การควบคุมการเข้าถึงโอเพ่นซอร์แบบแข็งแรงและมีประสิทธิภาพที่รองรับ `ACL`, `RBAC`, `ABAC` และโมเดลการควบคุมการเข้าถึงอื่น ๆ

## ที่อยู่โปรเจ็กต์

https://github.com/Tinywan/webman-permission

## การติดตั้ง

```php
composer require tinywan/webman-permission
```
> ส่วนขยายนี้สามารถทำงานได้บน PHP 7.1+ และ [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) ดูเพิ่มเติมที่: https://www.workerman.net/doc/webman#/db/others

## การกำหนดค่า

### ลงทะเบียนเซอร์วิส
สร้างไฟล์กำหนดค่า `config/bootstrap.php` โดยมีเนื้อหาเช่นต่อไปนี้:

```php
    // ...
    webman\permission\Permission::class,
```
### การกำหนดค่าโมเดล

สร้างไฟล์กำหนดค่า `config/casbin-basic-model.conf` โดยมีเนื้อหาเช่นต่อไปนี้:

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
### การกำหนดค่าโพลิซี

สร้างไฟล์กำหนดค่า `config/permission.php` โดยมีเนื้อหาเช่นต่อไปนี้:

```php
<?php

return [
    /*
     * การกำหนดค่าสิทธิ์เริ่มต้น
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * การตั้งค่าโมเดล
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // ตัวปรับเปลี่ยน .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * การตั้งค่าฐานข้อมูล
            */
            'database' => [
                // ชื่อการเชื่อมต่อฐานข้อมูล หากไม่ได้ใส่จะใช้ค่าเริ่มต้น
                'connection' => '',
                // ชื่อตารางนําหนาย (ไม่รวมคำนําหน้าตาราง)
                'rules_name' => 'rule',
                // ชื่อตารางนําหนายที่สมบูรณ์
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## เริ่มต้นอย่างรวดเร็ว

```php
use webman\permission\Permission;

// เพิ่มสิทธิ์ให้กับผู้ใช้
Permission::addPermissionForUser('eve', 'articles', 'read');
// เพิ่มบทบาทให้กับผู้ใช้
Permission::addRoleForUser('eve', 'writer');
// เพิ่มสิทธิ์ให้กับกฎ
Permission::addPolicy('writer', 'articles','edit');
```

คุณสามารถตรวจสอบว่าผู้ใช้มีสิทธิ์ดังกล่าวหรือไม่

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // อนุญาตให้ eve แก้ไขบทความ
} else {
    // ปฏิเสธคำขอ แสดงข้อผิดพลาด
}
````

## Middleware การอนุญาต

สร้างไฟล์ `app/middleware/AuthorizationMiddleware.php` (ถ้าโฟลเดอร์ไม่มีให้สร้างเอง) ตามดังต่อไปนี้:

```php
<?php

/**
 * Middleware การอนุญาต
 * โดย ShaoBo Wan (Tinywan)
 * วันที่เวลา 2021/09/07 14:15
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
				throw new \Exception('ขอโทษ คุณไม่มีสิทธิ์ในการเข้าถึงอินเทอร์เฟซนี้');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('การอนุญาตผิดพลาด' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

เพิ่ม Middleware ด้านบนในไฟล์ `config/middleware.php` ตามดังนี้:

```php
return [
    // Middleware ทั่วไป
    '' => [
        // ... ส่วนนี้ข้าม Middleware อื่น ๆ
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## ขอบคุณ

[Casbin](https://github.com/php-casbin/php-casbin) คุณสามารถดูเอกสารทั้งหมดบน [เว็บไซต์ทางการ](https://casbin.org/) ของมันได้.

## ใบอนุญาต

โครงการนี้เป็นลิขสิทธิ์ภายใต้ [ใบอนุญาต Apache 2.0](LICENSE).
