# มิดเดิลแวร์
มิดเดิลแวร์ทั่วไปใช้สำหรับสกัดควบคุมหรือการตอบกลับข้อความการขอความ. ตัวอย่างเช่นการตรวจสอบสิทธิ์ของผู้ใช้เป็นข้อมูลในการเข้าถึงควบคุมก่อนการดำเนินการ, เช่นการเพิ่มหัวข้อหน้าในการตอบกลับ, เช่นการวัดสัดส่วนการขอ URL เป็นต้น

## โมเดลตรงกลางรูปหอม
```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── คำขอ ───────────────────────> ตัวควบคุม ─ การตอบกลับ ───────────────────────────> ลูกค้า
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```

มิดเดิลแวร์และตัวควบคุมเข้าด้วยกันเป็นโมเดลตรงกลางที่เป็นดอกบัวอย่างคลาสสิค ตามที่แสดงในภาพ, คำขอเป็นเหมือนลูกธนูที่ซึ่งไปผ่านที่มิดเดิลแวร์ 1,2,3 ไปสู่ตัวควบคุม โดยตัวควบคุมส่งคำขอไป แล้วมีการตอบกลับ หลังจากนั้นคำตอบกลับกลับผ่าน 3,2,1 ของมิดเดิลแวร์และส่งกลับไปที่ลูกค้า ที่เป็นเส้นทางที่ โดยมีส่วนที่คล้ายคลึงว่าในแต่ละมิดเดิลแวร์เราสามารถรับคำขอได้ และสามารถรับคำตอบกลับ

## การแยกรับคำขอ
ในบางครั้งเราอาจไม่ต้องการให้คำขอไปสู่ส่วนควบคุม, เช่นเราพบว่าผู้ใข้ปัจจุบันยังไม่ได้เข้าสู่ระบบ ดังนั้นเราสามารถให้คำขอที่ผ่านมิดเดิลแวร์ 2 แล้วในที่สุดทำคำขอใหม่ ในการเข้าสู่ระบบ โดยปฏิเสธคำขอเดิมและส่งคำใหม่ ในหน้าเข้าสู่ระบบ ลักษณะเส้นโค้งจะประมาณนี้

``` 
                              
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── คำขอ ────────────┐     │    │      ตัวควบคุม      │      │      │     │
            │     │ การตอบกลับ │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

เส้นแบบนี้คำขอมาถึงมิดเดิลแวร์ 2 หลังจาเเล่นสร้างการตอบกลับเป็นการเข้าสู่ระบบ, การส่งผ่านมิดเดิลแวร์ 1 และส่งกลับไปที่ลูกค้า

## อินเทอร์เฟสมิดเดิลแวร์
มิดเดิลแวร์ต้องปฏิบัติตามอินเทอร์เฟส`Webman\MiddlewareInterface` ดังนี้
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
ก็คือจำเป็นต้องปฏิบัติตามวิธี `process` วิธี `process` จะต้องคืน`support\Response` คือ วัตถุที่ได้มาจาก  `$handler($request)` ถ้าทำไม่ได้ต้องพากับ คำขอเข้าไปสู่โมเดลตรงกลาง, สามารถทำเป็น คำตอบ หรือสร้างโดย `response()` `json()` `xml()` `redirect()` เป็นต้น ที่ทำให้การปิดคำขอทำการผ่าน โค้งต่อแก่ลูกฟ้า 
ใน middleware เราสามารถรับ request และได้รับ response หลังจากการประมวลผลคอนโทรลเลอร์เรียบร้อยแล้ว ดังนั้น middleware มีส่วนประกอบสามส่วน
1. ขั้นตอนที่ส่งผ่านของการร้องขอ หรือขั้นตอนก่อนการประมวลผลของการร้องขอ
2. ขั้นตอนการประมวลผลของคอนโทรลเลอร์ หรือขั้นตอนการประมวลผลของการร้องขอ
3. ขั้นตอนที่ส่งออกของการตอบคืน หรือขั้นตอนหลังจากการประมวลผลของการร้องขอ

สามขั้นตอนนี้ใน middleware จะมีหน้าที่ดังนี้
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
        echo 'นี่คือขั้นตอนที่ส่งผ่านของการร้องขอ หรือขั้นตอนก่อนการประมวลผลของการร้องขอ';

        $response = $handler($request); // ดำเนินการต่อไปเพื่อวางใจไปยังไคลจ์เพื่อได้รับการควบคุมให้ได้ response

        echo 'นี่คือขั้นตอนที่ส่งออกของการตอบคืน หรือขั้นตอนหลังจากการประมวลผลของการร้องขอ';

        return $response;
    }
}
```
 
