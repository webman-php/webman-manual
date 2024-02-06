## Định tuyến
## Quy tắc định tuyến mặc định
Quy tắc định tuyến mặc định của webman là `http://127.0.0.1:8787/{controller}/{action}`.

Controller mặc định là `app\controller\IndexController`, Action mặc định là `index`.

Ví dụ: Khi truy cập:
- `http://127.0.0.1:8787` sẽ mặc định truy cập phương thức `index` của lớp `app\controller\IndexController`.
- `http://127.0.0.1:8787/foo` sẽ mặc định truy cập phương thức `index` của lớp `app\controller\FooController`.
- `http://127.0.0.1:8787/foo/test` sẽ mặc định truy cập phương thức `test` của lớp `app\controller\FooController`.
- `http://127.0.0.1:8787/admin/foo/test` sẽ mặc định truy cập phương thức `test` của lớp `app\admin\controller\FooController` (xem thêm [Ứng dụng đa](multiapp.md)).

Ngoài ra, webman bắt đầu hỗ trợ định tuyến mặc định phức tạp hơn từ phiên bản 1.4, ví dụ:
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
Thêm mã định tuyến sau vào tệp `config/route.php`
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Lưu ý**
> Do hàm đóng không thuộc bất kỳ thông số nào của điều khiển, nên `$request->app`, `$request->controller`, `$request->action` đều là chuỗi rỗng.

Khi truy cập địa chỉ `http://127.0.0.1:8787/test`, sẽ trả về chuỗi `test`.

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
Thêm mã định tuyến sau vào tệp `config/route.php`
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Khi truy cập địa chỉ `http://127.0.0.1:8787/testclass`, sẽ trả về giá trị của phương thức `test` của lớp `app\controller\IndexController`.

## Tham số định tuyến
Nếu có tham số trong định tuyến, sử dụng `{key}` để khớp, kết quả khớp sẽ được chuyển vào tham số phương thức của điều khiển tương ứng (bắt đầu từ tham số thứ hai), ví dụ:
```php
// Khớp /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Nhận tham số'.$id);
    }
}
```

Các ví dụ khác:
```php
// Khớp /user/123, không khớp /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Khớp /user/foobar, không khớp /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Khớp /user /user/123 và /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Khớp tất cả yêu cầu options
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Nhóm định tuyến
Đôi khi định tuyến chứa một lượng lớn tiền tố giống nhau, lúc đó, chúng ta có thể sử dụng định tuyến nhóm để đơn giản hóa định nghĩa. Ví dụ:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('tạo');});
   Route::any('/edit', function ($request) {return response('chỉnh sửa');});
   Route::any('/view/{id}', function ($request, $id) {return response("xem $id");});
}
```
Tương đương với
```php
Route::any('/blog/create', function ($request) {return response('tạo');});
Route::any('/blog/edit', function ($request) {return response('chỉnh sửa');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("xem $id");});
```

