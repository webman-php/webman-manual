# Quy trình tạo và phát hành plugin cơ bản

## Nguyên lý
1. Lấy ví dụ về plugin chéo, plugin được chia thành ba phần, một là tệp chương trình trung gian plugin chéo, hai là tệp cấu hình trung gian middleware.php, và ba là Install.php được tạo tự động thông qua lệnh.
2. Chúng ta sử dụng lệnh để đóng gói ba tệp này và phát hành chúng vào composer.
3. Khi người dùng cài đặt plugin chéo bằng composer, Install.php trong plugin sẽ sao chép tệp chương trình trung gian chéo cũng như tệp cấu hình vào `{dự án chính}/config/plugin`, để webman tải.
4. Khi người dùng gỡ bỏ plugin, Install.php sẽ xóa tệp chương trình trung gian chéo tương ứng và tệp cấu hình, thực hiện việc gỡ bỏ tự động của plugin.

## Quy chuẩn
1. Tên plugin bao gồm hai phần, `nhà sản xuất` và `tên plugin`, ví dụ `webman/push`, phù hợp với tên gói composer.
2. Tệp cấu hình plugin được chứa tại `config/plugin/manufacturer/plugin-name/` (lệnh console sẽ tự động tạo thư mục cấu hình). Nếu plugin không cần cấu hình, bạn cần xóa thư mục cấu hình được tạo tự động.
3. Thư mục cấu hình plugin chỉ hỗ trợ tệp app.php (cấu hình chính plugin), bootstrap.php (cấu hình khởi động quá trình), route.php (cấu hình định tuyến), middleware.php (cấu hình trung gian), process.php (cấu hình tiến trình tùy chỉnh), database.php (cấu hình cơ sở dữ liệu), redis.php (cấu hình redis), thinkorm.php (cấu hình thinkorm). Các cấu hình này sẽ tự động được webman nhận diện.
4. Plugin sử dụng phương pháp sau để truy cập cấu hình: `config('plugin.manufacturer.plugin-name.config files.specific configuration item')`, ví dụ `config('plugin.webman.push.app.app_key')`.
5. Nếu plugin có cấu hình cơ sở dữ liệu riêng, sẽ truy cập theo cách sau: `illuminate/database` là `Db::connection('plugin.manufacturer.plugin-name.specific connection')`, `thinkrom` là `Db::connct('plugin.manufacturer.plugin-name.specific connection')`.
6. Nếu plugin cần đặt tệp kinh doanh trong thư mục `app/`, cần đảm bảo không xung đột với dự án người dùng và plugin khác.
7. Plugin nên tránh sao chép tệp hoặc thư mục vào dự án chính, ví dụ plugin chéo ngoài tệp cấu hình cần sao chép vào dự án chính, tệp chương trình trung gian cần đặt tại `vendor/webman/cros/src`, không cần sao chép vào dự án chính.
8. Đề xuất sử dụng không gian tên plugin in hoa, ví dụ Webman/Console.

## Ví dụ

**Cài đặt lệnh `webman/console` từ dòng lệnh**

`composer require webman/console`

#### Tạo plugin

Giả sử tạo ra plugin với tên `foo/admin` (tên cũng là tên dự án mà bạn muốn phát hành thông qua composer và cần viết thường)
Chạy lệnh
`php webman plugin:create --name=foo/admin`

Sau khi tạo plugin, sẽ tạo ra thư mục `vendor/foo/admin` để lưu trữ các tệp liên quan đến plugin và `config/plugin/foo/admin` để lưu trữ cấu hình liên quan đến plugin.

> Lưu ý
> `config/plugin/foo/admin` hỗ trợ app.php (cấu hình chính plugin), bootstrap.php (cấu hình khởi động quá trình), route.php (cấu hình định tuyến), middleware.php (cấu hình trung gian), process.php (cấu hình tiến trình tùy chỉnh), database.php (cấu hình cơ sở dữ liệu), redis.php (cấu hình redis), thinkorm.php (cấu hình thinkorm). Định dạng cấu hình giống như của webman và sẽ tự động được webman nhận diện và hợp nhất vào cấu hình.
Khi sử dụng, truy cập đến `plugin` như là tiền tố, ví dụ config('plugin.foo.admin.app');

