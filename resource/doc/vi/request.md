# Giới thiệu

## Nhận đối tượng yêu cầu
Webman sẽ tự động tiêm đối tượng yêu cầu vào đối số đầu tiên của phương thức action, ví dụ

**Ví dụ**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Lấy tham số name từ yêu cầu get, nếu không có tham số name được truyền thì trả về $default_name
        $name = $request->get('name', $default_name);
        // Trả về chuỗi cho trình duyệt
        return response('hello ' . $name);
    }
}
```

Chúng ta có thể lấy bất kỳ dữ liệu nào liên quan đến yêu cầu thông qua đối tượng `$request`.

**Đôi khi chúng ta muốn lấy đối tượng `$request` của yêu cầu hiện tại trong một lớp khác. Lúc này, chúng ta chỉ cần sử dụng hàm trợ giúp `request()`**;

## Lấy tham số yêu cầu từ phương thức get

**Lấy toàn bộ mảng get**
```php
$request->get();
```
Nếu không có tham số get được yêu cầu thì trả về một mảng rỗng.

**Lấy một giá trị từ mảng get**
```php
$request->get('name');
```
Nếu mảng get không chứa giá trị này thì trả về null.

Bạn cũng có thể truyền một giá trị mặc định là tham số thứ hai cho phương thức get. Nếu không tìm thấy giá trị tương ứng trong mảng get thì trả về giá trị mặc định. Ví dụ:
```php
$request->get('name', 'tom');
```

## Lấy tham số yêu cầu từ phương thức post
**Lấy toàn bộ mảng post**
```php
$request->post();
```
Nếu không có tham số post được yêu cầu thì trả về một mảng rỗng.

**Lấy một giá trị từ mảng post**
```php
$request->post('name');
```
Nếu mảng post không chứa giá trị này thì trả về null.

Tương tự như phương thức get, bạn cũng có thể truyền một giá trị mặc định là tham số thứ hai cho phương thức post. Nếu không tìm thấy giá trị tương ứng trong mảng post thì trả về giá trị mặc định. Ví dụ:
```php
$request->post('name', 'tom');
```

## Lấy gói tin post gốc của yêu cầu
```php
$post = $request->rawBody();
```
Chức năng này tương tự như hoạt động `file_get_contents("php://input");` trong `php-fpm`. Dùng để lấy gói tin yêu cầu gốc của http. Điều này hữu ích khi lấy dữ liệu yêu cầu post không phải dạng `application/x-www-form-urlencoded`. 


## Lấy header
**Lấy toàn bộ mảng header**
```php
$request->header();
```
Nếu không có tham số header được yêu cầu thì trả về một mảng rỗng. Lưu ý tất cả các khóa đều viết thường.

**Lấy một giá trị từ mảng header**
```php
$request->header('host');
```
Nếu mảng header không chứa giá trị này thì trả về null. Lưu ý tất cả các khóa đều viết thường.

Tương tự như phương thức get, bạn cũng có thể truyền một giá trị mặc định là tham số thứ hai cho phương thức header. Nếu không tìm thấy giá trị tương ứng trong mảng header thì trả về giá trị mặc định. Ví dụ:
```php
$request->header('host', 'localhost');
```

## Lấy cookie
**Lấy toàn bộ mảng cookie**
```php
$request->cookie();
```
Nếu không có tham số cookie được yêu cầu thì trả về một mảng rỗng.

**Lấy một giá trị từ mảng cookie**
```php
$request->cookie('name');
```
Nếu mảng cookie không chứa giá trị này thì trả về null.

Tương tự như phương thức get, bạn cũng có thể truyền một giá trị mặc định là tham số thứ hai cho phương thức cookie. Nếu không tìm thấy giá trị tương ứng trong mảng cookie thì trả về giá trị mặc định. Ví dụ:
```php
$request->cookie('name', 'tom');
```

## Lấy toàn bộ dữ liệu yêu cầu
Bao gồm tập hợp của `post` và `get`.
```php
$request->all();
```

## Lấy giá trị yêu cầu cụ thể
Lấy một giá trị từ tập hợp của `post` và `get`.
```php
$request->input('name', $default_value);
```

## Lấy một phần dữ liệu yêu cầu
Lấy dữ liệu từ tập hợp của `post` và `get`.
```php
// Lấy mảng gồm username và password, nếu không có key tương ứng thì bỏ qua
$only = $request->only(['username', 'password']);
// Lấy tất cả dữ liệu trừ avatar và age
$except = $request->except(['avatar', 'age']);
```
## Nhận tệp tải lên
**Nhận mảng tệp tải lên toàn bộ**
```php
$request->file();
```

Mẫu biểu đạng như sau:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

Giá trị trả về của `$request->file()` có dạng như sau:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Đây là một mảng các thể hiện của `webman\Http\UploadFile`. Lớp `webman\Http\UploadFile` kế thừa từ lớp [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) tích hợp sẵn trong PHP và cung cấp một số phương thức hữu ích.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Tệp có hợp lệ hay không, ví dụ true|false
            var_export($spl_file->getUploadExtension()); // Đuôi của tệp tải lên, ví dụ 'jpg'
            var_export($spl_file->getUploadMimeType()); // Kiểu mine của tệp tải lên, ví dụ 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Nhận mã lỗi tải lên, ví dụ UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Tên tệp tải lên, ví dụ 'my-test.jpg'
            var_export($spl_file->getSize()); // Nhận kích thước tệp, ví dụ 13364, đơn vị byte
            var_export($spl_file->getPath()); // Nhận thư mục tải lên, ví dụ '/tmp'
            var_export($spl_file->getRealPath()); // Nhận đường dẫn tệp tạm thời, ví dụ `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Chú ý:**

