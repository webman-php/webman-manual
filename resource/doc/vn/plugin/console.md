# Trình cắm dòng lệnh webman/console

`webman/console` dựa trên `symfony/console`

> Trình cắm yêu cầu webman>=1.2.2 webman-framework>=1.2.1

## Cài đặt

```sh
composer require webman/console
```

## Các lệnh hỗ trợ
**Cách sử dụng**  
`php webman command` hoặc `php webman command`.
Ví dụ `php webman version` hoặc `php webman version`

## Các lệnh hỗ trợ
### version
**In phiên bản webman**

### route:list
**In cấu hình tuyến đường hiện tại**

### make:controller
**Tạo một tệp điều khiển**
Ví dụ `php webman make:controller admin` tạo một `app/controller/AdminController.php`
Ví dụ `php webman make:controller api/user` tạo một `app/api/controller/UserController.php`

### make:model
**Tạo một tệp mô hình**
Ví dụ `php webman make:model admin` tạo một `app/model/Admin.php`
Ví dụ `php webman make:model api/user` tạo một `app/api/model/User.php`

### make:middleware
**Tạo một tệp middleware**
Ví dụ `php webman make:middleware Auth` tạo một `app/middleware/Auth.php`

### make:command
**Tạo một tệp lệnh tùy chỉnh**
Ví dụ `php webman make:command db:config` tạo một `app\command\DbConfigCommand.php` 

### plugin:create
**Tạo một trình cắm cơ bản**
Ví dụ `php webman plugin:create --name=foo/admin` tạo hai thư mục `config/plugin/foo/admin` và `vendor/foo/admin`
Xem thêm[Tạo trình cắm cơ bản](/doc/webman/plugin/create.html)

### plugin:export
**Xuất trình cắm cơ bản**
Ví dụ `php webman plugin:export --name=foo/admin`
Xem thêm [Tạo trình cắm cơ bản](/doc/webman/plugin/create.html)

### plugin:export
**Xuất trình cắm ứng dụng**
Ví dụ `php webman plugin:export shop`
Xem thêm [Trình cắm ứng dụng](/doc/webman/plugin/app.html)

### phar:pack
**Đóng gói dự án webman thành tập tin phar**
Xem thêm [Đóng gói phar](/doc/webman/others/phar.html)
> Tính năng này yêu cầu webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Lệnh tùy chỉnh
Người dùng có thể xác định các lệnh tùy chỉnh của họ, ví dụ dưới đây là lệnh in cấu hình cơ sở dữ liệu

* Thực hiện `php webman make:command config:mysql`
* Mở `app/command/ConfigMySQLCommand.php` và sửa thành sau đây

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
        $output->writeln('Thông tin cấu hình MySQL như sau: ');
        $config = config('database');
        $headers = ['name', 'default', 'driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefix', 'strict', 'engine', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'true' : 'false';
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

Chạy dòng lệnh `php webman config:mysql`

Kết quả sẽ giống như sau:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Tham khảo thêm
http://www.symfonychina.com/doc/current/components/console.html
