# Quy tắc phát triển plugin ứng dụng

## Yêu cầu cho plugin ứng dụng
* Plugin không được chứa mã độc hại, biểu tượng, hình ảnh, v.v.
* Mã nguồn plugin phải là mã nguồn hoàn chỉnh và không được mã hóa
* Plugin phải có chức năng hoàn chỉnh, không được là chức năng đơn giản
* Phải cung cấp thông tin chức năng đầy đủ, tài liệu
* Plugin không được chứa các thành phố con
* Trong plugin không được chứa bất kỳ văn bản hoặc liên kết quảng cáo nào

## Định danh plugin ứng dụng
Mỗi plugin ứng dụng đều có một định danh duy nhất, được tạo thành từ các chữ cái. Định danh này ảnh hưởng đến tên thư mục mã nguồn plugin, không gian tên lớp và tiền tố bảng cơ sở dữ liệu cho plugin.

Giả sử nhà phát triển sử dụng "foo" làm định danh plugin, thì thư mục mã nguồn plugin sẽ là`{main_project}/plugin/foo`, không gian tên lớp tương ứng sẽ là `plugin\foo`, và tiền tố bảng sẽ là `foo_`.

Vì định danh là duy nhất trên toàn mạng, nhà phát triển cần kiểm tra tính khả dụng của định danh trước khi phát triển, có thể kiểm tra tại [Kiểm tra định danh ứng dụng](https://www.workerman.net/app/check).

## Cơ sở dữ liệu
* Tên bảng bao gồm chữ thường `a-z` và dấu gạch dưới `_`
* Bảng dữ liệu của plugin nên có tiền tố là định danh plugin, ví dụ bảng article của plugin foo sẽ là `foo_article`
* Khóa chính của bảng nên là id
* Sử dụng cùng một engine là innodb cho tất cả các bảng
* Sử dụng bộ ký tự utf8mb4_general_ci cho tất cả bảng
* Sử dụng ORM của laravel hoặc think-orm
* Khuyến nghị sử dụng trường thời gian là DateTime

## Quy tắc mã nguồn

#### Quy tắc PSR
Mã nguồn nên tuân theo quy tắc tải PSR4

#### Đặt tên lớp theo kiểu CamelCase với ký tự đầu viết hoa
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Đặt tên thuộc tính và phương thức của lớp theo kiểu CamelCase với ký tự đầu viết thường
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
Các thuộc tính và phương thức của lớp phải đi kèm với chú thích, bao gồm tóm tắt, tham số và kiểu trả về

#### Thụt lề
Mã nguồn nên sử dụng 4 dấu cách để thụt lề, không dùng ký tự tab

#### Điều khiển luồng
Sau từ khóa điều khiển luồng (if for while foreach, v.v) phải có một dấu cách, dấu mở ngoặc của điều khiển luồng phải ở cùng một hàng với từ khóa
```php
foreach ($users as $uid => $user) {

}
```

#### Tên biến tạm thời
Khuyến nghị sử dụng kiểu CamelCase với ký tự đầu viết thường cho tên biến tạm thời (không bắt buộc)
```php
$articleCount = 100;
```
