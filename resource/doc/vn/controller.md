# Bộ điều khiển

Tạo tệp điều khiển mới `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Khi truy cập `http://127.0.0.1:8787/foo`, trang web sẽ trả về `hello index`.

Khi truy cập `http://127.0.0.1:8787/foo/hello`, trang web sẽ trả về `hello webman`.

Tất nhiên, bạn có thể thay đổi quy tắc định tuyến bằng cách cấu hình đường dẫn, xem [định tuyến](route.md) để biết thêm chi tiết.

> **Lưu ý**
> Nếu gặp lỗi 404 không thể truy cập, hãy mở tệp `config/app.php`, đặt `controller_suffix` thành `Controller`, và khởi động lại.

## Hậu tố điều khiển
Webman từ phiên bản 1.3 trở lên hỗ trợ cài đặt hậu tố điều khiển trong `config/app.php`. Nếu `controller_suffix` trong `config/app.php` được đặt thành chuỗi trống `''`, tên điều khiển sẽ trông như sau:

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Chúng tôi đề xuất đặt hậu tố điều khiển thành `Controller`, điều này sẽ giúp tránh xung đột tên giữa điều khiển và lớp mô hình, đồng thời tăng cường tính bảo mật.

## Giải thích
 - Framework sẽ tự động truyền đối tượng `support\Request` vào điều khiển, thông qua đó bạn có thể lấy dữ liệu nhập từ người dùng (dữ liệu get, post, header, cookie, v.v.), xem thêm [yêu cầu](request.md)
 - Trong điều khiển, bạn có thể trả về số, chuỗi hoặc đối tượng `support\Response`, nhưng không thể trả về loại dữ liệu khác.
 - Đối tượng `support\Response` có thể được tạo thông qua các hàm trợ giúp như `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` v.v.

## Vòng đời điều khiển

Khi `controller_reuse` trong `config/app.php` được đặt là `false`, mỗi yêu cầu sẽ khởi tạo một lần instance điều khiển tương ứng, sau khi yêu cầu kết thúc, instance điều khiển sẽ bị hủy bỏ và giải phóng bộ nhớ, tương tự như cách mà cấu trúc khung truyền thống hoạt động.

Khi `controller_reuse` trong `config/app.php` được đặt là `true`, tất cả các yêu cầu sẽ tái sử dụng instance điều khiển, có nghĩa là một khi instance điều khiển được tạo ra, nó sẽ tồn tại trong bộ nhớ, và sẽ được tái sử dụng cho tất cả các yêu cầu.

> **Lưu ý**
> Để tắt việc tái sử dụng điều khiển, yêu cầu webman>=1.4.0. Điều này có nghĩa là trước phiên bản 1.4.0, điều khiển mặc định sẽ tái sử dụng tất cả các yêu cầu và không thể thay đổi.

> **Lưu ý**
> Khi bật tái sử dụng điều khiển, yêu cầu không nên thay đổi bất kỳ thuộc tính của điều khiển nào, vì những thay đổi này sẽ ảnh hưởng đến các yêu cầu tiếp theo, ví dụ

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // Phương thức này sẽ lưu giữ model sau khi yêu cầu update?id=1 được gọi
        // Nếu yêu cầu delete?id=2 được gọi sau đó, dữ liệu với id=1 sẽ bị xóa đi
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Lưu ý**
> Trong hàm tạo `__construct()` của điều khiển, việc trả về dữ liệu sẽ không có tác dụng gì cả, ví dụ

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Trả về dữ liệu trong hàm tạo không có tác dụng gì cả, trình duyệt sẽ không nhận phản hồi này
        return response('hello'); 
    }
}
```

## Sự khác biệt giữa việc không tái sử dụng và tái sử dụng điều khiển
Các sự khác biệt như sau:

#### Không tái sử dụng điều khiển
Mỗi yêu cầu sẽ tạo ra một thể hiện điều khiển mới, sau khi yêu cầu kết thúc, thể hiện này sẽ được giải phóng và giải phóng bộ nhớ. Không tái sử dụng điều khiển giống như hoạt động của các cấu trúc khung truyền thống, phù hợp với hầu hết thói quen phát triển của các nhà phát triển. Do việc điều khiển được tạo và giải phóng lặp đi lặp lại, nên hiệu suất sẽ ít tốt hơn so với việc tái sử dụng điều khiển (khả năng hiệu suất kém khoảng 10% trong trường hợp kiểm tra hiệu suất đơn giản, nhưng với công việc thực tế, điều này có thể được bỏ qua).

#### Tái sử dụng điều khiển
Khi tái sử dụng, một quá trình chỉ tạo ra một lần thể hiện điều khiển, sau khi yêu cầu kết thúc, thể hiện này sẽ không bị giải phóng, và sẽ được tái sử dụng cho tất cả các yêu cầu tiếp theo của quá trình hiện tại. Việc tái sử dụng điều khiển sẽ giúp cải thiện hiệu suất, nhưng không phù hợp với hầu hết thói quen phát triển của các nhà phát triển.

#### Các trường hợp không thể sử dụng tái sử dụng điều khiển
Khi yêu cầu thay đổi thuộc tính của điều khiển, không thể bật tái sử dụng điều khiển, vì việc thay đổi này sẽ ảnh hưởng đến các yêu cầu tiếp theo.

Một số nhà phát triển thích thực hiện một số khởi tạo cho mỗi yêu cầu trong hàm tạo của điều khiển `__construct()`, trong trường hợp này, không thể tái sử dụng điều khiển, vì hàm tạo trong quá trình chỉ được gọi một lần và không phải là mỗi yêu cầu.
