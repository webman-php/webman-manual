# มิดเดิลแวร์ (Middleware)

มิดเดิลแวร์ทั่วไปใช้สำหรับการตรวจจับคำขอหรือการตอบรับ ตัวอย่างเช่นการตรวจสอบสิทธิ์ผู้ใช้ที่มีการเข้าร่วมก่อนการดำเนินการควบคุม หรือการเพิ่มส่วนหัวของการตอบรับ หรือการวัดสัดส่วนคำขอ uri และอื่น ๆ

## โมเดลของมิดเดิลแวร์แบบหลวม 

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── คำขอ ───────────────────────> ควบคุม── การตอบรับ───────────────────────────> ลูกค้า
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
มิดเดิลแวร์และควบคุมเพื่อสร้างโมเดลหลวม คือ เหมือนกับชั้นหนังสือหลายๆ ชั้น โดยที่ควบคุมคือในส่วนกลางของดอกหอม ดังที่แสดงในภาพเหตุการณ์ คำขอจะเคลื่อนที่ผ่านมิดเดิลแวร์ 1 2 3 จนถึงควบคุม ควบคุมจะส่งคำขอกลับคืนด้วยลำดับ 3 2 1 สุดท้ายส่งคืนไปยังลูกค้า เราสามารถรับคำขอและคำตอบได้ภายในมิดเดิลแวร์ด้วย 

## การคัดกรองคำขอ
บางครั้งเราไม่ต้องการให้คำขอไปยังขั้วควบคุมเช่น ถ้าเราพบว่าผู้ใช้ปัจจุบันไม่ได้เข้าสู่ระบบที่เราพบก็สามารถคัดกรองคำขอและส่งคำตอบการเข้าสู่ระบบ ดังนั้นกระบวนการนี้จะมีลักษณะการเปลี่ยนดังนี้

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │             มิดเดิลแวร์การตรวจสอบสิทธิ์               │     │
            │     │      ┌──────────────────────────────┐      │     │
            │     │      │         middleware3          │      │     │       
            │     │      │     ┌──────────────────┐     │      │     │
            │     │      │     │                  │     │      │     │
 　── คำขอ ───────────┐   │     │          ควบคุม         │     │      │     │
            │     │ คำตอบ  │     │                  │     │      │     │
   <─────────────────┘   │     └──────────────────┘     │      │     │
            │     │      │                              │      │     │
            │     │      └──────────────────────────────┘      │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

ดังที่แสดงในภาพเหตุการณ์การขอเข้าสู่ระบบถึงมิดเดิลแวร์การตรวจสอบสิทธิ์แล้วทำให้เกิดคำตอบการเข้าสู่ระบบเราสามารถรวมความชื้นถึงการส่งคำขอผลักษณ์ผ่านมิดเดิลแวร์ 1 แล้วส่งคืนไปยังเบราว์เซอร์ของเรา

## อินเทอร์เฟซมิดเดิลแวร์ 
มิดเดิลแวร์ต้องทำการปฏิบัติตามอินเทอร์เฟซ `Webman\MiddlewareInterface` 
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
หมายความว่าจะต้องปฏิบัติตามวิธี `process` วิธี `process` จะต้องทำการคืนอ็อบเจกต์ `Webman\Http\Response` ที่ต้องไปหาโดยได้รับการสร้างจาก `$handler($request)` นอกจากนี้ยังสามารถเป็นการคืนจาก `response()` `json()` `xml()` `redirect()` หรือการสร้างการตอบรับอย่างอื่น (คำขอจะหยุดเคลื่อนที่ผ่านดอกหอม) 

## การรับคำขอและการตอบรับในมิดเดิลแวร์ 
จากมิดเดิลแวร์เราสามารถรับคำขอและการตอบรับหรือสามารถรับการดำเนินการในสามช่วง คือ
1. การคัดกรองของคำขอที่มีการดำเนินการก่อน  
2. ช่วงการควบคุมของการดำเนินการของคำขอ 
3. ระยะการตอบรับของการดำเนินการหลังดำเนินการ

สามช่วงในมิดเดิลแวร์จะปรากฏดังนี้
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'นี่คือช่วงที่คำขอเคลื่อนที่ผ่านดอกหอม ซึ่งคือการดำเนินการก่อน';

        $response = $handler($request); // ดำเนินการกล่างผ่านดอกหอมไปยั้งควบคุม

        echo 'นี่คือช่วงที่การตอบรับส่งคำออกมา ซึ่งคือการดำเนินการหลังดำเนินการ';

        return $response;
    }
}
```
## ตัวอย่าง: มิดเดิลแช่ข้อมูล
สร้างไฟล์ `app/middleware/AuthCheckTest.php` (หากไม่มีโฟลเดอร์โปรดสร้างโดยต自) ดังนี้:
```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // ล็อกอินแล้ว, ดำเนินการต่อในระดับ onion
            return $handler($request);
        }

        // ใช้การสะท้อนเพื่อรับรายละเอียดว่า เมดทอดไหนของคอนโทรลเลอร์ไม่ต้องการล็อกอิน
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // เมื่อการเข้าถึงเม็ดทอดต้องการล็อกอิน
        if (!in_array($request->action, $noNeedLogin)) {
            // ประกาศร้องขอการเปลี่ยนเส้นทาง, ต้นทางพักการผ่าน onion
            return redirect('/user/login');
        }

        // ไม่ต้องการล็อกอิน, ดำเนินการต่อในระดับ onion
        return $handler($request);
    }
}
```

สร้างคอนโทรเลอร์ใหม่ `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * เมดทอดที่ไม่ต้องการล็อกอิน
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'login ok']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **หมายเหตุ**
> `$noNeedLogin` เก็บเมดทอดของคอนโทรลเลอร์ที่ไม่ต้องการล็อกอิน