## ตัวอย่าง: Middleware การตรวจสอบความถูกต้องของตัวตน
สร้างไฟล์ `app/middleware/AuthCheckTest.php` (หากไม่มีโฟลเดอร์โปรดสร้างโดยตนเอง) ดังนี้:
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
            // เข้าสู่ระบบแล้ว ทำการเรียก request ต่อไปเพื่อได้การการควบคุมให้ได้ response
            return $handler($request);
        }

        // ทำการดึงรายการว่า method ใดไม่ต้องเข้าสู่ระบบดid้วยการทำสะทอง
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // แอกเซ็สที่เข้าถึง method นี้ต้องเข้าสู่ระบบ
        if (!in_array($request->action, $noNeedLogin)) {
            // ป้องกัน request โดยการส่ง response ที่วิ่งต่อหน้าเพื่อหยุดการควบคุม
            return redirect('/user/login');
        }

        // ไม่ต้องเข้าสู่ระบบ ทำการเรียก request ต่อไปเพื่อได้การควบคุมให้ได้ response
        return $handler($request);
    }
}
```

สร้างคอนโทรลเลอร์ `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * method ที่ไม่ต้องเข้าสู่ระบบ
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
> ใน `$noNeedLogin` ได้บันทึกรายการ method ที่ไม่ต้องเข้าสู่ระบบ

ใน `config/middleware.php` เพื่อเพิ่ม middleware ที่มีอยู่ทั้งหมดดังนี้:
```php
return [
    // มิดเดิลแวร์ทั่วไป
    '' => [
        // ... ที่นี่ส่วนของ middleware อื่นๆ ที่ยุบไป
        app\middleware\AuthCheckTest::class,
    ]
];
```

ด้วยการมี middleware การตรวจสอบความถูกต้องของตัวตน เราสามารถให้ความสนใจกับการเขียนโค้ดธุรกิจของเราในชั้นคอนโทรลเลอร์โดยไม่ต้องกังวลเรื่องการผู้ใช้เข้าสู่ระบบหรือไม่

## ตัวอย่าง: Middleware การร้องขอกว้าง 
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
        // หากเป็นการร้องขอ options จะคืนการควบคุมออกไป มิฉะนั้นทำการควบคุมต่อไปและได้รับการควบคุมให้ได้ response
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // เพิ่ม http header ที่เกี่ยวกับ cross-origin ให้กับ response
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

> **เคล็ดลับ**
> การร้องขอกว้างอาจสร้างการร้องขอของ options ซึ่งเราไม่ต้องการการร้องขอ options เข้าเข้าไปยังคอนโทรลเลอร์เช่นกัน ดังนั้นเราให้การร้องขอ options ส่ง response ที่ว่างๆ (`response('')`) มาเพื่อทำการสะท้องการร้องขอ 
> ถ้าอินเทอร์เฟซของคุณต้องการการตั้งเส้นทาง กรุณาใช้ `Route::any(..)` หรือ `Route::add(['POST', 'OPTIONS'], ..)`

ใน `config/middleware.php` เพื่อเพิ่ม middleware ที่มีอยู่ทั้งหมดดังนี้:
```php
return [
    // มิดเดิลแวร์ทั่วไป
    '' => [
        // ... ที่นี่ส่วนของ middleware อื่นๆ ที่ยุบไป
        app\middleware\AccessControlTest::class,
    ]
];
```

> **หมายเหตุ**
> หากการร้องขอ ajax ที่กำหนดไว้มีส่วนของ header ที่กำหนดเอง คุณต้องเพิ่มฟิลด์ `Access-Control-Allow-Headers` ใน middleware เพื่อรวม header ที่กำหนดเอง เนื่องจากไม่งั้นจะเกิดข้อผิดพลาด ` Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`


