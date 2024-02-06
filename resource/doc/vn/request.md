# Hướng dẫn

## Lấy đối tượng yêu cầu
webman sẽ tự động chèn đối tượng yêu cầu vào đối số đầu tiên của phương thức hành động, ví dụ


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
        // Lấy tham số tên từ yêu cầu get, nếu không có tham số tên được chuyển thì trả về $default_name
        $name = $request->get('name', $default_name);
        // Trả về chuỗi cho trình duyệt
        return response('hello ' . $name);
    }
}
```

Chúng ta có thể lấy bất kỳ dữ liệu liên quan nào từ đối tượng `$request`.

**Đôi khi chúng ta muốn lấy đối tượng `$request` hiện tại trong một lớp khác, lúc đó chúng ta chỉ cần sử dụng hàm trợ giúp `request()`**;

## Lấy tham số yêu cầu từ phương thức get

**Lấy toàn bộ mảng get**
```php
$request->get();
```
Nếu yêu cầu không có tham số get thì nó sẽ trả về một mảng rỗng.

**Lấy một giá trị từ mảng get**
```php
$request->get('name');
```
Nếu mảng get không chứa giá trị này thì nó sẽ trả về null.

Bạn cũng có thể truyền vào tham số thứ hai cho phương thức get, nếu mảng get không chứa giá trị tương ứng thì nó sẽ trả về giá trị mặc định. Ví dụ:
```php
$request->get('name', 'tom');
```

## Lấy tham số yêu cầu từ phương thức post
**Lấy toàn bộ mảng post**
```php
$request->post();
```
Nếu yêu cầu không có tham số post thì nó sẽ trả về một mảng rỗng.

**Lấy một giá trị từ mảng post**
```php
$request->post('name');
```
Nếu mảng post không chứa giá trị này thì nó sẽ trả về null.

Tương tự như phương thức get, bạn cũng có thể truyền vào tham số thứ hai cho phương thức post, nếu mảng post không chứa giá trị tương ứng thì nó sẽ trả về giá trị mặc định. Ví dụ:
```php
$request->post('name', 'tom');
```

## Lấy gói tin post nguyên thủy
```php
$post = $request->rawBody();
```
Chức năng này tương tự như hoạt động `file_get_contents("php://input");` trong `php-fpm`. Sử dụng để lấy gói tin yêu cầu post nguyên thủy. Điều này rất hữu ích khi lấy dữ liệu yêu cầu post không phải là định dạng `application/x-www-form-urlencoded`.

## Lấy header
**Lấy toàn bộ mảng header**
```php
$request->header();
```
Nếu yêu cầu không có tham số header thì nó sẽ trả về một mảng rỗng. Chú ý tất cả các khóa đều viết thường.

**Lấy mảng header một giá trị**
```php
$request->header('host');
```
Nếu mảng header không chứa giá trị này thì nó sẽ trả về null. Chú ý tất cả các khóa đều viết thường.

Tương tự như phương thức get, bạn cũng có thể truyền vào tham số thứ hai cho phương thức header, nếu mảng header không chứa giá trị tương ứng thì nó sẽ trả về giá trị mặc định. Ví dụ:
```php
$request->header('host', 'localhost');
```

## Lấy cookie
**Lấy toàn bộ mảng cookie**
```php
$request->cookie();
```
Nếu yêu cầu không có tham số cookie thì nó sẽ trả về một mảng rỗng.

**Lấy mảng cookie một giá trị**
```php
$request->cookie('name');
```
Nếu mảng cookie không chứa giá trị này thì nó sẽ trả về null.

Tương tự như phương thức get, bạn cũng có thể truyền vào tham số thứ hai cho phương thức cookie, nếu mảng cookie không chứa giá trị tương ứng thì nó sẽ trả về giá trị mặc định. Ví dụ:
```php
$request->cookie('name', 'tom');
```

## Lấy tất cả đầu vào
Bao gồm tập hợp của `post` `get`.
```php
$request->all();
```

## Lấy giá trị đầu vào cụ thể
Lấy giá trị cụ thể từ tập hợp của `post` `get`.
```php
$request->input('name', $default_value);
```

## Lấy một phần dữ liệu đầu vào
Lấy một phần dữ liệu từ tập hợp của `post` `get`.
```php
// Lấy một mảng gồm username và password, nếu key tương ứng không tồn tại thì bỏ qua
$only = $request->only(['username', 'password']);
// Lấy tất cả đầu vào trừ avatar và age
$except = $request->except(['avatar', 'age']);
```

## Lấy file tải lên
**Lấy toàn bộ mảng file tải lên**
```php
$request->file();
```

Đối với biểu mẫu:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()` trả về định dạng tương tự:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Nó là một mảng của các thể hiện `webman\Http\UploadFile`. Lớp `webman\Http\UploadFile` kế thừa từ lớp [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) có sẵn trong PHP và cung cấp một số phương thức hữu ích.

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
            var_export($spl_file->getUploadExtension()); // Phần mở rộng tải lên tệp, ví dụ 'jpg'
            var_export($spl_file->getUploadMimeType()); // Loại mine của tệp tải lên, ví dụ 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Lấy mã lỗi tải lên, ví dụ UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Tên tệp tải lên, ví dụ 'my-test.jpg'
            var_export($spl_file->getSize()); // Lấy kích thước tệp, ví dụ 13364, đơn vị byte
            var_export($spl_file->getPath()); // Lấy định dạng lưu tệp, ví dụ '/tmp'
            var_export($spl_file->getRealPath()); // Lấy đường dẫn tệp tạm thời, ví dụ `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Chú ý:**

