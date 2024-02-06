# Vòng đời

## Vòng đời của tiến trình
- Mỗi tiến trình có một vòng đời dài
- Mỗi tiến trình chạy độc lập không tác động lẫn nhau
- Mỗi tiến trình có thể xử lý nhiều yêu cầu trong vòng đời của mình
- Khi nhận lệnh `stop` `reload` `restart`, tiến trình sẽ kết thúc và chấm dứt vòng đời hiện tại

> **Lưu ý**
> Mỗi tiến trình đều hoạt động độc lập không tác động lẫn nhau, điều này có nghĩa là mỗi tiến trình duy trì tài nguyên, biến và các trường hợp của lớp riêng, điều này biểu hiện trong việc mỗi tiến trình có kết nối cơ sở dữ liệu của riêng mình, một số singleton được khởi tạo mỗi khi bắt đầu một tiến trình, điều này có nghĩa là nhiều tiến trình sẽ được khởi tạo nhiều lần.

## Vòng đời của yêu cầu
- Mỗi yêu cầu sẽ tạo ra một đối tượng `$request`
- `$request` sẽ được thu hồi sau khi xử lý yêu cầu xong

## Vòng đời của điều khiển
- Mỗi điều khiển chỉ được khởi tạo một lần cho mỗi tiến trình, nếu không có tái sử dụng điều khiển (xem [Vòng đời của điều khiển](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- Thể hiện điều khiển sẽ được chia sẻ giữa nhiều yêu cầu trong cùng một tiến trình (nếu không có tái sử dụng điều khiển)
- Vòng đời của điều khiển sẽ kết thúc khi tiến trình kết thúc (nếu không có tái sử dụng điều khiển)

## Về vòng đời của biến
webman được phát triển dựa trên PHP, do đó hoàn toàn tuân theo cơ chế thu hồi biến của PHP. Các biến tạm thời được tạo trong logic kinh doanh, bao gồm cả thể hiện của lớp được tạo bằng từ khóa `new`, sẽ được thu hồi tự động khi hàm hoặc phương thức kết thúc mà không cần phải gọi `unset` thủ công. Điều này có nghĩa là quá trình phát triển trong webman cơ bản tương tự như phát triển trong các framework truyền thống. Ví dụ, thể hiện `$foo` trong ví dụ sau sẽ tự động giải phóng sau khi phương thức index thực thi xong:

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

Nếu bạn muốn tái sử dụng thể hiện của một lớp nào đó, bạn có thể lưu trữ lớp đó vào thuộc tính tĩnh của lớp hoặc thuộc tính của đối tượng với vòng đời lâu dài (ví dụ như điều khiển), hoặc sử dụng phương thức `get` của Container để khởi tạo thể hiện lớp, như sau:

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

Phương thức `Container::get()` được sử dụng để khởi tạo và lưu trữ thể hiện của lớp, thay vì trả về thể hiện của lớp đã được tạo trước đó khi gọi lại với cùng tham số.

> **Chú ý**
> `Container::get()` chỉ có thể khởi tạo thể hiện không có tham số xây dựng. `Container::make()` có thể tạo thể hiện có tham số xây dựng, tuy nhiên, khác với `Container::get()`, `Container::make()` không tái sử dụng thể hiện, điều này có nghĩa là, giả sử gọi `Container::make()` với cùng các tham số, nó vẫn là thể hiện mới.

# Về rò rỉ bộ nhớ
Trong hầu hết các trường hợp, mã kinh doanh của chúng ta không gây ra rò rỉ bộ nhớ (rất ít người dùng phản ánh về rò rỉ bộ nhớ), chúng ta chỉ cần chú ý một chút đến việc không mở rộng vô hạn dữ liệu mảng có vòng đời lâu. Ví dụ:

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

Mặc định, điều khiển có vòng đời lâu (nếu không tái sử dụng điều khiển), cũng giống như mảng `$data` của điều khiển cũng có vòng đời lâu, qua mỗi yêu cầu `foo/index`, số phần tử của mảng `$data` ngày càng tăng dẫn đến rò rỉ bộ nhớ.

Để biết thêm thông tin liên quan, vui lòng tham khảo [Rò rỉ bộ nhớ](./memory-leak.md)