- Sau khi tệp tải lên, tệp sẽ được đặt tên là tệp tạm thời, ví dụ `/tmp/workerman.upload.SRliMu`
- Kích thước tệp tải lên bị giới hạn bởi [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), mặc định là 10M, có thể sửa đổi giá trị mặc định bằng cách chỉnh sửa `max_package_size` trong tệp `config/server.php`.
- Sau khi yêu cầu kết thúc, tệp tạm thời sẽ tự động bị xóa
- Nếu yêu cầu không có tệp tải lên thì `$request->file()` trả về một mảng trống
- Tệp tải lên không hỗ trợ phương thức `move_uploaded_file()`, hãy sử dụng phương thức `$file->move()` thay thế, xem ví dụ bên dưới

### Nhận cụ thể một tệp tải lên
```php
$request->file('avatar');
```
Nếu tệp tồn tại, thì trả về một thể hiện của `webman\Http\UploadFile` tương ứng với tệp, còn không thì trả về null.

**Ví dụ**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## Nhận host
Nhận thông tin host của yêu cầu.
```php
$request->host();
```
Nếu địa chỉ yêu cầu khác với cổng 80 hoặc 443 tiêu chuẩn, thông tin host có thể kèm theo cổng, ví dụ `example.com:8080`. Nếu không cần cổng, có thể truyền tham số đầu tiên là `true`.

```php
$request->host(true);
```

## Nhận phương thức yêu cầu
```php
$request->method();
```
Giá trị trả về có thể là `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, hoặc `HEAD`.

## Nhận uri yêu cầu
```php
$request->uri();
```
Trả về uri của yêu cầu, bao gồm phần path và queryString.

## Nhận phần path của yêu cầu
```php
$request->path();
```
Trả về phần path của yêu cầu.

## Nhận phần queryString của yêu cầu
```php
$request->queryString();
```
Trả về phần queryString của yêu cầu.

## Nhận url yêu cầu
Phương thức `url()` trả về URL không chứa tham số `Query`.
```php
$request->url();
```
Trả về giống như `//www.workerman.net/workerman-chat`

Phương thức `fullUrl()` trả về URL chứa tham số `Query`.
```php
$request->fullUrl();
```
Trả về giống như `//www.workerman.net/workerman-chat?type=download`

> **Chú ý**
> `url()` và `fullUrl()` không trả về phần giao thức (không trả về http hoặc https).
> Vì trình duyệt sử dụng địa chỉ bắt đầu bằng `//` để tự động nhận diện giao thức của trang web hiện tại và tự động thực hiện yêu cầu qua http hoặc https.

Nếu bạn sử dụng proxy nginx, hãy thêm `proxy_set_header X-Forwarded-Proto $scheme;` vào cấu hình nginx, [tham khảo proxy nginx](others/nginx-proxy.md),
sau đó bạn có thể sử dụng `$request->header('x-forwarded-proto');` để kiểm tra là http hay https, ví dụ:
```php
echo $request->header('x-forwarded-proto'); // In ra http hoặc https
```

