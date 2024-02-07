# Vòng đời

## Vòng đời của tiến trình
- Mỗi tiến trình có vòng đời rất dài
- Mỗi tiến trình chạy độc lập mà không làm ảnh hưởng đến nhau
- Mỗi tiến trình trong vòng đời của nó có thể xử lý nhiều yêu cầu
- Tiến trình sẽ thoát khi nhận lệnh `dừng` `tải lại` `khởi động lại`, kết thúc vòng đời lần này

> **Lưu ý**
> Mỗi tiến trình đều hoạt động mà không làm ảnh hưởng đến nhau, điều này có nghĩa là mỗi tiến trình duy trì các tài nguyên, biến và các thể hiện lớp của chính nó, có nghĩa là mỗi tiến trình có kết nối cơ sở dữ liệu của riêng mình, một số thể hiện đơn lẻ được khởi tạo mỗi khi một tiến trình bắt đầu, vì vậy nhiều tiến trình sẽ được khởi tạo nhiều lần.

## Vòng đời của yêu cầu
- Mỗi yêu cầu sẽ tạo ra một đối tượng `$request`
- Đối tượng `$request` sẽ được thu hồi sau khi xử lý yêu cầu

## Vòng đời của bộ điều khiển
- Mỗi bộ điều khiển chỉ được khởi tạo một lần cho mỗi tiến trình, và khởi tạo nhiều lần cho nhiều tiến trình (ngoại trừ việc tắt tái sử dụng bộ điều khiển, xem [Vòng đời của bộ điều khiển](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- Thể hiện của bộ điều khiển sẽ được chia sẻ giữa nhiều yêu cầu trong cùng một tiến trình (ngoại trừ việc tắt tái sử dụng bộ điều khiển)
- Vòng đời của bộ điều khiển kết thúc khi tiến trình thoát (ngoại trừ việc tắt tái sử dụng bộ điều khiển)

## Về vòng đời của biến
webman được phát triển dựa trên PHP, vì vậy nó hoàn toàn tuân theo cơ chế thu hồi biến của PHP. Các biến tạm thời được tạo ra trong logic kinh doanh, bao gồm các thể hiện của lớp được tạo ra bằng từ khóa `new`, sẽ được thu hồi tự động sau khi hàm hoặc phương thức kết thúc, không cần phải `unset` thủ công. Điều này có nghĩa là việc phát triển webman có trải nghiệm gần giống như phát triển trên các framework truyền thống. Ví dụ dưới đây cho thấy thể hiện `$foo` sẽ được thu hồi sau khi phương thức index thực thi xong:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Giả sử có một lớp Foo ở đây
        return response($foo->sayHello());
    }
}
```
Nếu bạn muốn thể hiện của một lớp được sử dụng lại, bạn có thể lưu trữ nó trong thuộc tính tĩnh của lớp hoặc trong thuộc tính của một đối tượng có vòng đời lâu dài (như bộ điều khiển), bạn cũng có thể sử dụng phương thức get của Container để khởi tạo thể hiện của lớp, ví dụ:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```
Phương thức `Container::get()` được sử dụng để tạo và lưu trữ thể hiện của lớp, và khi gọi lại với cùng tham số, nó sẽ trả về thể hiện của lớp đã được tạo trước đó.

> **Lưu ý**
> `Container::get()` chỉ có thể khởi tạo thể hiện không có tham số tạo. `Container::make()` có thể tạo thể hiện với các tham số tạo, nhưng khác với `Container::get()`, `Container::make()` sẽ không tái sử dụng thể hiện, điều này có nghĩa là ngay cả khi gọi lại với cùng tham số, `Container::make()` sẽ luôn trả về một thể hiện mới.

## Về rò rỉ bộ nhớ
Trong hầu hết các trường hợp, mã kinh doanh của chúng ta không gây ra rò rỉ bộ nhớ (có rất ít người dùng phản ánh rò rỉ bộ nhớ), chúng ta chỉ cần chú ý đến việc không mở rộng vô hạn dữ liệu mảng có vòng đời lâu. Hãy xem mã sau đây:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Thuộc tính mảng
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('Xin chào index');
    }

    public function hello(Request $request)
    {
        return response('Xin chào webman');
    }
}
```
Mặc định, bộ điều khiển là có vòng đời dài (ngoại trừ việc tắt tái sử dụng bộ điều khiển), thuộc tính mảng `$data` của cùng một bộ điều khiển cũng có vòng đời dài, với việc thêm dần vào mảng `$data` với mỗi yêu cầu `foo/index`, số phần tử trong mảng `$data` sẽ ngày càng tăng, dẫn đến rò rỉ bộ nhớ.

Xem thêm thông tin liên quan tại [Rò rỉ bộ nhớ](./memory-leak.md)
