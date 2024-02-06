## Tùy chỉnh trang 404
Khi gặp lỗi 404, webman sẽ tự động trả về nội dung trong `public/404.html`, do đó nhà phát triển có thể thay đổi trực tiếp tệp `public/404.html`.

Nếu bạn muốn kiểm soát nội dung của trang 404 động, ví dụ: trả về dữ liệu json trong yêu cầu ajax `{"code:"404", "msg":"404 not found"}`, và trả về mẫu `app/view/404.html` khi có yêu cầu trang, vui lòng tham khảo ví dụ dưới đây

> Ở đây, chúng ta lấy mẫu php nguyên thủy, các mẫu khác như `twig`, `blade`, `think-template` cũng có nguyên tắc tương tự

**Tạo tệp `app/view/404.html`**
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

**Thêm mã sau vào `config/route.php`**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Trả về json khi có yêu cầu ajax
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Trả về mẫu 404.html khi có yêu cầu trang
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Tùy chỉnh trang 500
**Tạo tệp mới `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Tùy chỉnh mẫu lỗi:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Tạo** `app/exception/Handler.php`**(nếu thư mục không tồn tại, vui lòng tự tạo)**
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
        // Trả về dữ liệu json khi có yêu cầu ajax
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Trang web yêu cầu trả về mẫu 500.html
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Cấu hình `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
