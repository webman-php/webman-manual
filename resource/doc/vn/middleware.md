# Middleware
Middleware thường được sử dụng để ngăn chặn yêu cầu hoặc phản hồi. Ví dụ, xác minh danh tính người dùng trước khi thực hiện điều khiển, chẳng hạn như chuyển hướng đến trang đăng nhập nếu người dùng chưa đăng nhập, hoặc thêm một tiêu đề header vào phản hồi. Ví dụ khác bao gồm việc thống kê tỷ lệ yêu cầu uri cụ thể và nhiều hơn nữa.

## Mô hình middleware củ
```plaintext
                              
┌──────────────────────────────────────────────────────┐
│                     middleware1                      │ 
│     ┌──────────────────────────────────────────┐     │
│     │               middleware2                │     │
│     │     ┌──────────────────────────────┐     │     │
│     │     │         middleware3          │     │     │        
│     │     │     ┌──────────────────┐     │     │     │
│     │     │     │                  │     │     │     │
── yêu cầu ───────> điều khiển ─ phản hồi ───────────────> người dùng
│     │     │     │                  │     │     │     │
│     │     │     └──────────────────┘     │     │     │
│     │     │                              │     │     │
│     │     └──────────────────────────────┘     │     │
│     │                                          │     │
│     └──────────────────────────────────────────┘     │
│                                                      │
└──────────────────────────────────────────────────────┘
```
Middleware và điều khiển tạo nên một mô hình củ cổ điển, với middleware giống như lớp vỏ của một chiếc hành tây và điều khiển là nhân của hành tây. Như hình minh họa, yêu cầu đi qua middleware 1, 2, 3 rồi đến điều khiển, điều khiển trả về một phản hồi, sau đó phản hồi lại đi qua middleware 3, 2, 1 để cuối cùng trở về cho người dùng. Nghĩa là trong mỗi middleware, chúng ta có thể nhận được yêu cầu và cũng có thể nhận được phản hồi.

## Ngăn chặn yêu cầu
Đôi khi chúng ta không muốn một yêu cầu nào đó đến tầng điều khiển, chẳng hạn khi chúng ta phát hiện rằng người dùng hiện tại chưa đăng nhập trong middleware xác minh danh tính, sau đó có thể ngay lập tức ngăn chặn yêu cầu và trả về một phản hồi đăng nhập. Quá trình này sẽ giống như sau:

```plaintext
                              
┌────────────────────────────────────────────────────┐
│                     middleware1                    │ 
│     ┌──────────────────────────────────────────┐   │
│     │           Kiểm tra danh tính             │   │
│     │     ┌──────────────────────────────┐     │   │
│     │     │         middleware3          │     │   │       
│     │     │     ┌──────────────────┐     │     │   │
│     │     │     │                  │     │     │   │
── yêu cầu ──────│      điều khiển      │     │     │
│     │ phản hồi │                  │     │     │   │
<────── ────────┘   └──────────────────┘     │     │
│     │      │                              │     │   │
│     │      └──────────────────────────────┘     │   │
│     │                                           │   │
└─────┼───────────────────────────────────────────┘   │
　　　 └──────────────────────────────────────────────┘
```

Như trong hình minh họa, khi yêu cầu đến middleware xác minh danh tính, một phản hồi đăng nhập được tạo ra và phản hồi đi qua lại từ middleware 1 rồi trở về cho trình duyệt.

## Giao diện middleware
Middleware phải triển khai giao diện `Webman\MiddlewareInterface`.
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
Điều này có nghĩa là phải triển khai phương thức `process`. Phương thức `process` phải trả về một đối tượng `Response`, mặc định đối tượng này được tạo ra bởi `$handler($request)` (yêu cầu tiếp tục đi xuyên qua nhân hành tây), hoặc có thể được tạo ra bởi các hàm trợ giúp như `response()` `json()` `xml()` `redirect()` (yêu cầu dừng lại và không đi xuyên qua nhân hành tây).

## Nhận yêu cầu và phản hồi trong middleware
Trong middleware, chúng ta có thể nhận được yêu cầu và cũng có thể nhận được phản hồi sau khi điều khiển được thực hiện, vì vậy, các middleware bên trong chia thành ba phần.
1. Giai đoạn đi qua yêu cầu, cũng chính là giai đoạn trước xử lý yêu cầu  
2. Giai đoạn xử lý yêu cầu điều khiển, cũng chính là giai đoạn xử lý yêu cầu  
3. Giai đoạn đi qua phản hồi, cũng chính là giai đoạn sau khi xử lý yêu cầu  

