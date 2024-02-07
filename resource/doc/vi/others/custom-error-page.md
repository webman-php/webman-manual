## Tùy chỉnh 404
Khi gặp lỗi 404, webman sẽ tự động trả về nội dung trong tệp `public/404.html`, vì vậy các nhà phát triển có thể chỉnh sửa trực tiếp tệp `public/404.html`.

Nếu bạn muốn kiểm soát nội dung lỗi 404 theo cách động, ví dụ như trả về dữ liệu json `{"code:"404", "msg":"404 not found"}` trong yêu cầu ajax, và trả về mẫu `app/view/404.html` khi yêu cầu trang web, vui lòng tham khảo ví dụ sau.

> Dưới đây là ví dụ sử dụng mẫu gốc PHP, các mẫu khác như `twig`, `blade`, `think-template` cũng tương tự.

**Tạo tệp `app/view/404.html`:**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**Trong `config/route.php`, thêm đoạn mã sau:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Trả về json trong yêu cầu ajax
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Trả về mẫu 404.html trong yêu cầu trang web
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Tùy chỉnh 500
**Tạo tệp `app/view/500.html`:**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Mẫu lỗi tùy chỉnh:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Tạo tệp** `app/exception/Handler.php` **(nếu thư mục không tồn tại, hãy tự tạo):**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Render và trả về
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Trả về dữ liệu json trong yêu cầu ajax
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Trả về mẫu 500.html trong yêu cầu trang web
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Cấu hình `config/exception.php`:**
```php
return [
    '' => \app\exception\Handler::class,
];
```
