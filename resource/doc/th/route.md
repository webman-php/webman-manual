## เส้นทาง (Routes)
## กฎเส้นทางเริ่มต้น
กฎเส้นทางเริ่มต้นของ webman คือ `http://127.0.0.1:8787/{คอนโทรลเลอร์}/{การกระทำ}`

คอนโทรลเลอร์เริ่มต้นคือ `app\controller\IndexController` และการกระทำเริ่มต้นคือ `index`

ตัวอย่างการเข้าถึง:
- `http://127.0.0.1:8787` จะเข้าถึงคลาส `IndexController` และเมทอด `index`
- `http://127.0.0.1:8787/foo` จะเข้าถึงคลาส `FooController` และเมทอด `index`
- `http://127.0.0.1:8787/foo/test` จะเข้าถึงคลาส `FooController` และเมทอด `test`
- `http://127.0.0.1:8787/admin/foo/test` จะเข้าถึงคลาส `FooController` ใน `app\admin\controller\` และเมทอด `test` (ดูเพิ่มเติมที่ [แอปพลิเคชั่นหลายอัน](multiapp.md))

นอกจากนี้ ตั้งแต่ webman เวอร์ชัน 1.4 เริ่มต้น สนับสนุนเส้นทางเริ่มต้นที่ซับซ้อนมากขึ้น เช่น
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

เมื่อคุณต้องการเปลี่ยนเส้นทางของคำขอบางอย่าง โปรดเปลี่ยนแก้ไขไฟล์กำหนดเส้นทาง `config/route.php`

หากคุณต้องการปิดเส้นทางเริ่มต้น ให้เพิ่มการกำหนดเช่นนี้ที่บรรทัดสุดท้ายของไฟล์กำหนดเส้นทาง `config/route.php`:
```php
Route::disableDefaultRoute();
```

## เส้นทางคลอเจอร์
เพิ่มโค้ดเส้นทางที่ใช้การปิดฟังก์ชันชัตเปิด
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **ข้อควรระวัง**
> เนื่องจากฟังก์ชันปิดไม่ได้เป็นส่วนหนึ่งของคอนโทรลเลอร์ ดังนั้น `$request->app` `$request->controller` `$request->action` จะเป็นสตริงว่างทั้งหมด

เมื่อเข้าถึงที่อยู่เว็บเป็น `http://127.0.0.1:8787/test` จะคืนค่าสตริง `test` 

> **ข้อควรระวัง**
> เส้นทางจำเป็นต้องเริ่มต้นด้วย `/` เช่น
```php
// ปรับใช้ไม่ถูกต้อง
Route::any('test', function ($request) {
    return response('test');
});

// ปรับใช้ถูกต้อง
Route::any('/test', function ($request) {
    return response('test');
});
```

## เส้นทางคลาส
เพิ่มโค้ดเส้นทางดังนี้ในไฟล์กำหนดเส้นทาง `config/route.php`
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
เมื่อเข้าถึงที่อยู่เว็บเป็น `http://127.0.0.1:8787/testclass` จะคืนค่าจากเมทอด `test` ของคลาส `IndexController` ใน `app\controller\`

## พารามิเตอร์ของเส้นทาง
หากมีพารามิเตอร์ในเส้นทาง ให้ใช้ `{key}` เพื่อจับคู่ ผลลัพธ์จะถูกส่งไปยังพารามิเตอร์ของเมทอดคอนโทรลเลอร์ (เริ่มจากพารามิเตอร์ตัวที่สองเป็นต้นไป) เช่น
```php
// จับคู่กับ /user/123 หรือ /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('รับพารามิเตอร์'.$id);
    }
}
```

ตัวอย่างเพิ่มเติม:
```php
// จับคู่กับ /user/123 แต่ไม่กับ /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// จับคู่กับ /user/foobar แต่ไม่กับ /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// จับคู่กับ /user /user/123 และ /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// จับคู่ทุกคำขอ options
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## กลุ่มเส้นทาง
บางครั้งมีเส้นทางที่มีคำนำหน้าเดียวกันมากมาย สามารถใช้กลุ่มเส้นทางเพื่อทําให้การกําหนดเส้นทางกระทําได้ง่ายขึ้น ตัวอย่างเช่น:
```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
เทียบเท่ากับ
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

ใช้กลุ่มซ้อน ๆ กัน
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## มีดกลายทางเส้นทาง
เราสามารถกําหนดมีดกลายทางไปยังเส้นทางหนึ่งหรือกลุ่มเส้นทาง
ตัวอย่างเช่น:
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

> **ข้อควรระวัง**: 
> ใน webman-framework <= 1.5.6 เมื่อใช้ `->middleware()` ใน group นอกจากฑ์ต่อจากมีดกลายทาง การกําหนดเส้นทางปัจจุบันจะต้องอยู่ในกลุ่มนั้น <br>
ตัวอย่างการใช้ไม่ถูกต้อง (การใช้วิธีการนี้ถูกต้องใน webman-framework >= 1.5.7)
```php
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
# ตัวอย่างการใช้ถูกต้อง
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

