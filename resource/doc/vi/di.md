# Tự động chèn phụ thuộc

Trong webman, việc chèn phụ thuộc tự động là một tính năng tùy chọn và mặc định là đóng. Nếu bạn cần chèn phụ thuộc tự động, chúng tôi khuyên bạn nên sử dụng [php-di](https://php-di.org/doc/getting-started.html). Dưới đây là cách sử dụng `php-di` kết hợp với webman.

## Cài đặt
```composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14```

Sửa đổi cấu hình trong `config/container.php`, nội dung cuối cùng sẽ như sau:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> Trong`config/container.php`, bạn cần trả về một thực thể của container phù hợp với chuẩn `PSR-11`. Nếu bạn không muốn sử dụng `php-di`, bạn có thể tạo và trả về một thực thể của container khác phù hợp với chuẩn `PSR-11` tại đây.

## Chèn theo constructor
Tạo mới tệp `app/service/Mailer.php` (nếu thư mục không tồn tại, vui lòng tạo mới) với nội dung như sau:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Mã gửi email được bay qua
    }
}
```

Nội dung của `app/controller/UserController.php` như sau:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Xin chào và chào mừng!');
        return response('ok');
    }
}
```
Thông thường, mã sau đây cần được sử dụng để khởi tạo thể hiện của `app\controller\UserController`:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Khi sử dụng `php-di`, phương pháp này sẽ không cần thiết nữa vì webman sẽ tự động hoàn thành việc này cho bạn. Nếu trong quá trình khởi tạo `Mailer` có phụ thuộc vào các lớp khác, webman cũng sẽ tự động khởi tạo và chèn vào. Bạn không cần phải làm bất kỳ công việc khởi tạo nào.

> **Lưu ý**
> Chỉ từ các thể hiện được tạo ra bởi framework hoặc `php-di` mới có thể sử dụng chèn phụ thuộc tự động. Các thể hiện được tạo bằng tay bằng từ khóa `new` sẽ không có chèn phụ thuộc tự động. Để chèn phụ thuộc, bạn cần sử dụng giao diện `support\Container` thay vì câu lệnh `new`, ví dụ:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Thể hiện được tạo ra bằng từ khóa `new` không thể chèn phụ thuộc tự động
$user_service = new UserService;
// Thể hiện được tạo ra bằng từ khóa `new` không thể chèn phụ thuộc tự động
$log_service = new LogService($path, $name);

// Thể hiện được tạo ra bằng Container có thể chèn phụ thuộc tự động
$user_service = Container::get(UserService::class);
// Thể hiện được tạo ra bằng Container có thể chèn phụ thuộc tự động
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Chèn bằng chú thích
Ngoài chứa chèn qua constructor, chúng ta cũng có thể sử dụng chú thích để chèn phụ thuộc. Tiếp tục từ ví dụ trước, `app\controller\UserController` sẽ được thay đổi như sau:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Xin chào và chào mừng!');
        return response('ok');
    }
}
```
Trong ví dụ này, chúng ta sử dụng chú thích `@Inject` để chèn và sử dụng chú thích `@var` để khai báo kiểu đối tượng. Hiệu quả của ví dụ này tương tự như chèn qua constructor nhưng mã nguồn ngắn gọn hơn.

> **Lưu ý**
> Trước phiên bản 1.4.6, webman không hỗ trợ chèn tham số điều khiển, ví dụ như trong đoạn mã sau đây sẽ không được hỗ trợ nếu phiên bản của webman <= 1.4.6

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Trước phiên bản 1.4.6, không hỗ trợ chèn tham số điều khiển
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Xin chào và chào mừng!');
        return response('ok');
    }
}
```

## Tự động chèn theo constructor tùy chỉnh

Đôi khi, tham số được truyền vào constructor có thể không phải là một thể hiện của lớp mà có thể là một chuỗi, số, mảng, hoặc dữ liệu khác. Ví dụ, constructor của Mailer cần phải truyền vào địa chỉ IP và cổng của máy chủ SMTP:

