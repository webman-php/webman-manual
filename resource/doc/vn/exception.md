# Xử lý ngoại lệ

## Cấu hình
`config/exception.php`
```php
return [
    // Configura các lớp xử lý ngoại lệ ở đây
    '' => support\exception\Handler::class,
];
```
Trong chế độ ứng dụng đa ứng dụng, bạn có thể cấu hình lớp xử lý ngoại lệ riêng cho mỗi ứng dụng, xem thêm [Ứng dụng đa](multiapp.md)


## Lớp xử lý ngoại lệ mặc định
Trong webman, ngoại lệ mặc định được xử lý bởi lớp `support\exception\Handler`. Bạn có thể thay đổi lớp xử lý ngoại lệ mặc định bằng cách sửa file cấu hình `config/exception.php`. Lớp xử lý ngoại lệ phải thực hiện interface `Webman\Exception\ExceptionHandlerInterface` như sau:
```php
interface ExceptionHandlerInterface
{
    /**
     * Ghi nhật ký
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Hiển thị phản hồi
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## Hiển thị phản hồi
Phương thức `render` trong lớp xử lý ngoại lệ được sử dụng để hiển thị phản hồi.

Nếu giá trị `debug` trong file cấu hình `config/app.php` là `true` (gọi tắt là `app.debug=true`), thông tin ngoại lệ chi tiết sẽ được trả về, ngược lại sẽ trả về thông tin ngoại lệ rút gọn.

Nếu yêu cầu trả về dưới dạng json, thông tin ngoại lệ sẽ được trả về dưới dạng json như sau:
```json
{
    "code": "500",
    "msg": "Thông tin ngoại lệ"
}
```
Nếu `app.debug=true`, dữ liệu json sẽ bổ sung một trường `trace` để trả về ngăn xếp cuộc gọi chi tiết.

Bạn có thể viết lớp xử lý ngoại lệ riêng để thay đổi logic xử lý ngoại lệ mặc định.

# Ngoại lệ kinh doanh BusinessException
Đôi khi chúng ta muốn dừng yêu cầu trong một hàm lồng nhau và trả về một thông báo lỗi cho máy khách, lúc này có thể sử dụng `BusinessException` để làm điều này.
Ví dụ:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Lỗi tham số', 3000);
        }
    }
}
```

Trong ví dụ trên sẽ trả về một
```json
{"code": 3000, "msg": "Lỗi tham số"}
```

> **Lưu ý**
> Ngoại lệ kinh doanh BusinessException không cần phải được try-catch bởi logic kinh doanh, framework sẽ tự động bắt và trả về kết quả phù hợp với loại yêu cầu.

## Ngoại lệ kinh doanh tùy chỉnh

Nếu kết quả trả về trong phần trả về không phù hợp với yêu cầu của bạn và muốn thay đổi `msg` thành `message`, bạn có thể tạo lớp ngoại lệ `MyBusinessException` tùy chỉnh.

Tạo file `app/exception/MyBusinessException.php` với nội dung sau:
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // Trả về dữ liệu json nếu yêu cầu là json
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Trả về một trang không phải json nếu không phải json
        return new Response(200, [], $this->getMessage());
    }
}
```

Khi gọi logic kinh doanh
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Lỗi tham số', 3000);
```
yêu cầu json sẽ nhận được kết quả json như sau:
```json
{"code": 3000, "message": "Lỗi tham số"}
```

> **Gợi ý**
> Vì ngoại lệ BusinessException thuộc loại ngoại lệ kinh doanh (ví dụ: lỗi tham số đầu vào người dùng), nó là có thể dự đoán được, vì vậy framework không coi đó là lỗi nghiêm trọng và không ghi nhật ký.

## Tóm lại
Bạn có thể xem xét việc sử dụng ngoại lệ `BusinessException` mỗi khi cần dừng yêu cầu hiện tại và trả về thông tin cho máy khách.