Sử dụng nhóm lồng

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('tạo');});
      Route::any('/edit', function ($request) {return response('chỉnh sửa');});
      Route::any('/view/{id}', function ($request, $id) {return response("xem $id");});
   });  
}
```

## Trung gian định tuyến
Chúng ta có thể thiết lập trung gian cho một hoặc một nhóm định tuyến cụ thể.
Ví dụ:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('tạo');});
   Route::any('/edit', function () {return response('chỉnh sửa');});
   Route::any('/view/{id}', function ($request, $id) {return response("xem $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **Lưu ý**:
> Trong webman-framework <= 1.5.6, khi `->middleware()` ứng dụng trung gian cho nhóm sau cùng sau phân nhóm, định tuyến hiện tại phải ở dưới phân nhóm hiện tại.

```php
# Ví dụ sử dụng sai (Kể từ webman-framework >= 1.5.7, cú pháp này có hiệu lực)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('tạo');});
      Route::any('/edit', function ($request) {return response('chỉnh sửa');});
      Route::any('/view/{id}', function ($request, $id) {return response("xem $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Ví dụ sử dụng đúng
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('tạo');});
      Route::any('/edit', function ($request) {return response('chỉnh sửa');});
      Route::any('/view/{id}', function ($request, $id) {return response("xem $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
}
```

## Định tuyến tài nguyên
```php
Route::resource('/test', app\controller\IndexController::class);

// Định tuyến tài nguyên cụ thể
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Định tuyến tài nguyên không xác định
// Ví dụ: nếu notify là địa chỉ truy cập, định tuyến cũng là loại any /test/notify hoặc/test/notify/{id} đều có thể routeName là test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Phương thức | Địa chỉ truy cập      | Hành động | Tên định tuyến |
|------------|----------------------|----------|----------------|
| GET        | /test                | index    | test.index     |
| GET        | /test/create         | create   | test.create    |
| POST       | /test                | store    | test.store     |
| GET        | /test/{id}           | show     | test.show      |
| GET        | /test/{id}/edit      | edit     | test.edit      |
| PUT        | /test/{id}           | update   | test.update    |
| DELETE     | /test/{id}           | destroy  | test.destroy   |
| PUT        | /test/{id}/recovery  | recovery | test.recovery  |

## Tạo URL
> **Lưu ý**
> Hiện tại không hỗ trợ tạo URL cho định tuyến nhóm lồng.

Ví dụ định tuyến:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Chúng ta có thể sử dụng phương pháp sau để tạo URL của định tuyến này.
```php
route('blog.view', ['id' => 100]); // Kết quả là /blog/100
```

Khi sử dụng URL của định tuyến trong mã nguồn, sẽ phòng trường hợp thay đổi quy tắc định tuyến và tránh việc sửa đổi số lượng lớn tệp gốc khi địa chỉ truy cập thay đổi.

## Lấy thông tin định tuyến
> **Lưu ý**
> Yêu cầu webman-framework >= 1.3.2

Chúng ta có thể lấy thông tin về định tuyến hiện tại thông qua đối tượng `$request->route`, ví dụ:

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
> Nếu yêu cầu hiện tại không khớp với bất kỳ định tuyến nào được cấu hình trong `config/route.php`, thì `$request->route` sẽ là null, nghĩa là khi sử dụng định tuyến mặc định thì `$request->route` sẽ là null.

## Xử lý lỗi 404
Khi đường dẫn truy cập không tồn tại, mặc định webman sẽ trả về mã trạng thái 404 và hiển thị nội dung của tệp `public/404.html`.

Nếu người phát triển muốn can thiệp vào quy trình kinh doanh khi không tìm thấy định tuyến, họ có thể sử dụng phương thức fallback của webman. Ví dụ mã sau đây sẽ chuyển hướng đến trang chủ khi không tìm thấy định tuyến.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Hoặc khi không tìm thấy định tuyến, trả về dữ liệu json, điều này rất hữu ích khi webman sử dụng làm API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 Không tìm thấy']);
});
```

Liên kết tương quan [Trang 404, 500 tùy chỉnh](others/custom-error-page.md)
## Giao diện định tuyến
```php
// Thiết lập định tuyến cho mọi phương thức yêu cầu đối với $uri
Route::any($uri, $callback);
// Thiết lập định tuyến cho yêu cầu get đối với $uri
Route::get($uri, $callback);
// Thiết lập định tuyến cho yêu cầu post đối với $uri
Route::post($uri, $callback);
// Thiết lập định tuyến cho yêu cầu put đối với $uri
Route::put($uri, $callback);
// Thiết lập định tuyến cho yêu cầu patch đối với $uri
Route::patch($uri, $callback);
// Thiết lập định tuyến cho yêu cầu delete đối với $uri
Route::delete($uri, $callback);
// Thiết lập định tuyến cho yêu cầu head đối với $uri
Route::head($uri, $callback);
// Thiết lập định tuyến cho nhiều loại yêu cầu cùng một lúc
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Định tuyến nhóm
Route::group($path, $callback);
// Định tuyến tài nguyên
Route::resource($path, $callback, [$options]);
// Vô hiệu hóa định tuyến mặc định
Route::disableDefaultRoute($plugin = '');
// Định tuyến fallback, thiết lập định tuyến mặc định
Route::fallback($callback, $plugin = '');
```
Nếu không có định tuyến nào tương ứng với uri (bao gồm cả định tuyến mặc định), và định tuyến fallback cũng không được thiết lập, thì sẽ trả về mã lỗi 404.

## Nhiều tập tin cấu hình định tuyến
Nếu bạn muốn quản lý định tuyến bằng nhiều tập tin cấu hình định tuyến, ví dụ như [ứng dụng đa ứng dụng](multiapp.md) với mỗi ứng dụng có cấu hình định tuyến riêng, bạn có thể sử dụng `require` để tải tập tin cấu hình định tuyến từ bên ngoài.
Ví dụ trong `config/route.php`
```php
<?php

// Tải cấu hình định tuyến của ứng dụng admin
require_once app_path('admin/config/route.php');
// Tải cấu hình định tuyến của ứng dụng api
require_once app_path('api/config/route.php');

```