- Tệp được tải lên sẽ được đặt tên là một tệp tạm thời, giống như `/tmp/workerman.upload.SRliMu`
- Kích thức tệp tải lên bị hạn chế bởi [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), mặc định là 10M, có thể thay đổi giá trị mặc định bằng cách sửa đổi `max_package_size` trong tệp `config/server.php`.
- Tệp tạm thời sẽ được tự động xóa sau khi yêu cầu kết thúc
- Nếu không có tệp tải lên thì `$request->file()` sẽ trả về một mảng rỗng
- Tệp tải lên không hỗ trợ phương thức `move_uploaded_file()`, hãy sử dụng phương thức `$file->move()` thay thế, xem ví dụ bên dưới.

### Lấy tệp tải lên cụ thể
```php
$request->file('avatar');
```
Nếu tệp tồn tại thì nó sẽ trả về thể hiện `webman\Http\UploadFile` tương ứng, ngược lại sẽ trả về null.

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

## Lấy host
Lấy thông tin host của yêu cầu.
```php
$request->host();
```
Nếu địa chỉ yêu cầu không phải là cổng chuẩn 80 hoặc 443, thông tin host có thể chứa cả cổng, ví dụ `example.com:8080`. Nếu không cần cổng, bạn có thể truyền vào tham số thứ nhất là `true`.

```php
$request->host(true);
```

## Lấy phương thức yêu cầu
```php
 $request->method();
```
Giá trị trả về có thể là `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Lấy uri yêu cầu
```php
$request->uri();
```
Trả về uri của yêu cầu, bao gồm cả phần path và queryString.

## Lấy đường dẫn yêu cầu

```php
$request->path();
```
Trả về phần path của yêu cầu.

## Lấy queryString của yêu cầu

```php
$request->queryString();
```
Trả về phần queryString của yêu cầu.

## Lấy url yêu cầu
Phương thức `url()` trả về URL không có tham số `Query`.
```php
$request->url();
```
Trả về giống như `//www.workerman.net/workerman-chat`

Phương thức `fullUrl()` trả về URL có tham số `Query`.
```php
$request->fullUrl();
```
Trả về giống như `//www.workerman.net/workerman-chat?type=download`

> **Chú ý**
> `url()` và `fullUrl()` không trả về phần giao thức (không trả về http hoặc https).
> Bởi vì trình duyệt sử dụng địa chỉ bắt đầu bằng `//` như `//example.com` sẽ tự động nhận dạng giao thức của trang web hiện tại và tự động gửi yêu cầu bằng http hoặc https.

Nếu bạn sử dụng proxy nginx, hãy thêm `proxy_set_header X-Forwarded-Proto $scheme;` vào cấu hình nginx, [tham khảo proxy nginx](others/nginx-proxy.md),
sau đó bạn có thể sử dụng `$request->header('x-forwarded-proto');` để kiểm tra xem đó là http hay https, ví dụ:
```php
echo $request->header('x-forwarded-proto'); // xuất ra http hoặc https
```

