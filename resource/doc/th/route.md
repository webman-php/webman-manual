เว็บแมน (webman) เป็นเฟรมเวิร์ก PHP ที่มีประสิทธิภาพสูงที่สร้างขึ้นบน workerman ดังนั้นข้าพเจ้าได้รับหน้าที่ให้คำแนะนำด้านเทคโนโลยีและพัฒนาระบบก่อนที่จะเริ่มต้นการทำงานกับ Wokerman บนพื้นฐานของ PHP 7.1+
 กรอบ (framework) ผลิตาจะย้ำในการบริการร่วมกับ Web Server ที่ต่ออยู่อย่างมีประสิทธิภาพ:

	- ไม่ต้องการ Worker 的 PHP HTTP Server，แต่ก็สามารถใช้เมื่อต้องการ

	- ไม่ต้องการ Kernel HTTP Server to PHP 如 PeachServer

	- Web Server ASP.NET, ASP.NET Core, Node.js และอื่นๆ 

	- แชต (Chart) ด้วย Swoole Tracker

	- ผาสะอันตรสื่จรขอมูลข้อควอนื่จงียนำ และบันทส่ม้้างขาทยาตขสอไลยังยัื่อููถุ้้ำ้ทบัตรต้อ้ง่ำำท (WebSocket + SSE) 

	- การจัดสตรางแบค เขว่แอใบปัดู๊ด้าบด้าตั5อ และขือ้ณุ้ับอใ้ำบดร้า้า

	- หสีสูฉีั้ืงีด่็แีกีส่ีบแลป ้ีด่ิ้กียฉงีบเหีีฉ้ดการส่ีกี่็ีงดอูก534ียีดด้า็็ะดีบีแดุ-ุดรี้ด้ีด

## กฎเส้นทางการเริ่มต้น

กฎเส้นทางการเริ่มต้นของ webman คือ `http://127.0.0.1:8787/{คอนโทรลเลอร์}/{การกระทำ}`

คอนโทรลเลอร์เริ่มต้นคือ `app\controller\IndexController` และการกระทำเริ่มต้นคือ `index`

เช่น การเข้าถึง:
- `http://127.0.0.1:8787` จะเข้าถึง `index` ของคลาส `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` จะเข้าถึง `index` ของคลาส `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` จะเข้าถึง `test` ของคลาส `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` จะเข้าถึง `test` ของคลาส `app\admin\controller\FooController` (อ่านเพิ่มเติมที่ [multiapp.md](multiapp.md))

นอกจากนี้ webman ตั้งแต่เวอร์ชัน 1.4 เป็นต้นมารองรับเส้นทางเริ่มต้นที่ซับซ้อนมากขึ้น เช่น
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

เมื่อคุณต้องการเปลี่ยนเส้นทางของคำร้องขอ กรุณาแก้ไขไฟล์กำหนดค่า `config/route.php` 

ถ้าคุณต้องการปิดเส้นทางเริ่มต้น ให้เพิ่มค่าตัวกำหนดดังนี้ในไฟล์กำหนดค่า `config/route.php` บรรทัดสุดท้าย:
```php
Route::disableDefaultRoute();
```
## เส้นทางปิด
เพิ่มโค้ดเส้นทางต่อไปนี้ใน `config/route.php`
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});
```
> **โปรดทราบ**
> เนื่องจากฟังก์ชันปิดไม่ได้เกี่ยวข้องกับคอนโทรลเลอร์ใด ๆ ค่า `$request->app` `$request->controller` `$request->action` จะเป็นสตริงว่างทั้งหมด

เมื่อเข้าถึง URL เป็น `http://127.0.0.1:8787/test` จะคืนค่าเป็นสตริง `test`

> **โปรดทราบ**
> เส้นทางต้องเริ่มต้นด้วย `/` เช่น

```php
use support\Request;
// ใช้ไม่ถูกต้อง
Route::any('test', function (Request $request) {
    return response('test');
});

// ใช้ถูกต้อง
Route::any('/test', function (Request $request) {
    return response('test');
});
```

## เส้นทางคลาส
เพิ่มโค้ดเส้นทางต่อไปนี้ใน `config/route.php`
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
เมื่อเข้าถึง URL เป็น `http://127.0.0.1:8787/testclass` จะคืนค่าวิธีการทดสอบของคลาส `app\controller\IndexController`

## เส้นทางแอนโนเทชัน