Ba giai đoạn này trong middleware được thể hiện như sau:
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
        echo 'Đây là giai đoạn đi qua yêu cầu, cũng chính là giai đoạn trước khi xử lý yêu cầu';
        
        $response = $handler($request); // Tiếp tục xuyên qua nhân hành tây cho đến khi điều khiển được thực hiện và trả về phản hồi
        
        echo 'Đây là giai đoạn đi qua phản hồi, cũng chính là giai đoạn sau khi xử lý yêu cầu';
        
        return $response;
    }
}
```
## Ví dụ: Middleware xác thực

Tạo tệp `app/middleware/AuthCheckTest.php` (nếu thư mục không tồn tại, vui lòng tự tạo) như sau:

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
            // Đã đăng nhập, tiếp tục xuyên qua lớp Middleware
            return $handler($request);
        }

        // Sử dụng phản ánh để lấy các phương thức nào không cần đăng nhập trong controller
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Phương thức yêu cầu đăng nhập
        if (!in_array($request->action, $noNeedLogin)) {
            // Chặn yêu cầu và trả về một phản hồi chuyển hướng, yêu cầu dừng lại khi đi xuyên qua lớp Middleware
            return redirect('/user/login');
        }

        // Không cần phải đăng nhập, yêu cầu tiếp tục xuyên qua lớp Middleware
        return $handler($request);
    }
}
```

