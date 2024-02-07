# Plugin console của webman

`Plugin console của webman` được xây dựng trên nền `symfony/console`

> plugin yêu cầu webman>=1.2.2 webman-framework>=1.2.1

## Cài đặt
```sh
composer require webman/console
```

## Các lệnh hỗ trợ
**Cách sử dụng**  
`php webman tên_lệnh` hoặc `php webman tên_lệnh`.
Ví dụ `php webman version` hoặc `php webman version`

## Các lệnh hỗ trợ
### version
**Hiển thị số phiên bản của webman**

### route:list
**Hiển thị cấu hình định tuyến hiện tại**

### make:controller
**Tạo một tập tin điều khiển** 
Ví dụ: `php webman make:controller admin` sẽ tạo một `app/controller/AdminController.php`
Ví dụ: `php webman make:controller api/user` sẽ tạo một `app/api/controller/UserController.php`

### make:model
**Tạo tập tin model**
Ví dụ: `php webman make:model admin` sẽ tạo một `app/model/Admin.php`
Ví dụ: `php webman make:model api/user` sẽ tạo một `app/api/model/User.php`

### make:middleware
**Tạo tập tin middleware**
Ví dụ: `php webman make:middleware Auth` sẽ tạo một `app/middleware/Auth.php`

### make:command
**Tạo tập tin lệnh tùy chỉnh**
Ví dụ: `php webman make:command db:config` sẽ tạo một `app\command\DbConfigCommand.php`

### plugin:create
**Tạo một plugin cơ bản**
Ví dụ: `php webman plugin:create --name=foo/admin` sẽ tạo hai thư mục là `config/plugin/foo/admin` và `vendor/foo/admin`
Xem chi tiết tại [Tạo plugin cơ bản](/doc/webman/plugin/create.html)

### plugin:export
**Xuất plugin cơ bản**
Ví dụ: `php webman plugin:export --name=foo/admin` 
Xem chi tiết tại [Tạo plugin cơ bản](/doc/webman/plugin/create.html)

### plugin:export
**Xuất plugin ứng dụng**
Ví dụ: `php webman plugin:export shop`
Xem chi tiết tại [Plugin ứng dụng](/doc/webman/plugin/app.html)

### phar:pack
**Đóng gói dự án webman thành tập tin phar**
Xem chi tiết tại [Đóng gói phar](/doc/webman/others/phar.html)
> Tính năng này yêu cầu webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Lệnh tùy chỉnh
Người dùng có thể định nghĩa các lệnh tùy chỉnh của riêng mình, ví dụ dưới đây là một lệnh để hiển thị cấu hình cơ sở dữ liệu

* Chạy `php webman make:command config:mysql`
* Mở `app/command/ConfigMySQLCommand.php` và sửa nội dung như sau

```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigMySQLCommand extends Command
{
    protected static $defaultName = 'config:mysql';
    protected static $defaultDescription = 'Hiển thị cấu hình máy chủ MySQL hiện tại';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Thông tin cấu hình MySQL như sau:');
        $config = config('database');
        $headers = ['tên', 'mặc định', 'driver', 'máy chủ', 'port', 'cơ sở dữ liệu', 'tên người dùng', 'mật khẩu', 'unix_socket', 'bảng mã', 'đối chiếu', 'tiền tố', 'nghiêm ngặt', 'engine', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'đúng' : 'sai';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['default'] == $name) {
                array_unshift($rows, $row);
            } else {
                $rows[] = $row;
            }
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}
```
  
## Kiểm tra

Chạy lệnh dòng lệnh `php webman config:mysql`

Kết quả sẽ tương tự như sau:
```plaintext
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| tên   | mặc định | driver | máy chủ    | cổng | cơ sở dữ liệu | tên người dùng | mật khẩu | unix_socket | bảng mã | đối chiếu       | tiền tố | nghiêm ngặt | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Tham khảo thêm
http://www.symfonychina.com/doc/current/components/console.html
