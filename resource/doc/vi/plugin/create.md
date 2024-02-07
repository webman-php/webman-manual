# Quy trình tạo và phát hành plugin cơ bản

## Nguyên lý
1. Ví dụ về plugin chuyển tiếp yêu cầu trỏ đến ba phần: một là tập tin chương trình trung gian chuyển tiếp, hai là tập tin cấu hình middleware.php và ba là Install.php được tạo tự động thông qua lệnh.
2. Chúng ta sử dụng lệnh để đóng gói ba tập tin và phát hành chúng lên composer.
3. Khi người dùng cài đặt plugin chuyển tiếp sử dụng composer, Install.php trong plugin sẽ sao chép tập tin chương trình trung gian chuyển tiếp và tập tin cấu hình vào đường dẫn `{project chính}/config/plugin` để webman tải và thực hiện cấu hình tự động.
4. Khi người dùng gỡ bỏ plugin, Install.php sẽ xóa tập tin chương trình trung gian chuyển tiếp và tập tin cấu hình tương ứng, thực hiện chức năng tự động gỡ bỏ plugin.

## Tiêu chuẩn
1. Tên plugin bao gồm hai phần: `nhà sản xuất` và `tên plugin`, ví dụ `webman/push`, tương ứng với tên gói composer.
2. Tập tin cấu hình plugin được đặt trong thư mục `config/plugin/ nhà sản xuất/tên plugin/` (lệnh console sẽ tự động tạo thư mục cấu hình). Nếu plugin không cần cấu hình, thư mục cấu hình tự động tạo sẽ phải bị xóa.
3. Thư mục cấu hình plugin chỉ hỗ trợ tập tin app.php cấu hình chính của plugin, bootstrap.php cấu hình khởi động tiến trình, route.php cấu hình đường dẫn, middleware.php cấu hình trung gian, process.php cấu hình tiến trình tùy chỉnh, database.php cấu hình cơ sở dữ liệu, redis.php cấu hình redis và thinkorm.php cấu hình thinkorm. Những cấu hình này sẽ được webman tự động nhận diện.
4. Plugin sử dụng phương thức sau để lấy cấu hình: `config('plugin. nhà sản xuất.tên plugin.tập tin cấu hình.mục cấu hình cụ thể');` ví dụ `config('plugin.webman.push.app.app_key')`
5. Nếu plugin có cấu hình cơ sở dữ liệu riêng, truy cập như sau: `illuminate/database` sử dụng `Db::connection('plugin. nhà sản xuất.tên plugin.kết nối cụ thể')`, `thinkrom` sử dụng `Db::connct('plugin. nhà sản xuất.tên plugin.kết nối cụ thể')`
6. Nếu plugin cần đặt tập tin kinh doanh trong thư mục `app/`, hãy đảm bảo không xung đột với dự án chính và các plugin khác.
7. Plugin nên tránh sao chép tập tin hoặc thư mục sang dự án chính càng nhiều càng tốt, ví dụ plugin chuyển tiếp ngoại trừ tập tin cấu hình, tập tin chương trình trung gian nên được đặt trong `vendor/webman/cros/src` và không cần sao chép sang dự án chính.
8. Đề xuất sử dụng không gian tên plugin có chữ cái in hoa, ví dụ Webman/Console.

## Ví dụ

**Cài đặt `webman/console` command line**

`composer require webman/console`

#### Tạo plugin

Giả sử tên plugin bạn tạo là `foo/admin` (tên cũng là tên dự án bạn cần phát hành trên composer, tên cần viết thường)
Chạy lệnh
`php webman plugin:create --name=foo/admin`

Sau khi tạo plugin, thư mục `vendor/foo/admin` sẽ được tạo để lưu trữ tập tin liên quan đến plugin và `config/plugin/foo/admin` sẽ được tạo để lưu trữ cấu hình liên quan đến plugin.

> Lưu ý
> `config/plugin/foo/admin` hỗ trợ cấu hình như app.php cấu hình chính của plugin, bootstrap.php cấu hình khởi động tiến trình, route.php cấu hình đường dẫn, middleware.php cấu hình trung gian, process.php cấu hình tiến trình tùy chỉnh, database.php cấu hình cơ sở dữ liệu, redis.php cấu hình redis và thinkorm.php cấu hình thinkorm. Định dạng cấu hình tương tự như của webman, tất cả cấu hình này sẽ được webman nhận diện tự động và hợp nhất vào cấu hình.
                                                           
