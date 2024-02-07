# Trung gian 
Trung gian thường được sử dụng để chặn yêu cầu hoặc phản hồi. Ví dụ một xác thực sự kiện người dùng trước khi thực hiện bộ điều khiển, chẳng hạn như chuyển hướng trang đăng nhập nếu người dùng chưa đăng nhập, hoặc thêm một tiêu đề header vào phản hồi. Ví dụ khác là thống kê tỷ lệ yêu cầu uri cụ thể và nhiều hơn nữa.

## Mô hình trung gian cebola

``` 
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │   
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── Yêu cầu ───────────────────────> Bộ điều khiển ─ Phản hồi ──────────────> Client 
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
``` 
Trung gian và bộ điều khiển cùng tạo thành một mô hình cebola cổ điển, nơi trung gian tạo ra các lớp vỏ như một chiếc cebola và bộ điều khiển là hạt cebola. Như trong hình minh họa, yêu cầu đi qua trung gian 1, 2, 3 để đến bộ điều khiển, bộ điều khiển trả về một phản hồi, sau đó phản hồi lại trải qua trung gian theo thứ tự 3, 2, 1 trước khi trả về cho người dùng. Nghĩa là ở mỗi trung gian, chúng ta vừa có thể nhận yêu cầu, vừa có thể nhận phản hồi.

## Chặn yêu cầu
Đôi khi chúng ta không muốn một yêu cầu nào đó đến tầng điều khiển, ví dụ như khi chúng ta phát hiện ra rằng người dùng hiện tại chưa đăng nhập trong trung gian 2, chúng ta có thể trực tiếp chặn yêu cầu và trả về một phản hồi đăng nhập. Quá trình này có vẻ như sau:

``` 
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Yêu cầu ─────────┐     │    │  Bộ điều khiển   │      │      │     │
            │     │ Phản hồi │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
``` 

Như hình minh họa, yêu cầu đến trung gian 2 sau đó tạo ra một phản hồi đăng nhập, phản hồi đi qua trung gian 1 rồi trả về cho người dùng.
## Giao diện trung gian
Giao diện trung gian phải triển khai `Webman\MiddlewareInterface` interface.
```php
interface MiddlewareInterface
{
    /**
     * Xử lý yêu cầu máy chủ đầu vào.
     *
     * Xử lý yêu cầu máy chủ đầu vào để tạo ra một phản hồi.
     * Nếu không thể tạo ra phản hồi chính mình, nó có thể ủy quyền cho trình xử lý yêu cầu được cung cấp để làm điều đó.
     */
    public function process(Request $request, callable $handler): Response;
}
```
Điều đó có nghĩa là phải triển khai phương thức `process`, `process` phải trả về một đối tượng `support\Response`. Mặc định, đối tượng này được tạo ra bởi `$handler($request)` (yêu cầu sẽ tiếp tục qua các lớp hành tinh như lớp cebolla), hoặc có thể được tạo ra bởi các hàm trợ giúp như `response()` `json()` `xml()` `redirect()` để tạo ra phản hồi (yêu cầu sẽ dừng lại và không tiếp tục qua các lớp hành tinh).

## Nhận yêu cầu và phản hồi trong trung gian
Trong middleware, chúng ta có thể nhận yêu cầu và cũng có thể nhận phản hồi sau khi điều khiển được thực hiện, vì vậy bên trong middleware được chia thành ba phần.
1. Phần thông qua yêu cầu, nghĩa là giai đoạn trước xử lý yêu cầu.
2. Phần xử lý yêu cầu điều khiển, nghĩa là giai đoạn xử lý yêu cầu.
3. Phần thoát khỏi yêu cầu, nghĩa là giai đoạn sau khi xử lý yêu cầu.

Ba giai đoạn trong middleware được thể hiện như sau
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
        echo 'Đây là giai đoạn thông qua yêu cầu, nghĩa là trước khi xử lý yêu cầu';
        
        $response = $handler($request); // Tiếp tục qua lớp hành tinh cho đến khi nhận được phản hồi từ điều khiển
        
        echo 'Đây là giai đoạn thoát khỏi yêu cầu, nghĩa là sau khi xử lý yêu cầu';
        
        return $response;
    }
}
```

## Ví dụ: Trung gian xác thực danh tính
Tạo tập tin `app/middleware/AuthCheckTest.php` (nếu thư mục không tồn tại, vui lòng tạo mới) như sau:
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
            // Đã đăng nhập, yêu cầu tiếp tục qua lớp hành tinh
            return $handler($request);
        }

        // Sử dụng phản ánh để lấy danh sách các phương thức của điều khiển không cần đăng nhập
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Phương thức truy cập cần đăng nhập
        if (!in_array($request->action, $noNeedLogin)) {
            // Chặn yêu cầu, trả về một phản hồi chuyển hướng, yêu cầu dừng lại và không tiếp tục qua lớp hành tinh
            return redirect('/user/login');
        }

        // Không cần đăng nhập, yêu cầu tiếp tục qua lớp hành tinh
        return $handler($request);
    }
}
```