กำหนดเส้นทางผ่านแอนโนเทชันบนเมธอดควบคุม ไม่ต้องกำหนดค่าใน `config/route.php`

> **โปรดทราบ**
> ฟีเจอร์นี้ต้องใช้ webman-framework >= v2.2.0

### การใช้งานพื้นฐาน

```php
namespace app\controller;
use support\annotation\route\Get;
use support\annotation\route\Post;

class UserController
{
    #[Get('/user/{id}')]
    public function show($id)
    {
        return "user $id";
    }

    #[Post('/user')]
    public function store()
    {
        return 'created';
    }
}
```

แอนโนเทชันที่ใช้ได้: `#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]` (วิธีใดก็ได้) เส้นทางต้องขึ้นต้นด้วย `/` พารามิเตอร์ที่สองสามารถระบุชื่อเส้นทาง สำหรับ `route()` สร้าง URL

### แอนโนเทชันไม่มีพารามิเตอร์: จำกัดวิธี HTTP ของเส้นทางเริ่มต้น

ไม่มีเส้นทาง จะจำกัดเฉพาะวิธี HTTP ที่อนุญาตสำหรับการดำเนินการนั้น ยังใช้เส้นทางเริ่มต้นอยู่:

```php
#[Post]
public function create() { ... }  // อนุญาตเฉพาะ POST เส้นทางยังคงเป็น /user/create

#[Get]
public function index() { ... }   // อนุญาตเฉพาะ GET
```

สามารถรวมแอนโนเทชันหลายอันเพื่ออนุญาตหลายวิธี:

```php
#[Get]
#[Post]
public function form() { ... }  // อนุญาต GET และ POST
```

วิธีที่ไม่ได้ประกาศในแอนโนเทชันจะส่งกลับ 405

แอนโนเทชันที่มีเส้นทางหลายอันจะลงทะเบียนเป็นเส้นทางแยก: `#[Get('/a')] #[Post('/b')]` จะสร้างเส้นทาง GET /a และ POST /b

### คำนำหน้ากลุ่มเส้นทาง

ใช้ `#[RouteGroup]` บนคลาสเพื่อเพิ่มคำนำหน้าให้เส้นทางเมธอดทั้งหมด:

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // เส้นทางจริง /api/v1/user/{id}
    public function show($id) { ... }
}
```

### วิธี HTTP ที่กำหนดเองและชื่อเส้นทาง

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### มิดเดิลแวร์

`#[Middleware]` บน controller หรือ method จะส่งผลต่อเส้นทางแอนโนเทชัน ใช้เหมือน `support\annotation\Middleware`

## พารามิเตอร์ของเส้นทาง
หากมีพารามิเตอร์ในเส้นทาง ใช้ `{key}` เพื่อตรงคู่ค่า ผลลัพธ์ที่ตรงจะถูกส่งไปยังพารามิเตอร์ของวิธีควบคุมที่เกี่ยวข้อง (เริ่มจากพารามิเตอร์ที่สองขึ้นไปตามลำดับ) เช่น: 
```php
// ตรงกับ /user/123 และ /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('รับพารามิเตอร์ '.$id);
    }
}
```

ตัวอย่างเพิ่มเติม:
```php
use support\Request;
// ตรงต่อ /user/123, ไม่ตรงต่อ /user/abc
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// ตรง /user/foobar, ไม่ตรง /user/foo/bar
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// ตรง /user /user/123 และ /user/abc   [] แสดงว่าเลือกได้
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// ตรงคำร้องขอที่มีคำนำหน้า /user/
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// ตรงทั้งหมดในคำร้องขอ options   : สำหรับใส่ regex
Route::options('[{path:.+}]', function () {
    return response('');
});
```

สรุปการใช้งานขั้นสูง

> ไวยากรณ์ `[]` ในเส้นทาง Webman ใช้หลักในการจัดการส่วนเส้นทางที่เลือกได้ หรือการจับคู่เส้นทางแบบไดนามิก ให้คุณกำหนดโครงสร้างเส้นทางและกฎการจับคู่ที่ซับซ้อนขึ้นได้
>
> `:` ใช้กำหนด regex