#### Xuất plugin

Sau khi phát triển xong plugin, thực hiện lệnh sau để xuất plugin
`php webman plugin:export --name=foo/admin`

> Giải thích
> Sau khi xuất, thư mục `config/plugin/foo/admin` sẽ được sao chép vào vendor/foo/admin/src, đồng thời tự động tạo ra một tệp Install.php, Install.php được sử dụng để thực hiện một số hành động khi cài đặt và gỡ bỏ plugin tự động.
Hành động mặc định khi cài đặt là sao chép cấu hình từ trong vendor/foo/admin/src vào config/plugin của dự án hiện tại
Hành động mặc định khi gỡ bỏ là xóa tệp cấu hình trong config/plugin của dự án hiện tại
Bạn có thể chỉnh sửa Install.php để thực hiện một số hành động tùy chỉnh khi cài đặt và gỡ bỏ plugin.

#### Nộp plugin
* Giả sử bạn đã có tài khoản trên [github](https://github.com) và [packagist](https://packagist.org)
* Trên [github](https://github.com) tạo một dự án admin và tải mã lên, địa chỉ dự án giả định là `https://github.com/username/admin`
* Truy cập vào địa chỉ `https://github.com/username/admin/releases/new` để phát hành một phiên bản như là `v1.0.0`
* Truy cập [packagist](https://packagist.org) và nhấp vào `Submit` trong thanh điều hướng, đưa địa chỉ dự án github của bạn `https://github.com/username/admin` lên đó, đến đây là đã hoàn thành việc phát hành một plugin

> **Chú ý**
> Nếu khi nộp plugin trên `packagist` hiển thị xung đột từ khóa, bạn có thể lấy một tên nhà sản xuất khác, ví dụ `foo/admin` thay đổi thành `myfoo/admin`

Sau này, khi mã dự án plugin của bạn có cập nhật, cần đồng bộ mã lên github và trở lại địa chỉ `https://github.com/username/admin/releases/new` để phát hành một phiên bản mới, sau đó truy cập trang `https://packagist.org/packages/foo/admin` nhấp nút `Update` để cập nhật phiên bản

## Thêm lệnh cho plugin
Đôi khi plugin của chúng ta cần một số lệnh tùy chỉnh để cung cấp các chức năng hỗ trợ, ví dụ sau khi cài đặt plugin `webman/redis-queue`, dự án sẽ tự động thêm một lệnh `redis-queue:consumer`, người dùng chỉ cần chạy `php webman redis-queue:consumer send-mail` để tạo ra một lớp người tiêu dùng SendMail.php trong dự án, điều này giúp phát triển nhanh chóng.

Giả sử plugin `foo/admin` cần thêm lệnh `foo-admin:add`, hãy tham khảo các bước sau.

#### Tạo lệnh mới

**Tạo tệp lệnh mới `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Đây là mô tả lệnh';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Add name');
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
> Để tránh xung đột giữa các plugin, đề xuất định dạng lệnh console là `manufacturer-plugin-name:specific command`, ví dụ tất cả các lệnh của plugin `foo/admin` phải bắt đầu bằng tiền tố `foo-admin:`, ví dụ `foo-admin:add`.

#### Thêm cấu hình
**Tạo cấu hình mới `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ...có thể thêm nhiều cấu hình khác...
];
```

> **Gợi ý**
> `command.php` dùng để cấu hình các lệnh tùy chỉnh cho plugin, mỗi phần tử trong mảng tương ứng với một tệp lớp lệnh, mỗi tệp lớp lệnh tương ứng với một lệnh. Khi người dùng chạy lệnh console, `webman/console` sẽ tự động tải các lệnh tùy chỉnh được đặt trong `command.php` của mỗi plugin. Để biết thêm về các lệnh console, vui lòng tham khảo [console.md](console.md)

#### Thực hiện xuất
Thực hiện lệnh `php webman plugin:export --name=foo/admin` để xuất plugin và nộp lên `packagist`. Sau đó, người dùng cài đặt plugin `foo/admin` sẽ có một lệnh mới `foo-admin:add`. Thực hiện `php webman foo-admin:add jerry` sẽ in ra `Admin add jerry`