Tạo điều khiển mới `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Các phương thức không cần đăng nhập
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'đăng nhập thành công']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **Chú ý**
> `$noNeedLogin` lưu trữ danh sách các phương thức mà điều khiển hiện tại không cần đăng nhập để truy cập

Thêm trung gian toàn cầu trong `config/middleware.php` như sau:
```php
return [
    // Trung gian toàn cầu
    '' => [
        // ... Phần còn lại của trung gian
        app\middleware\AuthCheckTest::class,
    ]
];
```

Với trung gian xác thực danh tính, chúng ta có thể tập trung viết mã kinh doanh ở tầng điều khiển, không cần lo lắng về việc người dùng có đăng nhập hay không.

## Ví dụ: Trung gian yêu cầu xứng đáng
Tạo tập tin `app/middleware/AccessControlTest.php` (nếu thư mục không tồn tại, vui lòng tạo mới) như sau:
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
        // Nếu là yêu cầu options, trả về một phản hồi trống, nếu không, tiếp tục qua lớp hành tinh và nhận phản hồi
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Thêm các tiêu đề liên quan đến CORS cho phản hồi
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

> **Lưu ý**
> Yêu cầu CORS có thể tạo ra yêu cầu OPTIONS, chúng ta không muốn yêu cầu OPTIONS vào tầng điều khiển, vì vậy chúng ta trả về một phản hồi trống ngay lập tức cho yêu cầu OPTIONS (`response('')`) để chặn yêu cầu. Nếu như giao diện của bạn cần thiết lập địa chỉ định tuyến, hãy sử dụng `Route::any(..)` hoặc `Route::add(['POST', 'OPTIONS'], ..)`.

Thêm trung gian toàn cầu trong `config/middleware.php` như sau:
```php
return [
    // Trung gian toàn cầu
    '' => [
        // ... Phần còn lại của trung gian
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Chú ý**
> Nếu yêu cầu ajax tùy chỉnh các tiêu đề, bạn cần thêm tiêu đề tùy chỉnh này vào trường `Access-Control-Allow-Headers` trong middleware, nếu không, bạn sẽ nhận được thông báo `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`
## Giải thích

- Middleware được chia thành middleware toàn cầu, middleware ứng dụng (chỉ có hiệu lực trong chế độ nhiều ứng dụng, xem [Nhiều Ứng dụng](multiapp.md)), middleware định tuyến.
- Hiện tại không hỗ trợ middleware cho một điều khiển duy nhất (nhưng có thể thực hiện chức năng middleware tương tự bằng công cụ kiểm tra `$request->controller` trong middleware).
- Vị trí tập tin cấu hình middleware là `config/middleware.php`.
- Cấu hình middleware toàn cầu được đặt tại key `''`.
- Cấu hình middleware ứng dụng được đặt tại tên ứng dụng cụ thể, ví dụ

```php
return [
    // Middleware toàn cầu
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware ứng dụng api (Middleware ứng dụng chỉ có hiệu lực trong chế độ nhiều ứng dụng)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware Định tuyến

Chúng ta có thể đặt middleware cho một route cụ thể hoặc một nhóm route cụ thể.
Ví dụ, thêm cấu hình sau vào `config/route.php`:

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

## Truyền tham số vào hàm tạo Middleware

> **Lưu ý**
> Tính năng này yêu cầu webman-framework >= 1.4.8

Sau phiên bản 1.4.8, tệp cấu hình hỗ trợ việc khởi tạo trực tiếp middleware hoặc hàm vô danh, điều này giúp dễ dàng truyền tham số vào middleware thông qua hàm tạo.
Ví dụ, bạn cũng có thể cấu hình như sau trong `config/middleware.php`:

```php
return [
    // Middleware toàn cầu
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware ứng dụng api (Middleware ứng dụng chỉ có hiệu lực trong chế độ nhiều ứng dụng)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Tương tự, middleware định tuyến cũng có thể truyền tham số vào hàm tạo Middleware, ví dụ trong `config/route.php`:

```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Thứ tự thực hiện Middleware

- Thứ tự thực hiện Middleware là `Middleware toàn cầu`-> `Middleware ứng dụng`-> `Middleware định tuyến`.
- Khi có nhiều middleware toàn cầu, chúng sẽ thực hiện theo thứ tự cấu hình thực tế (tương tự với Middleware ứng dụng và Middleware định tuyến).
- Yêu cầu 404 không gây kích hoạt bất kỳ Middleware nào, bao gồm cả Middleware toàn cầu.

## Truyền tham số vào Middleware (route->setParams)

**Cấu hình route `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (giả sử là Middleware toàn cầu)**
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
        // Mặc định, $request->route là null, vì vậy cần kiểm tra xem $request->route có trống không
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Truyền tham số từ Middleware vào Điều khiển

Đôi khi điều khiển cần sử dụng dữ liệu được tạo ra trong middleware, trong trường hợp này, chúng ta có thể truyền tham số vào điều khiển bằng cách thêm thuộc tính vào đối tượng `$request`. Ví dụ:

**Middleware**
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

**Điều khiển:**
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
## Middleware lấy thông tin định tuyến yêu cầu hiện tại
> **Lưu ý**
> Yêu cầu webman-framework >= 1.3.2

Chúng ta có thể sử dụng `$request->route` để lấy đối tượng định tuyến và lấy thông tin tương ứng bằng cách gọi các phương thức tương ứng.

**Cấu hình định tuyến**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Middleware**
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
        // Nếu yêu cầu không khớp với bất kỳ định tuyến nào (ngoại trừ định tuyến mặc định), thì $request->route sẽ là null
        // Giả sử trình duyệt truy cập vào địa chỉ /user/111, sau đó sẽ in ra thông tin như sau
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

> **Lưu ý**
> Phương thức `$route->param()` yêu cầu webman-framework >= 1.3.16


## Middleware lấy ngoại lệ
> **Lưu ý**
> Yêu cầu webman-framework >= 1.3.15

Trong quá trình xử lý kinh doanh, có thể phát sinh ngoại lệ, trong middleware sử dụng `$response->exception()` để lấy ngoại lệ.

**Cấu hình định tuyến**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('thử nghiệm ngoại lệ');
});
```

**Middleware:**
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


## Middleware toàn cầu

> **Lưu ý**
> Tính năng này yêu cầu webman-framework >= 1.5.16

Middleware toàn cầu của dự án chính chỉ ảnh hưởng đến dự án chính, không ảnh hưởng đến [plugin ứng dụng](app/app.md). Đôi khi chúng ta muốn thêm một middleware ảnh hưởng toàn cầu bao gồm tất cả các plugin, chúng ta có thể sử dụng middleware toàn cầu. 

Cấu hình trong `config/middleware.php` như sau:
```php
return [
    '@' => [ // Thêm middleware toàn cầu cho dự án chính và tất cả plugin
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Chỉ thêm middleware toàn cầu cho dự án chính
];
```

> **Ghi chú**
> Middleware `@` toàn cầu không chỉ có thể cấu hình trong dự án chính mà cũng có thể cấu hình trong một plugin nào đó, ví dụ `plugin/ai/config/middleware.php` cấu hình middleware `@`, điều này cũng sẽ ảnh hưởng đến dự án chính và tất cả các plugin.


## Thêm middleware cho một plugin cụ thể

> **Lưu ý**
> Tính năng này yêu cầu webman-framework >= 1.5.16

Đôi khi chúng ta muốn thêm một middleware cho một [plugin ứng dụng](app/app.md) cụ thể mà không muốn thay đổi mã của plugin (vì mã sẽ bị ghi đè khi nâng cấp), lúc đó chúng ta có thể cấu hình middleware cho nó trong dự án chính.

Cấu hình trong `config/middleware.php` như sau:
```php
return [
    'plugin.ai' => [], // Thêm middleware cho plugin ai
    'plugin.ai.admin' => [], // Thêm middleware cho module admin của plugin ai
];
```

> **Ghi chú**
> Tất nhiên cũng có thể cấu hình tương tự trong một plugin để ảnh hưởng đến plugin khác, ví dụ `plugin/foo/config/middleware.php` thêm cấu hình như trên sẽ ảnh hưởng đến plugin ai.