เพิ่มมิดเดิลแช่ในทุกที่ใน `config/middleware.php` ดังนี้:
```php
return [
    // มิดเดลแช่ทั่วไป
    '' => [
        // ... ย่อหน้ามิดเดลแช่อื่น ๆ
        app\middleware\AuthCheckTest::class,
    ]
];
```

หลังจากมีมิดเดลแช่การตรวจสอบสิทธิ์แล้ว เราสามารถเขียนโค้ดธุรกิจในระดับคอนโทรเลอร์โดยไม่ต้องกังวลเรื่องการล็อกอินของผู้ใช้งาน

## ตัวอย่าง: มิดเดลแช่ร้องขอข้ามโดเมน
สร้างไฟล์ `app/middleware/AccessControlTest.php` (หากไม่มีโฟลเดอร์โปรดสร้างโดยตนเอง) ดังนี้:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // ถ้าเป็นการร้องขอหน้าตา options คืนตอบสนองเปล่าหรือ
        // ดำเนินการต่อในระดับ onion และรับสนองหนึ่งตัว
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // เพิ่มส่วนของหัวที่เกี่ยวกับการข้ามโดเมนลงในสนอง
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

> **คำแนะนำ**
> การข้ามโดเมนอาจทำให้เกิดการร้องขอ options ดังนั้นเราไม่ต้องการให้การร้องขอ options เข้าสู่คอนโทรเลอร์ ดังนั้นเราคืนตอบสนองเปล่า(`response('')`) เพื่อบล็อกการร้องขอ

เพิ่มมิดเดลแช่การควบคุมใช้ `config/middleware.php` ดังนี้:
```php
return [
    // มิดเดลแช่ทั่วไป
    '' => [
        // ... ย่อหน้ามิดเดลแช่อื่น ๆ
        app\middleware\AccessControlTest::class,
    ]
];
```