## กลุ่มเส้นทาง
บางครั้งเส้นทางประกอบด้วยคำขึ้นต้นที่มีจำนวนมาก เราสามารถใช้กลุ่มเส้นทางเพื่อกำหนดให้ง่ายขึ้น เช่น:
```php
use support\Request;
Route::group('/blog', function () {
   Route::any('/create', function (Request $request) {return response('create');});
   Route::any('/edit', function (Request $request) {return response('edit');});
   Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
});
```
เทียบเท่ากับ:
```php
Route::any('/blog/create', function (Request $request) {return response('create');});
Route::any('/blog/edit', function (Request $request) {return response('edit');});
Route::any('/blog/view/{id}', function (Request $request, $id) {return response("view $id");});
```

การซ้อนกลุ่มการใช้
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
});
```
## เส้นทางกลางเส้นทาง

เราสามารถกำหนดเส้นทางกลางเส้นทางสำหรับเส้นทางเดียวหรือกลุ่มของเส้นทางได้
เช่น:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# ตัวอย่างที่ใช้ผิด (เมื่อ webman-framework >= 1.5.7 ใช้ได้)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# ตัวอย่างที่ถูกต้อง
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```
## เส้นทางของทรัพยากร

```php
Route::resource('/test', app\controller\IndexController::class);

// ระบุเส้นทางของทรัพยากร
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// เส้นทางของทรัพยากรที่ไม่ได้ระบุ
// เช่น เข้าถึง notify ใน URL ที่เป็นทั้ง any คือ /test/notify หรือ /test/notify/{id} ชื่อ routeName คือ test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| คำสั่ง   | URI                 | การดำเนินการ   | ชื่อเส้นทาง    |
|--------|---------------------|----------|---------------|
| GET    | /test               | ดึงข้อมูลทั้งหมด    | test.index    |
| GET    | /test/create        | สร้าง   | test.create   |
| POST   | /test               | บันทึกข้อมูล    | test.store    |
| GET    | /test/{id}          | ดูข้อมูล     | test.show     |
| GET    | /test/{id}/edit     | แก้ไข     | test.edit     |
| PUT    | /test/{id}          | ปรับปรุง   | test.update   |
| DELETE | /test/{id}          | ลบ  | test.destroy  |
| PUT    | /test/{id}/recovery | กู้คืน   | test.recovery |

## การสร้าง URL
> **หมายเหตุ** 
> ยังไม่รองรับการสร้าง URL โดยเส้นทางที่ซ้อนกัน  

ตัวอย่างเส้นทาง:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
เราสามารถใช้วิธีดังต่อไปนี้ในการสร้าง URL สำหรับเส้นทางนี้
```php
route('blog.view', ['id' => 100]); // ผลลัพธ์เป็น /blog/100
```
เมื่อใช้วิธีนี้ในการสร้าง URL ในมุมมอง เมื่อกฎของเส้นทางมีการเปลี่ยนแปลง URL ก็จะถูกสร้างขึ้นโดยอัตโนมัติ ซึ่งสามารถป้องกันการเปลี่ยนแปลงของไฟล์มุมมองที่เริ่มขึ้นจากการเปลี่ยนแปลงของเส้นทางได้
## การรับข้อมูลเส้นทาง

ผ่าน `$request->route` ในอ็อบเจกต์ เราสามารถรับข้อมูลของเส้นทางที่ถูกเรียกใช้ในปัจจุบันได้ เช่น

```php
$route = $request->route; // เทียบเท่ากับ $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param());
}
```

> **หมายเหตุ**
> หากเส้นทางที่ร้องขอไม่ตรงกับเส้นทางใดที่ได้กำหนดไว้ใน config/route.php แล้ว `$request->route` จะเป็นค่า null ซึ่งหมายความว่าเมื่อเข้าไปที่เส้นทางต้นสักเส้นทาง `$request->route` จะเป็น null
## การจัดการกับ 404
เมื่อไม่พบเส้นทาง ระบบจะส่งค่าสถานะ 404 และแสดงเนื้อหา 404 ที่เกี่ยวข้อง

หากนักพัฒนาต้องการที่จะทำการกลับไปยังโหมดการทำงานเมื่อไม่พบเส้นทาง สามารถใช้ฟังก์ชันสำรองทางเส้นทาง `$callback` ที่ webman มีให้ ตัวอย่างเช่น การเปลี่ยนเส้นทางที่ไม่พบไปที่หน้าแรก
```php
Route::fallback(function(){
    return redirect('/');
});
```
หรือเช่น ถ้าไม่พบเส้นทางจะส่งค่า JSON ซึ่งเป็นวิธีที่เหมาะสำหรับ webman เมื่อใช้สำหรับอินเตอร์เฟซ API
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## เพิ่มมิดเดิลแวร์ให้ 404

โดยปกติคำร้องขอ 404 จะไม่ผ่านมิดเดิลแวร์ใดๆ หากต้องการเพิ่มมิดเดิลแวร์ให้คำร้องขอ 404 ดูโค้ดต่อไปนี้:
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

ลิงค์ที่เกี่ยวข้อง [การกำหนดหน้า 404 500](others/custom-error-page.md)

## ปิดใช้งานเส้นทางเริ่มต้น

```php
// ปิดเส้นทางเริ่มต้นของโปรเจกต์หลัก ไม่กระทบปลั๊กอิน
Route::disableDefaultRoute();
// ปิดเส้นทาง admin ของโปรเจกต์หลัก ไม่กระทบปลั๊กอิน
Route::disableDefaultRoute('', 'admin');
// ปิดเส้นทางเริ่มต้นของปลั๊กอิน foo ไม่กระทบโปรเจกต์หลัก
Route::disableDefaultRoute('foo');
// ปิดเส้นทาง admin ของปลั๊กอิน foo ไม่กระทบโปรเจกต์หลัก
Route::disableDefaultRoute('foo', 'admin');
// ปิดเส้นทางเริ่มต้นของคอนโทรลเลอร์ [\app\controller\IndexController::class, 'index']
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## แอนโนเทชันปิดเส้นทางเริ่มต้น

