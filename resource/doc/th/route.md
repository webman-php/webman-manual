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
Route::any('/test', function ($request) {
    return response('test');
});
```
> **โปรดทราบ**
> เนื่องจากฟังก์ชันปิดไม่ได้เกี่ยวข้องกับคอนโทรลเลอร์ใด ๆ ค่า `$request->app` `$request->controller` `$request->action` จะเป็นสตริงว่างทั้งหมด

เมื่อเข้าถึง URL เป็น `http://127.0.0.1:8787/test` จะคืนค่าเป็นสตริง `test`

> **โปรดทราบ**
> เส้นทางต้องเริ่มต้นด้วย `/` เช่น

```php
// ใช้ไม่ถูกต้อง
Route::any('test', function ($request) {
    return response('test');
});

// ใช้ถูกต้อง
Route::any('/test', function ($request) {
    return response('test');
});
```

## เส้นทางคลาส
เพิ่มโค้ดเส้นทางต่อไปนี้ใน `config/route.php`
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
เมื่อเข้าถึง URL เป็น `http://127.0.0.1:8787/testclass` จะคืนค่าวิธีการทดสอบของคลาส `app\controller\IndexController`
## พารามิเตอร์ของเส้นทาง
หากมีพารามิเตอร์ในเส้นทาง ใช้ `{key}` เพื่อตรงคู่ค่า ผลลัพธ์ที่ตรงจะถูกส่งไปยังพารามิเตอร์ของวิธีควบคุมที่เกี่ยวข้อง (เริ่มจากพารามิเตอร์ที่สองขึ้นไปตามลำดับ) เช่น: 
```php
// ตรงกับ /user/123 และ /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('รับพารามิเตอร์ '.$id);
    }
}
```

ตัวอย่างเพิ่มเติม:
```php
// ตรงต่อ /user/123, ไม่ตรงต่อ /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// ตรง /user/foobar, ไม่ตรง /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// ตรงทั้งหมดในคำร้องขอ options
Route::options('[{path:.+}]', function () {
    return response('');
});
```
## กลุ่มเส้นทาง
บางครั้งเส้นทางประกอบด้วยคำขึ้นต้นที่มีจำนวนมาก เราสามารถใช้กลุ่มเส้นทางเพื่อกำหนดให้ง่ายขึ้น เช่น:
```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
เทียบเท่ากับ:
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

การซ้อนกลุ่มการใช้
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
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
   Route::any('/view/{id}', function ($request, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **โปรดทราบ**:
> ใน webman-framework <= 1.5.6  เมื่อใช้ `->middleware()` กลางเส้นทางสำหรับกลุ่มหลังจากนั้น เส้นทางปัจจุบันต้องอยู่ภายใต้กลุ่มปัจจุบัน

```php
# ตัวอย่างที่ใช้ผิด (เมื่อ webman-framework >= 1.5.7 ใช้ได้)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
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
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
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

> **หมายเหตุ**
> ต้องติดตั้ง webman-framework เวอร์ชัน 1.3.2 ขึ้นไป

ผ่าน `$request->route` ในอ็อบเจกต์ เราสามารถรับข้อมูลของเส้นทางที่ถูกเรียกใช้ในปัจจุบันได้ เช่น

```php
$route = $request->route; // เทียบเท่ากับ $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // คุณลักษณะนี้ใช้ได้ก็ต่อเมื่อ webman-framework >= 1.3.16
}
```

> **หมายเหตุ**
> หากเส้นทางที่ร้องขอไม่ตรงกับเส้นทางใดที่ได้กำหนดไว้ใน config/route.php แล้ว `$request->route` จะเป็นค่า null ซึ่งหมายความว่าเมื่อเข้าไปที่เส้นทางต้นสักเส้นทาง `$request->route` จะเป็น null
## การจัดการกับ 404
เมื่อไม่พบเส้นทาง ระบบจะส่งค่าสถานะ 404 และแสดงเนื้อหาของไฟล์ `public/404.html`.

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

ลิงค์ที่เกี่ยวข้อง [การกำหนดหน้า 404 500](others/custom-error-page.md)
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