## Lấy phiên yêu cầu

```php
$request->protocolVersion();
```
Trả về chuỗi `1.1` hoặc `1.0`.

## Lấy sessionId của yêu cầu

```php
$request->sessionId();
```
Trả về một chuỗi, bao gồm cả chữ cái và số.


## Lấy địa chỉ IP của máy khách yêu cầu
```php
$request->getRemoteIp();
```

## Lấy cổng của máy khách yêu cầu
```php
$request->getRemotePort();
```
## Lấy địa chỉ IP thực sự của máy khách yêu cầu
```php
$request->getRealIp($safe_mode=true);
```

Khi dự án sử dụng proxy (ví dụ: nginx), việc sử dụng `$request->getRemoteIp()` thường sẽ trả về địa chỉ IP của máy chủ proxy (giống như `127.0.0.1` `192.168.x.x`) thay vì địa chỉ IP thực sự của máy khách. Trong trường hợp này, bạn có thể thử sử dụng `$request->getRealIp()` để lấy địa chỉ IP thực sự của máy khách.

`$request->getRealIp()` sẽ cố gắng lấy địa chỉ IP thực sự từ các trường HTTP header như `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via`.

> Do các tiêu đề HTTP có thể bị làm giả dễ dàng, nên địa chỉ IP của máy khách thu được từ phương thức này không phải lúc nào cũng tin cậy 100%, đặc biệt là khi `$safe_mode` là false. Phương pháp đáng tin cậy hơn để lấy địa chỉ IP thực sự của máy khách thông qua proxy là biết địa chỉ IP của máy chủ proxy an toàn và rõ ràng biết được tiêu đề HTTP nào chứa địa chỉ IP thực sự. Nếu địa chỉ IP trả về từ `$request->getRemoteIp()` xác nhận là của máy chủ proxy an toàn, sau đó bạn có thể sử dụng `$request->header('tiêu đề HTTP chứa địa chỉ IP thực sự')` để lấy địa chỉ IP thực sự.

## Lấy địa chỉ IP của máy chủ
```php
$request->getLocalIp();
```

## Lấy cổng của máy chủ
```php
$request->getLocalPort();
```

## Kiểm tra xem có phải là yêu cầu ajax không
```php
$request->isAjax();
```

## Kiểm tra xem có phải là yêu cầu pjax không
```php
$request->isPjax();
```

## Kiểm tra xem có phải là yêu cầu trả về JSON không
```php
$request->expectsJson();
```

## Kiểm tra xem máy khách có chấp nhận trả về JSON không
```php
$request->acceptJson();
```

## Lấy tên plugin của yêu cầu
Yêu cầu không phải từ plugin sẽ trả về chuỗi rỗng `''`.
```php
$request->plugin;
```
> Tính năng này yêu cầu webman>=1.4.0

## Lấy tên ứng dụng của yêu cầu
Trong trường hợp chỉ có một ứng dụng, sẽ luôn trả về chuỗi rỗng `''`, [Trong trường hợp có nhiều ứng dụng](multiapp.md) sẽ trả về tên ứng dụng
```php
$request->app;
```

> Do hàm đóng không thuộc về bất kỳ ứng dụng nào, vì vậy yêu cầu từ định tuyến đóng luôn trả về chuỗi rỗng `''`
> Xem thêm về định tuyến đóng tại [Định tuyến](route.md)

## Lấy tên lớp điều khiển của yêu cầu
Lấy tên lớp tương ứng với điều khiển
```php
$request->controller;
```
Trả về giống như `app\controller\IndexController`

> Vì hàm đóng không thuộc về bất kỳ điều khiển nào, vì vậy yêu cầu từ định tuyến đóng luôn trả về chuỗi rỗng `''`
> Xem thêm về định tuyến đóng tại [Định tuyến](route.md)

## Lấy tên phương pháp của yêu cầu
Lấy tên phương pháp điều khiển của yêu cầu
```php
$request->action;
```
Trả về giống như `index`

> Vì hàm đóng không thuộc về bất kỳ điều khiển nào, vì vậy yêu cầu từ định tuyến đóng luôn trả về chuỗi rỗng `''`
> Xem thêm về định tuyến đóng tại [Định tuyến](route.md)