```php
<?php
namespace app\service;

class Mailer
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // Mã gửi email được bay qua
    }
}
```

Trường hợp này không thể sử dụng chèn tự động qua constructor như đã giới thiệu trước vì `php-di` không thể xác định giá trị của `$smtp_host` và `$smtp_port`. Trong trường hợp này, bạn có thể thử nghiệm với chèn tùy chỉnh.

Trong `config/dependence.php` (nếu tệp không tồn tại, vui lòng tạo mới), thêm mã sau đây:
```php
return [
    // ... Bỏ qua các cấu hình khác

    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Như vậy, khi cần thể hiện của `app\service\Mailer`, webman sẽ tự động sử dụng thể hiện của `app\service\Mailer` được tạo ra trong cấu hình này. 

Chúng ta nhìn thấy rằng, trong `config/dependence.php`, chúng ta sử dụng từ khóa `new` để khởi tạo thể hiện của lớp `Mailer`. Trong ví dụ này, điều này không gây vấn đề gì, nhưng nếu lớp `Mailer` phụ thuộc vào các lớp khác hoặc sử dụng chèn qua chú thích, việc khởi tạo bằng từ khóa `new` sẽ không thực hiện chèn phụ thuộc tự động. Cách giải quyết là sử dụng chèn tùy chỉnh thông qua phương thức `Container::get(class_name)` hoặc `Container::make(class_name, [constructor_parameters])` để khởi tạo lớp.
## Tiêm cấp giao diện tùy chỉnh

Trong dự án thực tế, chúng ta mong muốn lập trình theo giao diện, chứ không phải là các lớp cụ thể. Ví dụ:`app\controller\UserController` nên nhúng `app\service\MailerInterface` thay vì `app\service\Mailer`.
Định nghĩa giao diện `MailerInterface`:
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Triển khai giao diện `MailerInterface`:
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    private $smtpHost;
    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // Mã gửi email được bỏ qua
    }
}
```

Nhúng giao diện `MailerInterface` thay vì triển khai cụ thể:
```php
<?php
namespace app\controller;

use support\Request;
use app\service\MailerInterface;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var MailerInterface
     */
    private $mailer;
    
    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Xin chào và chào mừng!');
        return response('ok');
    }
}
```


`config/dependence.php` định nghĩa triển khai `MailerInterface` như sau:
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```
Như vậy, khi doanh nghiệp cần sử dụng giao diện `MailerInterface`, nó sẽ tự động sử dụng triển khai `Mailer`.
> Sự lợi ích của lập trình theo giao diện là khi chúng ta cần thay đổi một thành phần, chúng ta không cần thay đổi mã doanh nghiệp, chỉ cần thay đổi triển khai cụ thể trong `config/dependence.php`. Điều này cũng rất hữu ích khi thực hiện kiểm thử đơn vị.

## Tiêm cấp tùy chỉnh khác

`config/dependence.php` không chỉ định nghĩa các phụ thuộc của lớp, mà còn có thể định nghĩa các giá trị khác như chuỗi, số, mảng, v.v.

Ví dụ `config/dependence.php` định nghĩa như sau:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Lúc này, chúng ta có thể nhúng `smtp_host` `smtp_port` vào thuộc tính của lớp thông qua `@Inject`:
```php
<?php
namespace app\service;

use DI\Annotation\Inject;

class Mailer
{
    /**
     * @Inject("smtp_host")
     */
    private $smtpHost;

    /**
     * @Inject("smtp_port")
     */
    private $smtpPort;

    public function mail($email, $content)
    {
        // Mã gửi email được bỏ qua
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Sẽ xuất hiện 192.168.1.11:25
    }
}
```

> Lưu ý: `"@Inject("key")"` nằm trong cặp dấu kép.

## Nội dung khác
Vui lòng tham khảo [hướng dẫn sử dụng php-di](https://php-di.org/doc/getting-started.html)
