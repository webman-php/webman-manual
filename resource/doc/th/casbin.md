# ไลบรารีการควบคุมการเข้าถึง Casbin สำหรับ webman-permission

## คำอธิบาย

มันขึ้นอยู่กับ [PHP-Casbin](https://github.com/php-casbin/php-casbin), กรอบการควบคุมการเข้าถึงโอเพ่นซอร์ซ์ที่มีประสิทธิภาพและมีประสิทธิภาพที่รองรับโดย `ACL`, `RBAC`, `ABAC` และอื่น ๆ รูปแบบการควบคุมการเข้าถึง

## ที่อยู่โครงการ

https://github.com/Tinywan/webman-permission

## การติดตั้ง

```php
composer require tinywan/webman-permission
```
> ส่วนขยายนี้ต้องการ PHP 7.1+ และ [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) คู่มือทางการ: https://www.workerman.net/doc/webman#/db/others

## การกำหนดค่า

### ลงทะเบียนเซอร์วิส
สร้างไฟล์กำหนดค่า `config/bootstrap.php` เนื้อหาคล้ายกับนี้:

```php
    // ...
    webman\permission\Permission::class,
```
### ไฟล์กำหนดค่า Model

สร้างไฟล์กำหนดค่า `config/casbin-basic-model.conf` เนื้อหาคล้ายกับนี้:

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
### ไฟล์กำหนดค่านโยบาย

สร้างไฟล์กำหนดค่า `config/permission.php` เนื้อหาคล้ายกับนี้:

```php
<?php

return [
    /*
     * การอนุญาตริมเริ่มต้น
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * การกำหนดแบบจำลอง
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // อะแดปเตอร์ .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * การกำหนดฐานข้อมูล
            */
            'database' => [
                // ชื่อการเชื่อมต่อฐานข้อมูล, หากไม่มีให้เป็นค่าเริ่มต้น
                'connection' => '',
                // ชื่อตารางนโยบาย (ไม่รวมคำนำหน้าตาราง)
                'rules_name' => 'rule',
                // ชื่อตารางนโยบายทั้งหมด
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

คุณสามารถตรวจสอบว่าผู้ใช้อนุญาตให้มีสิทธิ์แบบนี้

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // อนุญาตให้ eve แก้ไขบทความ
} else {
    // ปฎิเสธคำขอและแสดงข้อผิดพลาด
}
````

## มัลแวร์การอนุญาต

สร้างไฟล์ `app/middleware/AuthorizationMiddleware.php` (หากไม่มีโฟลเดอร์โปรดสร้างขึ้นเอง) แบบนี้:

```php
<?php

/**
 * มัลแวร์การอนุญาต
 * โดย ชาโอโบ วัน (ไทนีวัน)
 * เวลา 2021/09/07 14:15
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
				throw new \Exception('ขออภัย คุณไม่มีสิทธิ์ในการเข้าถึงอินเตอร์เฟซนี้');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('ข้อยกเว้นการอนุญาต' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

เพิ่มมัลแวร์แบบทั่วไปใน `config/middleware.php` ดังนี้:

```php
return [
    // มัลแวร์ทั่วไป
    '' => [
        // ... ขาดการใช้มัลแวร์อื่น ๆ ที่นี่
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## ขอบคุณ

[Casbin](https://github.com/php-casbin/php-casbin) คุณสามารถดูเอกสารทั้งหมดใน [เว็บไซต์ทางการ](https://casbin.org/) ของตน

## ใบอนุญาต

โปรเจคนี้มีใบอนุญาระดับ [Apache 2.0 license](LICENSE)
