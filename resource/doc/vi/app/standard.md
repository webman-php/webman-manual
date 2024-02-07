# Tiêu chuẩn phát triển plugin ứng dụng

## Yêu cầu cho plugin ứng dụng
* Plugin không được chứa mã vi phạm bản quyền, biểu tượng, hình ảnh, v.v.
* Mã nguồn của plugin phải là mã nguồn đầy đủ và không được mã hóa
* Plugin phải có chức năng đầy đủ, không được là chức năng đơn giản
* Phải cung cấp hướng dẫn sử dụng đầy đủ và tài liệu
* Plugin không được chứa các thành phần con
* Trong plugin không được chứa bất kỳ từ hoặc liên kết quảng cáo nào

## Định danh của plugin ứng dụng
Mỗi plugin ứng dụng đều có một định danh duy nhất, được tạo thành từ các ký tự chữ cái. Định danh này ảnh hưởng đến tên thư mục nguồn của plugin, không gian tên của lớp và tiền tố bảng cơ sở dữ liệu của plugin.

Giả sử người phát triển sử dụng "foo" làm định danh cho plugin, thì thư mục nguồn của plugin sẽ là `{thư mục gốc}/plugin/foo`, không gian tên tương ứng của plugin sẽ là `plugin\foo`, và tiền tố bảng sẽ là `foo_`.

Do định danh này duy nhất trên toàn mạng, vì vậy người phát triển cần kiểm tra tính khả dụng của định danh trước khi phát triển, và có thể kiểm tra tại địa chỉ [Kiểm tra định danh ứng dụng](https://www.workerman.net/app/check).

## Cơ sở dữ liệu
* Tên bảng cần được tạo bởi các ký tự thường từ `a` đến `z` và gạch dưới `_`
* Bảng dữ liệu của plugin nên được tạo với tiền tố là định danh của plugin, ví dụ bảng article của plugin "foo" sẽ là `foo_article`
* Khóa chính của bảng cần sử dụng id làm chỉ mục
* Sử dụng đồng nhất công cụ lưu trữ là động cơ innodb
* Sử dụng tập ký tự đồng nhất là utf8mb4_general_ci
* Có thể sử dụng ORM của Laravel hoặc ThinkORM
* Cột thời gian nên sử dụng kiểu dữ liệu DateTime

## Quy tắc mã nguồn

#### Quy ước PSR
Mã nguồn phải tuân thủ quy ước PSR4 về việc tải và sử dụng

#### Tên lớp bắt đầu bằng chữ cái viết hoa và theo kiểu Camel Case
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Thuộc tính và phương thức của lớp bắt đầu bằng chữ cái viết thường và theo kiểu Camel Case
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Các phương thức không cần xác thực
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Lấy nhận xét
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Chú thích
Cần có chú thích cho các thuộc tính và phương thức của lớp, bao gồm tóm tắt, tham số và kiểu trả về

#### Lùi đầu dòng
Mã nguồn cần sử dụng 4 dấu cách để lùi đầu dòng, không nên sử dụng tab

#### Điều khiển luồng
Sau các từ khóa điều khiển luồng (if for while foreach, v.v.) cần có một khoảng trắng, dấu ngoặc nhọn bắt đầu luồng cần ở cùng một hàng với dấu ngoặc đóng.

```php
foreach ($users as $uid => $user) {

}
```

#### Tên biến tạm thời
Khuyến nghị sử dụng kiểu Camel Case với chữ cái viết thường bắt đầu (không bắt buộc)

```php
$articleCount = 100;
```