> **หมายเหต**
> หากการร้องขอ ajax กำหนดหัวข้อที่กำหนดเอง เราจำเป็นต้องเพิ่มฟิลด์ `Access-Control-Allow-Headers` ลงในมิดเดลซึ่งเป็นฟิลด์อัตโนมัติแล้วมิจะฟ้อง `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

## การอธิบาย

 - มิดเดลแช่แบ่งเป็น มิดเดลแช่ทั่วไป, มิดเดลแช่แอปพลิเคชัน (มิดเดลแช่แอปพลิเคชันจะมีผลเฉพาะในโหมดแอปพลิเคชันหลายระบบ ดูที่[แอพพลิเคชันหลายระบบ](multiapp.md)) และ การ์ชคุมมิดเดล
 - ณ ตอนนี้ไม่รองรับมิดเดลสำหรับคอนโทรเลอร์เดียว (แต่สามารถใช้ทางมิดเดลแช่และตรวจสอบ $request->controller เพื่อทำเหมือนกับมิดเดลของคอนโทรเลอร์)
 - ตำแหน่งของแฟ้มการตั้งค่ามิดเดลตั้งอยู่ที่ `config/middleware.php`
 - การตั้งค่ามิดเดลทั่วไปที่กุเป็นดังต่อไปนี้:

```php
return [
    // มิดเดลแช่ทั่วไป
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // มิดเดลแอพพลิเคชันแช่(มีผลเฉพาะในโหมดแอพพลิเคชันหลายระบบ)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## การโยกยแกนมิดเดล

เราสามารถกำหนดมิดเดลต่าคุมที่เฉพาะเส้นทางหนึ่งหรือกลุ่มเส้นทาง ตัวอย่างเช่นใน `config/route.php` เพิ่มการตั้งค่าดังนี้:
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## พารามิเตอร์ที่ส่งให้มิดเดลโยกยแกน(middleware->setParams)

**การตั้งค่าเส้นทาง `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**มิดเดล(ถ้าเป็นมิดเดลแช่ทั่วไป)**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // ค่าพารามิเตอร์ของได้เส้นทาง` $request->route`  ไม่ใช่ null, เราจำเป็นต้องตรวจสอบว่า $request->route ว่างเพียงแค่
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## การส่งพารามิเตอร์จากมิดเดลไปยังคอนโทร์เลอร์

ในบางครั้งคอนโทร์เลอร์ต้องการใช้ข้อมูลที่มิดเดลสร้างขึ้น ในกรณีนี้ เราสามารถส่งข้อมูลผ่านการเพิ่มคุณสมบัติลงใน $request ของคอนโทร์เลอร์ได้ ดังตัวอย่าง:

**มิดเดล**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**คอนโทรเลอร์:**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## การเรียกใช้ข้อมูลเส้นทางปัจจุบันจากมิดเดิลแวร์
> **หมายเหตุ**
> มีการต้องการ webman-framework >= 1.3.2

เราสามารถใช้ `$request->route` เพื่อรับวัตถุเส้นทาง และเรียกใช้เมธอดที่เกี่ยวข้อง

**การกำหนดเส้นทาง**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**มิดเดิลแวร์**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // หากคำขอไม่ตรงกับเส้นทางใดเลย (ยกเว้นเส้นทางเริ่มต้น) $request->route จะเป็น null
        // ถ้าเราสนใจเรียกหน้าเว็บที่ /user/111 ตัวอย่างการพิมพ์ค่าออกมาจะเป็นดังนี้
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **หมายเหตุ**
> เมธอด `$route->param()` ต้องการ webman-framework >= 1.3.16

## มิดเดิลแวร์ในการรับข้อผิดพลาด
> **หมายเหตุ**
> มีการต้องการ webman-framework >= 1.3.15

ในกระบวนการการดำเนินการธุรกิจอาจเกิดข้อผิดพลาด ในมิดเดลแวร์สามารถใช้ `$response->exception()` เพื่อรับข้อผิดพลาด

**การกำหนดเส้นทาง**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('การทดสอบข้อผิดพลาด');
});
```

**มิดเดลแวร์:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## มิดเดลแวร์ทั่วไป

> **หมายเหตุ**
> คุณสมบัตินี้ต้องการ webman-framework >= 1.5.16

มิดเดลแวร์ทั่วไปของโปรเจ็กต์หลักจะมีผลกับโปรเจ็กต์หลักเท่านั้น และจะไม่มีผลต่อ[ปลั๊กอินแอป](app/app.md)ใด ๆ บางครั้งเราต้องการเพิ่มมิดเดลแวร์ที่มีผลต่อทุก ๆ ปลั๊กอิน และในกรณีนี้คุณสามารถใช้มิดเดลแวร์ทั่วไป

ใน `config/middleware.php` เพิ่มการกำหนดค่าตามนี้:
```php
return [
    '@' => [ // เพิ่มมิดเดลแวร์ทั่วไปสำหรับโปรเจ็กต์หลักและปลั๊กอินทั้งหมด
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // เพิ่มมิดเดลแวร์ทั่วไปสำหรับโปรเจ็กต์หลักเท่านั้น
];
```

> **เกริ่น**
> มิดเดลแวร์ทั่วไป `@` ไม่เพียงแต่สามารถกำหนดในโปรเจ็กต์หลักเท่านั้น แต่ยังสามารถกำหนดในโปรเจ็กต์อื่น ๆ อีกด้วย เช่น การกำหนดมิดเดลแวร์ทั่วไปในปลั๊กอินที่อยู่ใน `plugin/ai/config/middleware.php` จะมีผลต่อโปรเจ็กต์หลักและปลั๊กอินทั้งหมด

## เพิ่มมิดเดลแวร์ในปลั๊กอินที่เฉพาะเจาะจง

> **หมายเหตุ**
> คุณสมบัตินี้ต้องการ webman-framework >= 1.5.16

บางครั้งเราต้องการเพิ่มมิดเดลแวร์ใน[ปลั๊กอินแอป](app/app.md)ที่เฉพาะเจาะจง แต่ไม่ต้องการแก้ไขโค้ดของปลั๊กอิน (เนื่องจากการอัปเกรดจะทับซ้อน) ในกรณีนี้ คุณสามารถกำหนดมิดเดลแวร์ในโปรเจ็กต์หลัก

ใน `config/middleware.php` เพิ่มการกำหนดค่าตามนี้:
```php
return [
    'plugin.ai' => [], // เพิ่มมิดเดลแวร์ในปลั๊กอิน ai
    'plugin.ai.admin' => [], // เพิ่มมิดเดลแวร์ในโมดูล admin ของปลั๊กอิน ai
];
```

> **เกริ่น**
> แน่นอนยังสามารถกำหนดค่าในปลั๊กอินเพื่อส่งผลต่อปลั๊กอินอื่น ๆ ได้เช่นกัน เช่น การเพิ่มการกำหนดของแน่นอนและคล้ายกันในปลั๊กอินที่อยู่ใน `plugin/foo/config/middleware.php` จะส่งผลต่อปลั๊กอิน ai ด้วย
