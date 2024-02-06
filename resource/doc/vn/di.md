# Tự động chèn phụ thuộc

Trong webman, chèn phụ thuộc tự động là một tính năng tùy chọn và mặc định là tắt. Nếu bạn cần chèn phụ thuộc tự động, chúng tôi khuyến khích sử dụng [php-di](https://php-di.org/doc/getting-started.html). Dưới đây là cách sử dụng webman kết hợp với `php-di`.

## Cài đặt
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Chỉnh sửa cấu hình `config/container.php`, nội dung sau cùng là như sau:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` trả về một trường hợp của container phù hợp với chuẩn `PSR-11`. Nếu bạn không muốn sử dụng `php-di`, bạn có thể tạo và trả về một trường hợp của container phù hợp với chuẩn `PSR-11` ở đây.

## Chèn phụ thuộc bằng hàm tạo
Tạo mới `app/service/Mailer.php`(nếu không có thư mục, bạn có thể tự tạo) với nội dung như sau:
```php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Mã gửi thư đi được rút gọn
    }
}
```

Nội dung của `app/controller/UserController.php` như sau:

```php
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
Trong tình huống thông thường, mã dưới đây sẽ được sử dụng để khởi tạo thực thể của `app\controller\UserController`:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Khi sử dụng `php-di`, nhà phát triển không cần khởi tạo `Mailer` trong controller và webman sẽ tự động thực hiện việc này. Nếu có phụ thuộc từ các lớp khác trong quá trình khởi tạo `Mailer`, webman cũng sẽ tự động khởi tạo và chèn phụ thuộc. Người phát triển không cần làm bất kỳ công việc khởi tạo nào.

> **Lưu ý**: Chỉ có các thực thể được tạo bởi framework hoặc `php-di` mới có thể hoàn tất chèn phụ thuộc tự động. Thực thể được tạo bằng từ khóa `new` thông thường không thể hoàn tất chèn phụ thuộc tự động. Nếu muốn chèn, bạn cần sử dụng giao diện `support\Container` thay thế cho câu lệnh `new`, ví dụ:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Thực thể được tạo bằng từ khóa "new" không thể chèn phụ thuộc tự động
$user_service = new UserService;
// Thực thể được tạo bằng từ khóa "new" không thể chèn phụ thuộc tự động
$log_service = new LogService($path, $name);

// Thực thể được tạo bằng Container có thể chèn phụ thuộc tự động
$user_service = Container::get(UserService::class);
// Thực thể được tạo bằng Container có thể chèn phụ thuộc tự động
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Chèn phụ thuộc thông qua chú thích
Ngoài chèn phụ thuộc thông qua hàm tạo, chúng ta cũng có thể sử dụng chú thích để chèn phụ thuộc. Tiếp tục với ví dụ trước, `app\controller\UserController` sẽ được sửa như sau:

```php
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
Ví dụ này sử dụng chú thích `@Inject` để chèn phụ thuộc và dùng `@var` để khai báo kiểu đối tượng. Hiệu quả của ví dụ này giống hệt việc chèn phụ thuộc thông qua hàm tạo, nhưng mã nguồn được viết gọn hơn.

> **Lưu ý**: Trước phiên bản 1.4.6, webman không hỗ trợ chèn tham số controller, ví dụ như mã dưới đây sẽ không được hỗ trợ khi webman <= 1.4.6:

```php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Phiên bản 1.4.6 trở về trước không hỗ trợ chèn tham số controller
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Xin chào và chào mừng!');
        return response('ok');
    }
}
```

## Tùy chỉnh chèn phụ thuộc thông qua hàm tạo

Đôi khi tham số được truyền vào hàm tạo có thể không phải là một thực thể của lớp mà có thể là chuỗi, số, mảng và dữ liệu khác. Ví dụ, hàm tạo của Mailer cần truyền vào địa chỉ IP và cổng smtp ví dụ như sau:

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
        // Mã gửi thư đi được rút gọn
    }
}
```

Trường hợp này không thể sử dụng chèn phụ thuộc tự động thông qua hàm tạo như đã giới thiệu ở trên, vì `php-di` không thể xác định giá trị của `$smtp_host` và `$smtp_port`. Trong trường hợp này, bạn có thể thử nghiệm với chèn phụ thuộc tùy chỉnh.

Thêm mã sau vào `config/dependence.php` (nếu file không tồn tại, bạn có thể tự tạo):
```php
return [
    // ... Bỏ qua cấu hình khác

    app\service\Mailer::class => new app\service\Mailer('192.168.1.11', 25);
];
```
Khi cần chèn phụ thuộc để lấy thực thể của `app\service\Mailer`, nó sẽ tự động sử dụng thực thể `app\service\Mailer` được tạo trong cấu hình.

Chúng ta chú ý rằng, trong `config/dependence.php` sử dụng `new` để khởi tạo thực thể của lớp Mailer. Điều này không có vấn đề gì trong ví dụ này, nhưng nếu lớp Mailer phụ thuộc vào các lớp khác hoặc sử dụng chú thích chèn phụ thuộc bên trong, việc khởi tạo sẽ không có chèn phụ thuộc tự động. Cách giải quyết là sử dụng chèn phụ thuộc thông qua giao diện tùy chỉnh, thông qua phương thức `Container::get(Ten_Lop)` hoặc `Container::make(Ten_Lop, [Tham_so_ham_tao])` để khởi tạo lớp.

## Chèn phụ thuộc thông qua giao diện tùy chỉnh

Trong dự án thực tế, chúng ta muốn lập trình dựa trên giao diện, chứ không phải lớp cụ thể. Ví dụ, trong `app\controller\UserController`, chúng ta nên sử dụng `app\service\MailerInterface` thay vì `app\service\Mailer`.

Định nghĩa giao diện `MailerInterface`.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Định nghĩa cách thực hiện giao diện `MailerInterface`.
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
        // Mã gửi thư đi được rút gọn
    }
}
```

Thay vì sử dụng thực thể cụ thể, chúng ta sẽ sử dụng giao diện `MailerInterface`.
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

Trong `config/dependence.php`, giao diện `MailerInterface` được định nghĩa một cách sử lần triển và chung.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Khi cần sử dụng giao diện `MailerInterface`, sẽ tự động sử dụng cách thực hiện của `Mailer`.

> Ưu điểm của lập trình dựa trên giao diện là khi cần thay đổi một thành phần nào đó, chúng ta không cần phải thay đổi mã nguồn của dự án mà chỉ cần thay đổi cách thực hiện cụ thể trong `config/dependence.php`. Điều này rất hữu ích khi thực hiện kiểm thử đơn vị.

## Chèn phụ thuộc tùy chỉnh khác

`config/dependence.php` ngoài việc định nghĩa các phụ thuộc của lớp, còn có thể định nghĩa các giá trị, chẳng hạn như chuỗi, số, mảng và dữ liệu khác.

Ví dụ, `config/dependence.php` được định nghĩa như sau:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Khi đó, `@Inject` có thể chèn giá trị của `smtp_host` và `smtp_port` vào thuộc tính của lớp.
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
        // Mã gửi thư đi được rút gọn
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Kết quả sẽ là 192.168.1.11:25
    }
}
```

> Lưu ý: Trong `@Inject("key")`, bạn cần sử dụng dấu nháy kép.

## Thêm nội dung khác

Vui lòng tham khảo [tài liệu php-di](https://php-di.org/doc/getting-started.html) để biết thêm thông tin.
