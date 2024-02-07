# Cơ sở dữ liệu
Các plugin có thể cấu hình cơ sở dữ liệu của chính mình, ví dụ như nội dung của `plugin/foo/config/database.php` như sau
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql là tên kết nối
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'tên_cơ_sở_dữ_liệu',
            'username'    => 'tên_người_dùng',
            'password'    => 'mật_khẩu',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin là tên kết nối
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'tên_cơ_sở_dữ_liệu',
            'username'    => 'tên_người_dùng',
            'password'    => 'mật_khẩu',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Cách tham chiếu là `Db::connection('plugin.{tên_plugin}.{tên_kết_nối}');`, ví dụ
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Nếu muốn sử dụng cơ sở dữ liệu của dự án chính, chỉ cần sử dụng như sau, ví dụ
```php
use support\Db;
Db::table('user')->first();
// Giả sử dự án chính còn cấu hình một kết nối admin
Db::connection('admin')->table('admin')->first();
```

## Cấu hình cơ sở dữ liệu cho Model
Chúng ta có thể tạo một lớp Base cho Model, lớp Base sử dụng `$connection` để chỉ định kết nối cơ sở dữ liệu riêng của plugin, ví dụ
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

Như vậy, tất cả Model trong plugin khi kế thừa từ Base sẽ tự động sử dụng cơ sở dữ liệu riêng của plugin.

## Tái sử dụng cấu hình cơ sở dữ liệu
Tất nhiên, chúng ta có thể tái sử dụng cấu hình cơ sở dữ liệu của dự án chính, nếu đã tích hợp [webman-admin](https://www.workerman.net/plugin/82), cũng có thể tái sử dụng cấu hình cơ sở dữ liệu của [webman-admin](https://www.workerman.net/plugin/82), ví dụ
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
