## Định tuyến
## Quy tắc mặc định của định tuyến
Quy tắc mặc định của webman là `http://127.0.0.1:8787/{controller}/{action}`.

Controller mặc định là `app\controller\IndexController`, action mặc định là `index`.

Ví dụ truy cập:
- `http://127.0.0.1:8787` sẽ mặc định truy cập phương thức `index` của lớp `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` sẽ mặc định truy cập phương thức `index` của lớp `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` sẽ mặc định truy cập phương thức `test` của lớp `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` sẽ mặc định truy cập phương thức `test` của lớp `app\admin\controller\FooController` (xem thêm [Ứng dụng đa nhiệm](multiapp.md))

Ngoài ra, từ phiên bản 1.4, webman hỗ trợ định tuyến mặc định phức tạp hơn, ví dụ:
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

Khi bạn muốn thay đổi định tuyến yêu cầu, hãy thay đổi tệp cấu hình `config/route.php`.

Nếu bạn muốn tắt định tuyến mặc định, hãy thêm cấu hình sau vào cuối tệp cấu hình `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Định tuyến đóng
Thêm mã định tuyến sau vào tệp `config/route.php`:
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Lưu ý**
> Do hàm đóng không thuộc bất kỳ controller nào, nên `$request->app` `$request->controller` `$request->action` đều là chuỗi trống.

Khi truy cập địa chỉ `http://127.0.0.1:8787/test`, nó sẽ trả về chuỗi `test`.

> **Lưu ý**
> Đường dẫn định tuyến phải bắt đầu bằng `/`, ví dụ:

```php
// Sử dụng sai
Route::any('test', function ($request) {
    return response('test');
});

// Sử dụng đúng
Route::any('/test', function ($request) {
    return response('test');
});
```


## Định tuyến lớp
Thêm mã định tuyến sau vào tệp `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Khi truy cập địa chỉ `http://127.0.0.1:8787/testclass`, nó sẽ trả về giá trị của phương thức `test` của lớp `app\controller\IndexController`.


## Tham số của định tuyến
Nếu có tham số trong định tuyến, sử dụng `{key}` để khớp, kết quả khớp sẽ được chuyển đến các tham số của phương thức điều khiển tương ứng (bắt đầu từ tham số thứ hai), ví dụ:
```php
// Khớp với /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Nhận được tham số'.$id);
    }
}
```

Nhiều ví dụ khác:
```php
// Khớp với /user/123, không khớp với /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Khớp với /user/foobar, không khớp với /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Khớp với /user /user/123 và /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Khớp với tất cả các yêu cầu options
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Nhóm định tuyến

Đôi khi định tuyến bao gồm một số tiền tố giống nhau, trong trường hợp này, chúng ta có thể sử dụng nhóm định tuyến để đơn giản hóa định nghĩa. Ví dụ:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Tương đương với
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Sử dụng nhóm lồng

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```
## Middleware (Phần Middleware)

Chúng ta có thể thiết lập middleware cho một hoặc một nhóm routes.
Ví dụ: 
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
> **Lưu ý**: 
> Trong webman-framework <= 1.5.6 khi sử dụng `->middleware()` với group middleware, route hiện tại phải nằm dưới group hiện tại.

Ví dụ sử dụng sai (đúng khi webman-framework >= 1.5.7)
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

Ví dụ sử dụng đúng
```php
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

## Routes tài nguyên

```php
Route::resource('/test', app\controller\IndexController::class);

// Xác định routes tài nguyên
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Routes tài nguyên không xác định
// Ví dụ như khi truy cập notify, địa chỉ có thể là /test/notify hoặc /test/notify/{id} đều có thể, routeName sẽ là test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```

| Phương Thức | URI                 | Hành Động   | Tên Route    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## Tạo URL

> **Lưu ý**: 
> Hiện tại không hỗ trợ việc tạo URL cho nhóm routes lồng nhau

Ví dụ về routes:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Chúng ta có thể sử dụng phương thức sau để tạo URL cho route này.
```php
route('blog.view', ['id' => 100]); // Kết quả sẽ là /blog/100
```

Khi sử dụng URL của route trong view, phương pháp này có thể giúp tạo URL tự động mà không cần sửa đổi file view vì thay đổi trong các quy tắc route, để tránh việc phải thay đổi nhiều file view.

## Lấy thông tin về route

> **Lưu ý**
> Yêu cầu webman-framework >= 1.3.2

Chúng ta có thể lấy thông tin của route hiện tại thông qua đối tượng `$request->route`, ví dụ:

```php
$route = $request->route; // Tương đương với $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Tính năng này yêu cầu webman-framework >= 1.3.16
}
```
> **Lưu ý**
> Nếu request hiện tại không khớp với bất kỳ route nào được xác định trong `config/route.php`, thì `$request->route` sẽ là null, nghĩa là khi sử dụng route mặc định, `$request->route` sẽ là null.

## Xử lý lỗi 404

Khi không tìm thấy route, webman mặc định sẽ trả về mã trạng thái 404 và đồng thời xuất nội dung của tệp `public/404.html`.
 
Nếu nhà phát triển muốn can thiệp vào quy trình kinh doanh khi không tìm thấy route, có thể sử dụng phương thức fallback của webman `Route::fallback($callback)`. Ví dụ: logic dưới đây sẽ chuyển hướng về trang chủ khi không tìm thấy route.
```php
Route::fallback(function(){
    return redirect('/');
});
```

Hoặc khi không tìm thấy route sẽ trả về một dữ liệu json, điều này rất hữu ích khi webman hoạt động như một API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Xem thêm: [Trang Lỗi Tùy Chỉnh](others/custom-error-page.md)

## Giao Diện Người Dùng Route

```php
// Thiết lập route cho tất cả các phương thức của $uri
Route::any($uri, $callback);
// Thiết lập route GET cho $uri
Route::get($uri, $callback);
// Thiết lập route POST cho $uri
Route::post($uri, $callback);
// Thiết lập route PUT cho $uri
Route::put($uri, $callback);
// Thiết lập route PATCH cho $uri
Route::patch($uri, $callback);
// Thiết lập route DELETE cho $uri
Route::delete($uri, $callback);
// Thiết lập route HEAD cho $uri
Route::head($uri, $callback);
// Thiết lập nhiều loại route cùng một lúc
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Route nhóm
Route::group($path, $callback);
// Route tài nguyên
Route::resource($path, $callback, [$options]);
// Vô hiệu hóa route mặc định
Route::disableDefaultRoute($plugin = '');
// Fallback route, thiết lập route mặc định
Route::fallback($callback, $plugin = '');
```
Nếu không tìm thấy route cho $uri (bao gồm route mặc định), và fallback route cũng không được thiết lập, webman sẽ trả về 404.
## Tập tin cấu hình đa tuyến

Nếu bạn muốn quản lý tuyến đường bằng cách sử dụng nhiều tập tin cấu hình tuyến đường, ví dụ như [ứng dụng đa](multiapp.md) khi mỗi ứng dụng có cấu hình tuyến đường riêng của mình, lúc này bạn có thể tải các tập tin cấu hình tuyến đường bên ngoài thông qua `require`.
Ví dụ trong `config/route.php`:

```php
<?php

// Tải tập tin cấu hình tuyến đường của ứng dụng admin
require_once app_path('admin/config/route.php');
// Tải tập tin cấu hình tuyến đường của ứng dụng api
require_once app_path('api/config/route.php');

```
