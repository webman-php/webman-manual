# Tiện ích ứng dụng
Mỗi tiện ích ứng dụng là một ứng dụng đầy đủ, mã nguồn được đặt trong thư mục `{thư mục chính}/plugin`

> **Gợi ý**
> Sử dụng lệnh `php webman app-plugin:create {tên tiện ích}` (cần webman/console>=1.2.16) để tạo một tiện ích ứng dụng cục bộ,
> Ví dụ `php webman app-plugin:create cms` sẽ tạo cấu trúc thư mục như sau

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Chúng ta thấy một tiện ích ứng dụng có cấu trúc thư mục và tệp cấu hình giống như webman. Thực tế, việc phát triển một tiện ích ứng dụng giống như việc phát triển một dự án webman cơ bản, chỉ cần chú ý một số điểm sau.

## Không gian tên
Tên thư mục và tên không gian tuân theo chuẩn PSR4, vì tất cả các tiện ích đều nằm trong thư mục plugin, vì thế tên không gian bắt đầu bằng plugin, ví dụ `plugin\cms\app\controller\UserController`, ở đây cms là thư mục mã nguồn của tiện ích.

## Truy cập url
Địa chỉ URL của tiện ích ứng dụng bắt đầu bằng `/app`, ví dụ `plugin\cms\app\controller\UserController` có địa chỉ URL là `http://127.0.0.1:8787/app/cms/user`.

## Tệp tĩnh
Tệp tĩnh được đặt trong `plugin/{tiện ích}/public`, ví dụ truy cập `http://127.0.0.1:8787/app/cms/avatar.png` là việc lấy tệp `plugin/cms/public/avatar.png`.

## Tệp cấu hình
Tệp cấu hình của tiện ích tương tự như dự án webman thông thường, nhưng tệp cấu hình của tiện ích thường chỉ ảnh hưởng đến tiện ích hiện tại, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.app.controller_suffix` chỉ ảnh hưởng đến hậu tố của bộ điều khiển của tiện ích, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.app.controller_reuse` chỉ ảnh hưởng đến việc tái sử dụng bộ điều khiển của tiện ích, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.middleware` chỉ ảnh hưởng đến middleware của tiện ích, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.view` chỉ ảnh hưởng đến giao diện mà tiện ích sử dụng, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.container` chỉ ảnh hưởng đến bộ chứa mà tiện ích sử dụng, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.exception` chỉ ảnh hưởng đến lớp xử lý ngoại lệ của tiện ích, không ảnh hưởng đến dự án chính.

Tuy nhiên, do cấu hình này toàn cục, cấu hình địa chỉ của tiện ích cũng ảnh hưởng đến toàn cục.

## Lấy cấu hình
Cách lấy cấu hình của một tiện ích nào đó là `config('plugin.{tiện ích}.{cấu hình cụ thể}');`, ví dụ lấy tất cả cấu hình của `plugin/cms/config/app.php` là `config('plugin.cms.app')`.
Tương tự, dự án chính hoặc các tiện ích khác cũng có thể sử dụng `config('plugin.cms.xxx')` để lấy cấu hình của cms.

## Cấu hình không hỗ trợ
Tiện ích ứng dụng không hỗ trợ cấu hình server.php, session.php, không hỗ trợ cấu hình `app.request_class`, `app.public_path`, `app.runtime_path`.

## Cơ sở dữ liệu
Tiện ích có thể cấu hình cơ sở dữ liệu của chính mình, ví dụ `plugin/cms/config/database.php` có nội dung như sau
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql là tên kết nối
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'tên cơ sở dữ liệu',
            'username'    => 'tên người dùng',
            'password'    => 'mật khẩu',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin là tên kết nối
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'tên cơ sở dữ liệu',
            'username'    => 'tên người dùng',
            'password'    => 'mật khẩu',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Cách sử dụng là `Db::connection('plugin.{tiện ích}.{tên kết nối}');`, ví dụ
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Nếu muốn sử dụng cơ sở dữ liệu của dự án chính, chỉ cần sử dụng trực tiếp, ví dụ
```php
use support\Db;
Db::table('user')->first();
// Giả sử dự án chính cũng cấu hình một kết nối admin
Db::connection('admin')->table('admin')->first();
```

> **Gợi ý**
> Cách sử dụng thinkorm cũng tương tự

## Redis
Cách sử dụng Redis tương tự như cơ sở dữ liệu, ví dụ `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
Khi sử dụng
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Tương tự, nếu muốn tái sử dụng cấu hình Redis của dự án chính
```php
use support\Redis;
Redis::get('key');
// Giả sử dự án chính cũng cấu hình một kết nối cache
Redis::connection('cache')->get('key');
```

## Nhật ký
Cách sử dụng nhật ký cũng tương tự như cách sử dụng cơ sở dữ liệu
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Nếu muốn tái sử dụng cấu hình nhật ký của dự án chính, chỉ cần sử dụng
```php
use support\Log;
Log::info('nội dung nhật ký');
// Giả sử dự án chính có cấu hình nhật ký test
Log::channel('test')->info('nội dung nhật ký');
```

# Cài đặt và gỡ bỏ tiện ích ứng dụng
Khi cài đặt tiện ích ứng dụng, chỉ cần sao chép thư mục tiện ích vào thư mục `{thư mục chính}/plugin` là có thể sử dụng, cần reload hoặc restart để có hiệu lực.
Để gỡ bỏ, chỉ cần xóa thư mục tiện ích tương ứng trong `{thư mục chính}/plugin`.
