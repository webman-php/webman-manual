# Bộ điều khiển

Tạo một tệp điều khiển mới `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('xin chào index');
    }
    
    public function hello(Request $request)
    {
        return response('xin chào webman');
    }
}
```

Khi truy cập `http://127.0.0.1:8787/foo`, trang web sẽ trả về `xin chào index`.

Khi truy cập `http://127.0.0.1:8787/foo/hello`, trang web sẽ trả về `xin chào webman`.

Tất nhiên, bạn có thể thay đổi quy tắc định tuyến thông qua cấu hình định tuyến, xem [Định tuyến](route.md).

> **Gợi ý**
> Nếu gặp lỗi 404 không truy cập được, hãy mở tệp `config/app.php`, đặt `controller_suffix` thành `Controller`, sau đó khởi động lại.

## Hậu tố điều khiển
Từ phiên bản 1.3 trở lên, webman hỗ trợ cài đặt hậu tố điều khiển trong `config/app.php`. Nếu `controller_suffix` trong tệp `config/app.php` được đặt thành `''`, tên lớp điều khiển sẽ như sau

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('xin chào index');
    }
    
    public function hello(Request $request)
    {
        return response('xin chào webman');
    }
}
```

Đề xuất mạnh mẽ đặt hậu tố điều khiển thành `Controller`, điều này giúp tránh xung đột tên giữa nhiều lớp điều khiển và tăng cường tính an toàn.

## Giải thích
- Framework sẽ tự động truyền đối tượng `support\Request` vào điều khiển. Qua đối tượng này, bạn có thể lấy dữ liệu nhập từ người dùng (như dữ liệu get, post, header, cookie, vv.), xem [Yêu cầu](request.md).
- Trong điều khiển, bạn có thể trả về số, chuỗi hoặc đối tượng `support\Response`, nhưng không thể trả về bất kỳ loại dữ liệu khác.
- Đối tượng `support\Response` có thể được tạo ra thông qua các hàm trợ giúp như `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, và các hàm khác.

## Chu kỳ sống của điều khiển
Khi `controller_reuse` trong `config/app.php` là `false`, mỗi yêu cầu sẽ khởi tạo một lần phiên bản điều khiển tương ứng, sau khi yêu cầu kết thúc, phiên bản điều khiển sẽ bị hủy bỏ, tương tự như cách hoạt động của framework truyền thống.

Khi `controller_reuse` trong `config/app.php` là `true`, tất cả các yêu cầu sẽ sử dụng lại phiên bản điều khiển, nghĩa là một khi phiên bản điều khiển được tạo ra, nó sẽ ở trong bộ nhớ và sẽ được sử dụng lại cho tất cả các yêu cầu.

> **Chú ý**
> Để tắt việc sử dụng lại điều khiển, cần sử dụng webman>=1.4.0, nghĩa là trước phiên bản 1.4.0, việc sử dụng lại điều khiển mặc định là bật và không thể thay đổi.

> **Chú ý**
> Khi bật việc sử dụng lại điều khiển, yêu cầu không nên thay đổi bất kỳ thuộc tính của điều khiển nào, vì những thay đổi này sẽ ảnh hưởng đến các yêu cầu sau này, ví dụ:

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
        // Phương thức này sẽ giữ lại $model sau lần yêu cầu update?id=1 đầu tiên
        // Nếu yêu cầu lại delete?id=2, dữ liệu của 1 sẽ bị xóa đi
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Gợi ý**
> Trong hàm tạo `__construct()` của điều khiển, việc trả về dữ liệu sẽ không có tác dụng gì cả, ví dụ:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Trả về dữ liệu trong hàm tạo không có tác dụng gì cả, trình duyệt sẽ không nhận được phản hồi này
        return response('xin chào'); 
    }
}
```

## Sự khác biệt giữa việc sử dụng lại và không sử dụng lại điều khiển
Sự khác biệt như sau:

#### Không sử dụng lại điều khiển
Mỗi yêu cầu sẽ tạo một phiên bản điều khiển mới, sau khi kết thúc yêu cầu, phiên bản này sẽ bị giải phóng và giải phóng bộ nhớ. Không sử dụng lại điều khiển giống như hoạt động của framework truyền thống, phù hợp với hầu hết thói quen phát triển của người dùng. Do việc tạo và giải phóng điều khiển nhiều lần, hiệu suất sẽ hơi kém hơn so với việc sử dụng lại điều khiển (hiệu suất kiểm tra với chức năng hello world kém 10% xấp xỉ, có thể bỏ qua khi có chức năng thực tế)

#### Sử dụng lại điều khiển
Nếu sử dụng lại, một tiến trình chỉ tạo một lần phiên bản điều khiển, sau khi yêu cầu kết thúc, phiên bản điều khiển này không được giải phóng, và các yêu cầu sau này trong tiến trình sẽ sử dụng lại phiên bản điều khiển này. Sử dụng lại điều khiển có hiệu suất tốt hơn, nhưng không phù hợp với hầu hết thói quen phát triển của người dùng.

#### Trường hợp không nên sử dụng lại điều khiển

Khi yêu cầu làm thay đổi thuộc tính của điều khiển, không thể bật sự sử dụng lại điều khiển, vì những thay đổi này sẽ ảnh hưởng đến các yêu cầu sau này.

Một số nhà phát triển thích thực hiện một số khởi tạo cho mỗi yêu cầu trong hàm tạo `__construct()` của điều khiển, lúc đó không thể sử dụng lại điều khiển, vì hàm tạo của mỗi tiến trình chỉ được gọi một lần và không phải là mỗi yêu cầu.
