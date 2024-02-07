# Đa ngôn ngữ

Đa ngôn ngữ sử dụng thành phần [symfony/translation](https://github.com/symfony/translation).

## Cài đặt
```sh
composer require symfony/translation
```

## Tạo gói ngôn ngữ
Mặc định, webman đặt các gói ngon ngữ trong thư mục `resource/translations` (nếu không có thì hãy tự tạo), nếu cần thay đổi thư mục, vui lòng cấu hình trong `config/translation.php`.
Mỗi ngôn ngữ tương ứng với một thư mục con trong đó, và định nghĩa ngôn ngữ mặc định được đặt trong `messages.php`. Ví dụ:

```plaintext
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Tất cả các tệp ngôn ngữ đều trả về một mảng như sau:

```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Cấu hình

`config/translation.php`

```php
return [
    // Ngôn ngữ mặc định
    'locale' => 'zh_CN',
    // Ngôn ngữ dự phòng, nếu không thấy dịch vụ trong ngôn ngữ hiện tại, thì sẽ thử sử dụng dịch vụ trong ngôn ngữ dự phòng
    'fallback_locale' => ['zh_CN', 'en'],
    // Thư mục chứa tập tin ngôn ngữ
    'path' => base_path() . '/resource/translations',
];
```

## Dich
Dịch bằng cách sử dụng phương thức `trans()`.

Tạo tập tin ngôn ngữ `resource/translations/zh_CN/messages.php` như sau:

```php
return [
    'hello' => 'Xin chào thế giới!',
];
```

Tạo tệp `app/controller/UserController.php` như sau:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // Xin chào thế giới!
        return response($hello);
    }
}
```

Truy cập `http://127.0.0.1:8787/user/get` sẽ trả về "Xin chào thế giới!"

## Thay đổi ngôn ngữ mặc định

Sử dụng phương thức `locale()` để chuyển đổi ngôn ngữ.

Thêm tệp ngôn ngữ `resource/translations/en/messages.php` như sau:

```php
return [
    'hello' => 'Hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Chuyển đổi ngôn ngữ
        locale('en');
        $hello = trans('hello'); // Hello world!
        return response($hello);
    }
}
```
Truy cập `http://127.0.0.1:8787/user/get` sẽ trả về "Hello world!"

Bạn cũng có thể sử dụng tham số thứ tư của hàm `trans()` để chuyển đổi ngôn ngữ tạm thời, ví dụ như ở trên và ví dụ dưới đây tương đương:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Tham số thứ 4 chuyển đổi ngôn ngữ
        $hello = trans('hello', [], null, 'en'); // Hello world!
        return response($hello);
    }
}
```

## Đặt ngôn ngữ cụ thể cho mỗi yêu cầu
translation là một đối tượng đơn lẻ, điều này có nghĩa là tất cả các yêu cầu chia sẻ đối tượng này, nếu một yêu cầu nào đó sử dụng `locale()` để thiết lập ngôn ngữ mặc định, thì nó sẽ ảnh hưởng đến tất cả các yêu cầu tiếp theo của quá trình này. Vì vậy, chúng ta nên đặt ngôn ngữ cụ thể cho mỗi yêu cầu. Ví dụ như sử dụng middleware sau:

Tạo tệp `app/middleware/Lang.php` (nếu thư mục không tồn tại thì hãy tự tạo) như sau:

```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

Thêm middleware toàn cầu vào `config/middleware.php` như sau:

```php
return [
    // Middleware toàn cầu
    '' => [
        // ... Giữa này, được lược bỏ
        app\middleware\Lang::class,
    ]
];
```

## Sử dụng thay thế
Đôi khi, một thông báo chứa biến cần được dịch, ví dụ:

```php
trans('hello ' . $name);
```

Khi gặp trường hợp này, chúng ta sử dụng thay thế để xử lý.

Thay đổi `resource/translations/zh_CN/messages.php` như sau:

```php
return [
    'hello' => 'Xin chào %name%!',
];
```
Khi dịch, dữ liệu được chuyển vào các giá trị tương ứng với thay thế thông qua tham số thứ hai
```php
trans('hello', ['%name%' => 'webman']); // Xin chào webman!
```

## Xử lý số nhiều
Một số ngôn ngữ do sự khác biệt về số lượng có các cấu trúc câu khác nhau, ví dụ: `There is %count% apple`, khi `%count%` bằng 1 cấu trúc câu đúng, khi lớn hơn 1 thì cấu trúc câu sai.

Khi gặp trường hợp này, chúng ta sử dụng **dấu đường ống** (`|`) để liệt kê các hình thức số nhiều.

Tệp ngôn ngữ `resource/translations/en/messages.php` thêm `apple_count` như sau:

```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Chúng ta thậm chí có thể chỉ định phạm vi số, tạo ra các quy tắc số nhiều phức tạp hơn:

```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Chỉ định tệp ngôn ngữ
Tệp ngôn ngữ mặc định có tên là `messages.php`, thực tế bạn có thể tạo tệp ngôn ngữ khác. 

Tạo tệp ngôn ngữ `resource/translations/zh_CN/admin.php` như sau:

```php
return [
    'hello_admin' => 'Xin chào người quản trị!',
];
```

Để chỉ định tệp ngôn ngữ, sử dụng tham số thứ ba của `trans()` (bỏ qua phần mở rộng `.php`).
```php
trans('hello', [], 'admin', 'zh_CN'); // Xin chào người quản trị!
```

## Thêm thông tin
Xin tham khảo [tài liệu symfony/translation](https://symfony.com/doc/current/translation.html) để biết thêm thông tin.