## เส้นทางทรัพยากร
```php
Route::resource('/test', app\controller\IndexController::class);

// กําหนดเส้นทางทรัพยากร
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// เส้นทางทรัพยากรที่ไม่ได้ถูกกําหนด
// เช่น การเข้าถึงคือ notify ก็จะเป็น http://test/notify หรือ http://test/notify/{id} ทั้งคู่ได้ และชื่อเส้นทาง routeName เป็น test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| คำสรรพพากร   | URI                 | การกระทํา   | ชื่อเส้นทาง    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## สร้าง URL
> **สำคัญ** 
> ไม่สนับสนุนการสร้าง URL ของเส้นทางที่ซ้อนกัน (group ซ้อนกัน) ในปัจจุบัน

ตัวอย่างของเส้นทาง:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
เราสามารถใช้เมธอดดังต่อไปนี้เพื่อสร้าง URL ของเส้นทางนี้
```php
route('blog.view', ['id' => 100]); // ผลลัพธ์เป็น /blog/100
```

การใช้เส้นทาง URL ในมุมมองที่มีการสร้าง URL นี้จะเป็นการล็อกต่าง ๆ ในระะการเปลี่ยนการกำหนดเส้นทาง ในการปฏิบัติการมักจะทำให้สามาถสร้าง URL โดยอัตโนมัติ โดยไม่จำเป็นตำเปลี่ยนแปลงไฟล์มุมมอง

## ข้อมูลของเส้นทาง
> **สำคัญ**
> ต้องการ webman-framework >= 1.3.2

ผ่าน object `$request->route` เราสามารถใช้ค่าข้อมูลเส้นทางที่คำขอปัจจุบัน เช่น
```php
$route = $request->route; // เทียบเท่ากับ $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // คุณลักษณะนี้ต้องการ webman-framework >= 1.3.16
}
```

> **สำคัญ**
> หากคำขอปัจจุบันไม่มีการจับคู่กับเส้นทางใด ๆ ที่กำหนดใน `config/route.php` จะเป็น `$request->route` จะเป็นค่าว่าง กล่าวคือเริ่มต้นเส้นทาง `$request->route` จะเป็นค่าว่าง

## การจัดการเมื่อไม่พบ
เมื่อไม่พบเส้นทาง หรือ "Not Found" จะกลายเป็นการส่งคืนสถานะ 404 และแสดงเนื้อหาของไฟล์ `public/404.html`

หากนักพัฒนาต้องการเข้าไปในกระบวนการทางการเชื่อมไม่พบทาง เขาสามารถใช้ฟังก์ชันถัดไปของ webman-provided `Route::fallback($callback)` เช่นไข้ ๆ โลจิกคือ
## อินเทอร์เฟซเส้นทาง
```php
// ตั้งค่าเส้นทางของการร้องขอทุกวิธีของ $uri
Route::any($uri, $callback);
// ตั้งค่าเส้นทางของการร้องขอเฉพาะแบบ get ของ $uri
Route::get($uri, $callback);
// ตั้งค่าเส้นทางของการร้องขอเฉพาะแบบ post ของ $uri
Route::post($uri, $callback);
// ตั้งค่าเส้นทางของการร้องขอเฉพาะแบบ put ของ $uri
Route::put($uri, $callback);
// ตั้งค่าเส้นทางของการร้องขอเฉพาะแบบ patch ของ $uri
Route::patch($uri, $callback);
// ตั้งค่าเส้นทางของการร้องขอเฉพาะแบบ delete ของ $uri
Route::delete($uri, $callback);
// ตั้งค่าเส้นทางของการร้องขอเฉพาะแบบ head ของ $uri
Route::head($uri, $callback);
// กำหนดเส้นทางของการร้องขอแบบหลายรูปแบบพร้อมกัน
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// เส้นทางร่วมกลุ่ม
Route::group($path, $callback);
// เส้นทางของทรัพยากร
Route::resource($path, $callback, [$options]);
// ปิดใช้งานเส้นทาง
Route::disableDefaultRoute($plugin = '');
// เส้นทางล่างล้อมคืน, กำหนดเส้นทางเริ่มต้นเป็นค่าเริ่มต้น
Route::fallback($callback, $plugin = '');
```
หากไม่มีเส้นทางที่สอดคล้องกับ URI (รวมถึงเส้นทางดีฟอลต์เริ่มต้น) และไม่ได้ตั้งค่าเส้นทางที่ถอยกลับ จะส่งคืน 404.

## แฟ้มเส้นทางหลายรายการ
หากคุณต้องการจัดการเส้นทางโดยใช้แฟ้มเส้นทางหลายรายการ ตัวอย่างเช่น [แอพพลิเคชั่นหลายรายการ](multiapp.md) โดยแต่ละแอพพลิเคชั่นมีการตั้งค่าเส้นทางของตัวเอง คุณสามารถโหลดแฟ้มเส้นทางภายนอกผ่านการใช้ `require` 
เช่น `config/route.php` ดังตัวอย่าง
```php
<?php

// โหลดการตั้งค่าเส้นทางของแอดมินแอพพลิเคชั่น
require_once app_path('admin/config/route.php');
// โหลดการตั้งค่าเส้นทางของแอพี่พลิเคชั่น API
require_once app_path('api/config/route.php');

```