## คำอธิบาย
  
 - Middleware ถูกแบ่งเป็น middleware ทั่วไป, middleware ของแอพพลิเคชั่น (middleware ของแอพพลิเคชั่นใช้ได้เฉพาะในโหมดแอพพลิเคชั่นหลายตัว, ดูเพิ่มเติมที่[multiapp.md](multiapp.md)) และ middleware ของเส้นทาง
 - ปัจจุบันไม่รองรับ middleware ของคอนโทรลเลอร์คนละตัว (แต่สามารถใช้ `$request->controller` เพื่อทำฟังก์ชั่น middleware ในคอนโทรลเลอร์ได้)
 - ที่เก็บไฟล์การกำหนด middleware อยู่ที่ `config/middleware.php`
 - การกำหนด middleware ทั่วไปอยู่ใน key `''`
 - การกำหนด middleware ของแอพพลิเคชั่นอยู่ในชื่อของแอพพลิเคชั่นเฉพาะ เช่น

```php
return [
    // มิดเดิลแวร์ทั่วไป
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // middleware ของแอพพลิเคชั่น (middleware ของแอพพลิเคชั่นใช้ได้เฉพาะในโหมดแอพพลิเคชั่นหลายตัว)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware ของเส้นทาง

เราสามารถกำหนด middleware สำหรับเส้นทางเฉพาะหรือกลุ่มเส้นทางใดๆ
ตัวอย่างเช่นใน `config/route.php` เพื่อเพิ่ม middleware ในเส้นทางดังนี้:
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
## การสร้างฟังก์ชันกลางของ middleware พร้อมพาไป

> **หมายเหตุ**
> คุณสามารถใช้คุณลักษณะนี้ได้เมื่อเวอร์ชันของ webman-framework >= 1.4.8

ตั้งแต่เวอร์ชัน 1.4.8 เป็นต้นมา ไฟล์การกำหนดค่ารองรับการสร้างฟังก์ชันกลางโดยตรงหรือฟังก์ชันไม่มีชื่อ เพื่อง่ายต่อการส่งค่าพารามิเตอร์ผ่านฟังก์ชันกลาง
เช่น `config/middleware.php` สามารถกำหนดได้ดังนี้
```php
return [
    // ฟังก์ชันกลางทั่วไป
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // ฟังก์ชันกลางของแอพพลิเคชัน (ฟังก์ชันกลางของแอพพลิเคชันมีผลอย่างกว้างขวางเมื่อใช้ในโหมดหลายแอพ)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

ตลอดจนการกำหนดฟังก์ชันกลางในเส้นทางก็สามารถส่งพารามิเตอร์ผ่านฟังก์ชันกลางได้ เช่น `config/route.php`
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## ลำดับการทำงานของฟังก์ชันกลาง
 - ลำดับการทำงานของฟังก์ชันกลางคือ `ฟังก์ชันกลางทั่วไป`->`ฟังก์ชันกลางของแอพพลิเคชัน`->`ฟังก์ชันกลางของเส้นทาง`
 - เมื่อมีฟังก์ชันกลางทั่วไปหลายตัว จะตามการกำหนดฟังก์ชันกลางจริง ๆ (ฟังก์ชันกลางของแอพพลิเคชันและฟังก์ชันกลางของเส้นทางก็เช่นกัน)
 - คำขอที่ส่งค่าสถานะ 404 จะไม่เรียกใช้ฟังก์ชันกลางใด ๆ รวมทั้งฟังก์ชันกลางทั้วไปด้วย

## ส่งประเภทสำหรับฟังก์ชันกลางไปยังคอนโทรลเลอร์

**การกำหนดเส้นทาง `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**ฟังก์ชันกลาง (สมมติว่าเป็นฟังก์ชันกลางทั่วไป)**
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
        // ทำธรรมดา ๆ  $request->route จะเป็น null ดังนั้นคุณจึงต้องตรวจสอบว่า $request->route เป็นว่างหรือไม่
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## การส่งประเภทจากฟังก์ชันกลางไปยังคอนโทรลเลอร์

บางครั้งคอนโทรลเลอร์อาจต้องการใช้ข้อมูลที่ได้จากฟังก์ชันกลาง ในกรณีนี้เราสามารถทำด้วยการเพิ่มคุณลักษณะให้กับอ็อบเจ็กต์ `$request` เพื่อส่งมายังคอนโทรลเลอร์ได้ เช่น:

**ฟังก์ชันกลาง**
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

**คอนโทรลเลอร์:**
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

## การรับข้อมูลเส้นทางปัจจุบันจากฟังก์ชันกลาง

> **หมายเหตุ**
> คุณสามารถใช้คุณลักษณะนี้ได้เมื่อเวอร์ชันของ webman-framework >= 1.3.2

เราสามารถใช้ `$request->route` เพื่อรับอ็อบเจ็กต์เส้นทางและสามารถเรียกใช้เมทอดที่เกี่ยวข้องเพื่อรับข้อมูลที่เกี่ยวข้อง

**การกำหนดเส้นทาง**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**ฟังก์ชันกลาง**
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
        // ถ้าคำขอไม่ตรงกับเส้นทางที่มี (ยกเว้นเส้นทางประเภทเริ่มต้น) จะได้ $request->route เป็น null
        // ถ้าเบราว์เซอร์ทำการเข้าถึงที่อยู่ /user/111, จะพิมพ์ข้อมูลดังต่อไปนี้
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
> เมทอด `$route->param()` ต้องการ webman-framework >= 1.3.16

## การรับข้อมูลข้อผิดพลาดจากฟังก์ชันกลาง

> **หมายเหตุ**
> คุณสามารถใช้คุณลักษณะนี้ได้เมื่อเวอร์ชันของ webman-framework >= 1.3.15

เมื่อดำเนินการธุรกรรมธุรกิจอาจเกิดข้อผิดพลาด ในฟังก์ชันกลางคุณสามารถใช้ `$response->exception()` เพื่อรับข้อผิดพลาด

**การกำหนดเส้นทาง**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**ฟังก์ชันกลาง:**
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

## ฟังก์ชันกลางเซูเปอร์ที่กว้าง

> **หมายเหตุ**
> คุณสามารถใช้คุณลักษณะนี้ได้เมื่อเวอร์ชันของ webman-framework >= 1.5.16

ฟังก์ชันกลางทั่วไปของโครงการหลักมีผลต่อโครงการหลักเท่านั้น โดยไม่กระทบบน [ปลั๊กอินแอปพลิเคชัน](app/app.md) อย่างไรก็ตาม เราอาจต้องการกำหนดฟังก์ชันกลางที่ส่งผลต่อทุกๆ ปลั๊กอินเอางจะสามารถใช้ฟังก์ชันกลางเซูเปอร์ได้

ใน `config/middleware.php` คุณสามารถกำหนดได้ดังนี้:
```php
return [
    '@' => [ // เพิ่มฟังก์ชันกลางทว่าหลักและปลั๊กอินทั้งหมด
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // รายการฟังก์ชันกลางผ่านโครงการหลักเท่านั้น
];
```

> **เพระะน**
> ฟังก์ชันกลางเซูเปอร์ `@` สามารถกำหนดได้ไม่เพียงแค่ในโครงการหลักเท่านั้น แต่ยังสามารถกำหนดได้ในปลั๊กอินใดๆ อย่างไรก็ตาม เมื่อกำหนดการกำหนดไว้ในปลั๊กอิน เช่น `plugin/ai/config/middleware.php` การกำหนดฟังก์ชันกลางเซูเปอร์เป็นอย่างไร ก็จะส่งผลกระทบต่อโครงการหลักและปลั๊กอินทั้งหมด

## การเพิ่มฟังก์ชันกลางให้กับแอปพลิเคชันบางแอป

> **หมายเหตุ**
> คุณสามารถใช้คุณลักษณะนี้ได้เมื่อเวอร์ชันของ webman-framework >= 1.5.16

บางครั้งเราต้องการเพิ่มฟังก์ชันกลางให้กับ [ปลั๊กอินแอปพลิเคชัน](app/app.md) บางตัวโดยที่ไม่ต้องแก้ไขโค้ดของปลั๊กอิน (เพราะการอัพเกรดจะเป็นการแทนที่) ในกรณีนี้ เราสามารถกำหนดได้ในโครงการหลักเพื่อเพิ่มฟังก์ชันกลางให้กับแอปพลิเคชัน

ใน `config/middleware.php` คุณสามารถกำหนดได้ดังนี้:
```php
return [
    'plugin.ai' => [], // เพิ่มฟังก์ชันกลางให้กับแอปพลิเคชัน ai
    'plugin.ai.admin' => [], // เพิ่มฟังก์ชันกลางให้กับโมดูล admin ของแอปพลิเคชัน ai
];
```

> **เทคนิคอ**
> แน่นอนว่ายังสามารถกำหนดการเป็นแบบเดียวกับการเข้ากระทำต่อตัพเกรดที่กล่าวไปแล้ว ตัวอย่างเช่น `plugin/foo/config/middleware.php` ที่เพิ่มคำสั่งการกำหนดไว้จะส่งผลกระทบต่อการทำงานของแอปพลิเคชัน ai ได้