Tạo controller `app/controller/UserController.php`
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
        return json(['code' => 0, 'msg' => 'Đăng nhập thành công']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **Thông báo**
> `$noNeedLogin` lưu trữ các phương thức của controller hiện tại mà không cần đăng nhập để truy cập

Thêm middleware toàn cầu vào `config/middleware.php` như sau:
```php
return [
    // Middleware toàn cầu
    '' => [
        // ... Bỏ qua các middleware khác ở đây
        app\middleware\AuthCheckTest::class,
    ]
];
```

Với middleware xác thực, chúng ta có thể tập trung việc viết mã kinh doanh ở tầng controller mà không cần lo lắng về việc người dùng có đăng nhập hay không.

## Ví dụ: Middleware yêu cầu Cross-Origin

Tạo tệp `app/middleware/AccessControlTest.php` (nếu thư mục không tồn tại, vui lòng tự tạo) như sau:

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
        // Nếu là yêu cầu OPTIONS thì trả về một phản hồi trống, nếu không tiếp tục xuyên qua lớp Middleware và nhận một phản hồi
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Thêm các tiêu đề liên quan đến Cross-Origin vào phản hồi
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

> **Ghi chú**
> Yêu cầu Cross-Origin có thể tạo ra yêu cầu OPTIONS, chúng tôi không muốn yêu cầu OPTIONS đi vào controller, vì vậy chúng tôi trả về một phản hồi trống (`response('')`) để chặn yêu cầu. Nếu giao diện người dùng cần được thiết lập, vui lòng sử dụng `Route::any(..)` hoặc `Route::add(['POST', 'OPTIONS'], ..)`.

Thêm middleware toàn cầu vào `config/middleware.php` như sau:

```php
return [
    // Middleware toàn cầu
    '' => [
        // ... Bỏ qua các middleware khác ở đây
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Lưu ý**
> Nếu yêu cầu ajax tùy chỉnh tiêu đề, bạn cần thêm tiêu đề tùy chỉnh này vào trường `Access-Control-Allow-Headers` trong middleware, nếu không sẽ báo lỗi `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

**Chú ý**

- Middleware được chia thành toàn cầu, ứng dụng (middleware ứng dụng chỉ có hiệu lực trong trường hợp chạy nhiều ứng dụng, xem [Multiple Apps](multiapp.md)), và middleware tuyến đường.
- Hiện tại không hỗ trợ middleware cho từng controller (nhưng bạn có thể thực hiện chức năng tương tự như middleware trong controller thông qua việc kiểm tra `$request->controller` trong middleware).
- Tệp cấu hình middleware được lưu tại vị trí `config/middleware.php`.
- Cấu hình middleware toàn cầu được lưu tại key `''`.
- Cấu hình middleware ứng dụng được lưu tại tên ứng dụng cụ thể, ví dụ:

```php
return [
    // Middleware toàn cầu
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware ứng dụng api (middleware ứng dụng chỉ có hiệu lực trong trường hợp chạy nhiều ứng dụng)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware của tuyến đường

Chúng ta có thể thiết lập middleware cho một hoặc một nhóm tuyến đường.
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

## Truyền tham số vào hàm tạo của middleware

> **Lưu ý**
> Tính năng này yêu cầu webman-framework >= 1.4.8

Từ phiên bản 1.4.8 trở lên, tệp cấu hình hỗ trợ khởi tạo trực tiếp middleware hoặc hàm không tên, điều này giúp dễ dàng truyền tham số vào middleware thông qua hàm tạo.

Ví dụ, bạn cũng có thể cấu hình như sau trong tệp `config/middleware.php`:

```
return [
    // Middleware toàn cầu
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware ứng dụng api (middleware ứng dụng chỉ có hiệu lực trong trường hợp chạy nhiều ứng dụng)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Tương tự, middleware của tuyến đường cũng có thể truyền tham số vào middleware thông qua hàm tạo, ví dụ như sau trong tệp `config/route.php`:

```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Thứ tự thực thi của middleware
- Thứ tự thực thi của middleware là `toàn cầu` -> `middleware ứng dụng` -> `middleware tuyến đường`.
- Khi có nhiều middleware toàn cầu, chúng sẽ được thực thi theo thứ tự cấu hình thực tế (tương tự với middleware ứng dụng và middleware tuyến đường).
- Yêu cầu 404 sẽ không kích hoạt bất kỳ middleware nào, bao gồm cả middleware toàn cầu.

## Truyền tham số vào middleware (route->setParams)

**Cấu hình tuyến đường `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (giả sử là middleware toàn cầu)**
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
        // Mặc định, $request->route sẽ là null, vì vậy bạn cần kiểm tra xem $request->route có được xác định không
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Truyền tham số từ middleware vào controller

Đôi khi controller cần sử dụng dữ liệu được tạo ra từ trong middleware, lúc đó chúng ta có thể truyền tham số vào controller thông qua việc thêm thuộc tính vào đối tượng `$request`. Ví dụ:

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

**Controller:**
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

Chúng ta có thể sử dụng `$request->route` để lấy đối tượng định tuyến, sau đó gọi các phương thức tương ứng để lấy thông tin tương ứng.

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
        // Nếu yêu cầu không khớp với bất kỳ định tuyến nào (ngoại trừ định tuyến mặc định), thì `$request->route` sẽ là null
        // Giả sử trình duyệt truy cập địa chỉ /user/111, sau đó sẽ in ra thông tin sau
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid' => 111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **Lưu ý**
> Phương thức `$route->param()` cần webman-framework >= 1.3.16


## Middleware lấy ngoại lệ
> **Lưu ý**
> Yêu cầu webman-framework >= 1.3.15

Trong quá trình xử lý doanh nghiệp, có thể phát sinh ngoại lệ, trong Middleware, sử dụng `$response->exception()` để lấy ngoại lệ.

**Cấu hình định tuyến**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**Middleware：**
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

Middleware toàn cầu của dự án chính chỉ ảnh hưởng đến dự án chính, không ảnh hưởng đến [phần cắm ứng dụng](app/app.md). Đôi khi chúng ta muốn thêm một middleware ảnh hưởng toàn cầu bao gồm tất cả các phần cắm, thì có thể sử dụng middleware toàn cầu.

Cấu hình trong `config/middleware.php` như sau：
```php
return [
    '@' => [ // Thêm middleware toàn cầu cho dự án chính và tất cả các phần cắm
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Chỉ thêm middleware toàn cầu cho dự án chính
];
```

> **Gợi ý**
> Middleware toàn cầu `@` có thể được cấu hình không chỉ trong dự án chính mà còn trong một số phần cắm khác nhau, ví dụ như cấu hình middleware toàn cầu `@` trong tệp `plugin/ai/config/middleware.php` sẽ ảnh hưởng đến cả dự án chính và tất cả các phần cắm.

## Thêm middleware cho một phần cắm nào đó

> **Lưu ý**
> Tính năng này yêu cầu webman-framework >= 1.5.16

Đôi khi chúng ta muốn thêm một middleware cho một [phần cắm ứng dụng](app/app.md) nào đó, nhưng không muốn thay đổi mã nguồn của phần cắm đó (vì sẽ bị ghi đè khi nâng cấp), lúc đó chúng ta có thể cấu hình middleware cho nó trong dự án chính.

Cấu hình trong `config/middleware.php` như sau：
```php
return [
    'plugin.ai' => [], // Thêm middleware cho phần cắm ai
    'plugin.ai.admin' => [], // Thêm middleware cho phần cắm admin của ai
];
```

> **Gợi ý**
> Tất nhiên cũng có thể cấu hình tương tự trong một phần cắm khác để ảnh hưởng đến các phần cắm khác, ví dụ như thêm cấu hình trên vào tệp `plugin/foo/config/middleware.php` sẽ ảnh hưởng đến phần cắm ai.