## Nhận phiên session của yêu cầu
```php
$request->sessionId();
```
Trả về chuỗi gồm chữ cái và số

## Nhận địa chỉ IP của client yêu cầu
```php
$request->getRemoteIp();
```

## Nhận cổng của client yêu cầu
```php
$request->getRemotePort();
```
## Lấy địa chỉ IP thực sự của khách hàng yêu cầu
```php
$request->getRealIp($safe_mode=true);
```

Khi dự án sử dụng proxy (ví dụ như nginx), việc sử dụng `$request->getRemoteIp()` thường sẽ trả về địa chỉ IP của máy chủ proxy (giống như `127.0.0.1` `192.168.x.x`) thay vì địa chỉ IP thực sự của khách hàng. Lúc này, bạn có thể dùng `$request->getRealIp()` để lấy địa chỉ IP thực sự của khách hàng.

`$request->getRealIp()` sẽ cố gắng lấy địa chỉ IP thực sự từ các trường `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via` của tiêu đề HTTP.

> Do tiêu đề HTTP dễ bị giả mạo, nên địa chỉ IP của khách hàng lấy được từ phương thức này không phải luôn đáng tin cậy 100%, đặc biệt là khi `$safe_mode` là false. Phương pháp đáng tin cậy hơn để lấy địa chỉ IP thực sự của khách hàng thông qua proxy là biết địa chỉ IP của máy chủ proxy an toàn và biết chắc chắn tiêu đề HTTP nào chứa địa chỉ IP thực sự. Nếu địa chỉ IP trả về từ `$request->getRemoteIp()` xác nhận là của máy chủ proxy an toàn biết, sau đó có thể dùng `$request->header('tiêu đề chứa địa chỉ IP thực sự')` để lấy địa chỉ IP thực sự.


## Lấy địa chỉ IP của máy chủ
```php
$request->getLocalIp();
```

## Lấy cổng của máy chủ
```php
$request->getLocalPort();
```

## Kiểm tra liệu yêu cầu có phải là yêu cầu ajax hay không
```php
$request->isAjax();
```

## Kiểm tra liệu yêu cầu có phải là yêu cầu pjax hay không
```php
$request->isPjax();
```

## Kiểm tra liệu yêu cầu có phải là yêu cầu trả về dạng json hay không
```php
$request->expectsJson();
```

## Kiểm tra liệu khách hàng có chấp nhận trả về dữ liệu dạng json hay không
```php
$request->acceptJson();
```

## Lấy tên của plugin mà yêu cầu đang gửi đến
Đối với yêu cầu không phải từ plugin, sẽ trả về chuỗi rỗng `''`.
```php
$request->plugin;
```
> Tính năng này yêu cầu webman>=1.4.0

## Lấy tên của ứng dụng mà yêu cầu đang gửi đến
Khi chỉ có một ứng dụng, sẽ luôn trả về chuỗi rỗng `''`, [khi có nhiều ứng dụng](multiapp.md) sẽ trả về tên ứng dụng.
```php
$request->app;
```

> Vì hàm vô danh không thuộc về bất kỳ ứng dụng nào, nên yêu cầu từ router vô danh `$request->app` sẽ luôn trả về chuỗi rỗng `''`.
> Xem thêm về router vô danh tại [Định tuyến](route.md)

## Lấy tên của lớp điều khiển mà yêu cầu đang gửi đến
Lấy tên lớp tương ứng với điều khiển
```php
$request->controller;
```
Sẽ trả về giống như `app\controller\IndexController`

> Vì hàm vô danh không thuộc về bất kỳ điều khiển nào, nên yêu cầu từ router vô danh `$request->controller` sẽ luôn trả về chuỗi rỗng `''`.
> Xem thêm về router vô danh tại [Định tuyến](route.md)

## Lấy tên của phương thức yêu cầu
Lấy tên phương thức tương ứng với điều khiển
```php
$request->action;
```
Sẽ trả về giống như `index`

> Vì hàm vô danh không thuộc về bất kỳ điều khiển nào, nên yêu cầu từ router vô danh `$request->action` sẽ luôn trả về chuỗi rỗng `''`.
> Xem thêm về router vô danh tại [Định tuyến](route.md)