เราใช้แอนโนเทชันปิดเส้นทางเริ่มต้นของคอนโทรลเลอร์ได้ เช่น:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

#[DisableDefaultRoute]
class IndexController
{
    public function index()
    {
        return 'index';
    }
}
```

เช่นกัน เราปิดเส้นทางเริ่มต้นของเมธอดคอนโทรลเลอร์ด้วยแอนโนเทชันได้ เช่น:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

class IndexController
{
    #[DisableDefaultRoute]
    public function index()
    {
        return 'index';
    }
}
```

## อินเทอร์เฟซเส้นทาง
```php
// กำหนดเส้นทางที่ต้องการใดก็ได้ที่ $uri 
Route::any($uri, $callback);
// กำหนดเส้นทาง get ของ $uri
Route::get($uri, $callback);
// กำหนดเส้นทาง post ของ $uri
Route::post($uri, $callback);
// กำหนดเส้นทาง put ของ $uri
Route::put($uri, $callback);
// กำหนดเส้นทาง patch ของ $uri
Route::patch($uri, $callback);
// กำหนดเส้นทาง delete ของ $uri
Route::delete($uri, $callback);
// กำหนดเส้นทาง head ของ $uri
Route::head($uri, $callback);
// กำหนดเส้นทาง options ของ $uri
Route::options($uri, $callback);
// กำหนดเส้นทางที่มีการร้องขอหลายแบบพร้อมกัน
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// เส้นทางของการจัดกลุ่ม
Route::group($path, $callback);
// เส้นทางของทรัพยากร
Route::resource($path, $callback, [$options]);
// ปิดใช้งานเส้นทาง
Route::disableDefaultRoute($plugin = '');
// สำรองทางเส้นทาง, หลักการเส้นทางไว้เป็นการส่ง
Route::fallback($callback, $plugin = '');
// ดึงข้อมูลเส้นทางทั้งหมด
Route::getRoutes();
```
หากไม่มีเส้นทางที่เชื่อมโยง (รวมถึงเส้นทางตั้งตาย) และไม่ได้ส่งทางสำรองแล้ว จะคืนค่าสถานะ 404 ทันที
## ไฟล์กำหนดเส้นทางหลายแบบ
หากคุณต้องการใช้งานไฟล์กำหนดเส้นทางหลายแบบในการจัดการเส้นทาง เช่น [การประยุกต์ใช้หลายแอพ](multiapp.md) เมื่อแอพแต่ละตัวมีไฟล์กำหนดเส้นทางของตัวเอง ในกรณีนี้ คุณสามารถโหลดไฟล์กำหนดเส้นทางของภายนอกโดยวิธี `require`.
ตัวอย่าง `config/route.php`
```php
<?php

// โหลดไฟล์กำหนดเส้นทางที่ตั้งตาย
require_once app_path('admin/config/route.php');
// โหลดไฟล์กำหนดเส้นทางที่ตั้งตาย
require_once app_path('api/config/route.php');

```
