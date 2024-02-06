Xin chào, dưới đây là hướng dẫn về plugin Medoo cho webman, một framework PHP hiệu suất cao dựa trên Workerman.

### Bước 1: Cài đặt Medoo

Đầu tiên, bạn cần cài đặt Medoo thông qua Composer bằng cách chạy lệnh sau trong thư mục dự án của bạn:

```bash
composer require catfan/medoo
```

### Bước 2: Cài đặt Plugin Medoo cho webman

Bạn có thể cài đặt plugin Medoo bằng cách thêm dòng sau vào tệp cấu hình của webman (`config/medoo.php`):

```php
<?php

return [
    'default' => [ // Kết nối mặc định
        'database_type' => 'mysql', 
        'database_name' => 'your_database_name',
        'server' => 'localhost',
        'username' => 'your_username',
        'password' => 'your_password',
        'charset' => 'utf8',
    ],
];
```

Sau đó, bạn cần đăng ký plugin Medoo trong tệp cấu hình chính của webman (`config/main.php`):

```php
<?php

return [
    'debug' => true,
    'name' => 'YourWebmanApp',
    'host' => '0.0.0.0',
    'port' => 2345,
    'processes' => 4,
    'daemonize' => false,
    'plugins' => [
        \Webman\Medoo\MedooProvider::class,
    ],
];
```

### Bước 3: Sử dụng Medoo trong ứng dụng của bạn

Bây giờ bạn có thể sử dụng đối tượng Medoo để thao tác với cơ sở dữ liệu của bạn. Ví dụ:

```php
<?php

use Medoo\Medoo;

class YourModel
{
    protected $database;

    public function __construct(Medoo $database)
    {
        $this->database = $database;
    }

    public function getAllUsers()
    {
        return $this->database->select('users', '*');
    }
}
```

Đây là hướng dẫn cài đặt và sử dụng plugin Medoo cho webman. Hy vọng bạn có thể áp dụng thành công trong dự án của mình. Nếu cần hỗ trợ thêm, vui lòng liên hệ.