Hãy truy cập bằng cách thêm tiền tố `plugin`, ví dụ config('plugin.foo.admin.app');

#### Xuất plugin

Khi plugin đã được phát triển, thực hiện lệnh xuất plugin sau
`php webman plugin:export --name=foo/admin`

> Giải thích
> Sau khi xuất, thư mụcconfig/plugin/foo/admin sẽ được sao chép vào src của vendor/foo/admin, đồng thời sẽ tự động tạo ra một Install.php. Install.php được sử dụng để thực hiện một số thao tác tự động khi cài đặt và gỡ bỏ plugin.
> Thao tác mặc định khi cài đặt là sao chép cấu hình từ src của vendor/foo/admin vào thư mục config/plugin của dự án hiện tại
> Thao tác mặc định khi gỡ bỏ là xóa tập tin cấu hình trong thư mục config/plugin của dự án hiện tại
> Bạn có thể thay đổi Install.php để thực hiện các thao tác tự định nghĩa khi cài đặt hoặc gỡ bỏ plugin.

#### Đăng ký plugin
* Giả sử bạn đã có tài khoản trên [github](https://github.com) và [packagist](https://packagist.org)
* Tạo dự án trên [github](https://github.com) và tải mã nguồn của plugin, giả sử địa chỉ dự án là `https://github.com/yourusername/admin`
* Vào địa chỉ `https://github.com/yourusername/admin/releases/new` để phát hành phiên bản như `v1.0.0`
* Truy cập [packagist](https://packagist.org) và nhấn vào `Submit` để gửi địa chỉ dự án trên github của bạn `https://github.com/yourusername/admin`, từ đó hoàn thành quá trình phát hành plugin

> **Lưu ý**
> Nếu khi đăng ký plugin trên `packagist` mà gặp xung đột tên, bạn có thể đổi tên nhà sản xuất, ví dụ từ `foo/admin` sang `myfoo/admin`

Khi mã nguồn dự án plugin của bạn được cập nhật, bạn cần đồng bộ mã của mình lên github và trở lại địa chỉ `https://github.com/yourusername/admin/releases/new` để phát hành một phiên bản mới, sau đó vào trang `https://packagist.org/packages/foo/admin` và nhấn nút `Update` để cập nhật phiên bản.
## Thêm lệnh cho Plugin
Đôi khi plugin của chúng ta cần một số lệnh tùy chỉnh để cung cấp các chức năng hỗ trợ, ví dụ: sau khi cài đặt plugin `webman/redis-queue`, dự án sẽ tự động thêm lệnh `redis-queue:consumer` để người dùng chỉ cần chạy `php webman redis-queue:consumer send-mail` là sẽ tạo ra một lớp tiêu thụ SendMail.php trong dự án, điều này giúp phát triển nhanh chóng.

Giả sử plugin `foo/admin` cần thêm lệnh `foo-admin:add`, tham khảo các bước sau.

#### Tạo lệnh mới

**Tạo tập tin lệnh mới `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'Đây là mô tả của lệnh';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Thêm tên');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **Lưu ý**
> Để tránh xung đột giữa các lệnh của plugin, định dạng dòng lệnh nên là `nhà sản xuất-tên plugin:tên cụ thể`, ví dụ: Tất cả các lệnh của plugin `foo/admin` nên có tiền tố là `foo-admin:`, ví dụ: `foo-admin:add`.

#### Thêm cấu hình
**Tạo cấu hình mới `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....có thể thêm nhiều cấu hình khác...
];
```

> **Ghi chú**
> `command.php` được sử dụng để cấu hình lệnh tùy chỉnh cho plugin, mỗi phần tử trong mảng tương ứng với một tập tin lớp lệnh dòng lệnh, mỗi tập tin lớp tương ứng với một lệnh. Khi người dùng chạy dòng lệnh, `webman/console` sẽ tự động tải các lệnh tùy chỉnh được cấu hình trong `command.php` của mỗi plugin. Để biết thêm thông tin về dòng lệnh, vui lòng tham khảo [Dòng lệnh](console.md).

#### Xuất plugin
Chạy lệnh `php webman plugin:export --name=foo/admin` để xuất plugin, sau đó đưa lên `packagist`. Khi người dùng cài đặt plugin `foo/admin`, sẽ thêm lệnh `foo-admin:add`. Chạy `php webman foo-admin:add jerry` sẽ in ra `Admin add jerry`.
