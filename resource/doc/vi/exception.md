# Xử lý ngoại lệ

## Cấu hình
`config/exception.php`
```php
return [
    // Cấu hình lớp xử lý ngoại lệ ở đây
    '' => support\exception\Handler::class,
];
```
Trong chế độ ứng dụng nhiều, bạn có thể cấu hình riêng cho từng ứng dụng lớp xử lý ngoại lệ, xem thêm ở [multipleapp.md](multiapp.md)


## Lớp xử lý ngoại lệ mặc định
Trong webman, ngoại lệ mặc định được xử lý bởi lớp `support\exception\Handler`. Bạn có thể thay đổi lớp xử lý ngoại lệ mặc định bằng cách chỉnh sửa tệp cấu hình `config/exception.php`. Lớp xử lý ngoại lệ phải triển khai giao diện `Webman\Exception\ExceptionHandlerInterface`.
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
     * Trình bày phản hồi
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## Kết xuất phản hồi
Phương thức `render` trong lớp xử lý ngoại lệ được sử dụng để kết xuất phản hồi.

Nếu giá trị `debug` trong tệp cấu hình `config/app.php` được đặt thành `true` (gọi là `app.debug=true` sau đây), sẽ trả về thông tin ngoại lệ chi tiết. Ngược lại, sẽ trả về thông tin ngoại lệ gọn.

Nếu yêu cầu trả về dưới dạng json, thông tin ngoại lệ sẽ được trả về dưới dạng json, ví dụ
```json
{
    "code": "500",
    "msg": "Thông tin ngoại lệ"
}
```
Nếu `app.debug=true`, dữ liệu json sẽ bổ sung một trường `trace` để trả về ngăn xếp gọi chi tiết.

Bạn có thể viết lớp xử lý ngoại lệ của riêng mình để thay đổi logic xử lý ngoại lệ mặc định.

# Ngoại lệ kinh doanh BusinessException
Đôi khi chúng ta muốn dừng yêu cầu trong một hàm lồng nhau và trả về thông báo lỗi cho máy khách, có thể sử dụng `BusinessException` để làm điều này.
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
            throw new BusinessException('Tham số sai', 3000);
        }
    }
}
```

Trong ví dụ trên, sẽ trả về
```json
{"code": 3000, "msg": "Tham số sai"}
```

> **Chú ý**
> BusinessException không cần phải được bắt ngoại lệ kinh doanh, framework sẽ tự động bắt và trả về đầu ra phù hợp dựa trên loại yêu cầu.

## Tùy chỉnh ngoại lệ kinh doanh

Nếu phản hồi trên không đáp ứng nhu cầu của bạn, ví dụ: muốn thay đổi từ `msg` thành `message`, bạn có thể tạo một `MyBusinessException`

Tạo tệp `app/exception/MyBusinessException.php` với nội dung như sau
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
        // Trả về một trang web nếu không phải là yêu cầu json
        return new Response(200, [], $this->getMessage());
    }
}
```

Khi gọi
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Tham số sai', 3000);
```
yêu cầu trả về json sẽ nhận được phản hồi json tương tự như sau
```json
{"code": 3000, "message": "Tham số sai"}
```

> **Lưu ý**
> Vì BusinessException thuộc loại ngoại lệ kinh doanh (ví dụ: lỗi đầu vào của người dùng), nó có thể được dự đoán, vì vậy framework sẽ không coi nó là lỗi chết người và không ghi nhật ký.

## Tóm lược
Khi cần ngừng yêu cầu hiện tại và trả về thông tin cho máy khách, bạn có thể xem xét sử dụng `BusinessException`.
