# Các plugin ứng dụng
Mỗi plugin ứng dụng là một ứng dụng hoàn chỉnh, mã nguồn được đặt trong thư mục `{thư mục chính}/plugin`

> **Gợi ý**
> Sử dụng lệnh `php webman app-plugin:create {tên_plugin}` (yêu cầu webman/console>=1.2.16) để tạo một plugin ứng dụng cục bộ, 
> Ví dụ `php webman app-plugin:create cms` tạo cấu trúc thư mục như sau

```plaintext
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Chúng ta thấy rằng một plugin ứng dụng có cấu trúc thư mục và tệp cấu hình giống như của webman. Trên thực tế, trải nghiệm phát triển một plugin ứng dụng tương tự như phát triển một dự án webman, chỉ cần chú ý các điểm sau.

## Không gian tên
Thư mục và tên plugin tuân theo quy ước PSR4, vì tất cả plugin đều đặt trong thư mục plugin, nên không gian tên bắt đầu bằng plugin, ví dụ `plugin\cms\app\controller\UserController`, ở đây cms là thư mục gốc của mã nguồn plugin.

## URL truy cập
Địa chỉ URL của plugin ứng dụng bắt đầu bằng `/app`, ví dụ `plugin\cms\app\controller\UserController` có địa chỉ URL là `http://127.0.0.1:8787/app/cms/user`.

## Tập tin tĩnh
Các tệp tĩnh được đặt trong `plugin/{plugin}/public`, ví dụ, truy cập `http://127.0.0.1:8787/app/cms/avatar.png` thực ra là lấy tệp `plugin/cms/public/avatar.png`.

## Tập tin cấu hình
Cấu hình của plugin tương tự như dự án webman thông thường, tuy nhiên cấu hình của plugin thường chỉ áp dụng cho plugin hiện tại, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.app.controller_suffix` chỉ ảnh hưởng đến hậu tố của các điều khiển của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.app.controller_reuse` chỉ ảnh hưởng đến việc tái sử dụng điều khiển của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.middleware` chỉ ảnh hưởng đến middleware của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.view` chỉ ảnh hưởng đến cách sử dụng view của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.container` chỉ ảnh hưởng đến container mà plugin sử dụng, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.cms.exception` chỉ ảnh hưởng đến lớp xử lý ngoại lệ của plugin, không ảnh hưởng đến dự án chính.
Tuy nhiên vì route là toàn cục, nên cấu hình route của plugin cũng ảnh hưởng toàn cục.

## Lấy cấu hình
Để lấy cấu hình của một plugin, phương pháp là `config('plugin.{plugin}.{cấu hình cụ thể}');`, ví dụ lấy tất cả cấu hình của `plugin/cms/config/app.php` là `config('plugin.cms.app')`
Tương tự, dự án chính hoặc các plugin khác có thể sử dụng `config('plugin.cms.xxx')` để lấy cấu hình của plugin cms.

## Cấu hình không được hỗ trợ
Plugin ứng dụng không hỗ trợ cấu hình server.php, session.php, không hỗ trợ `app.request_class`, `app.public_path`, `app.runtime_path`.

## Cơ sở dữ liệu
Plugin có thể cấu hình cơ sở dữ liệu riêng của mình, ví dụ, nội dung của `plugin/cms/config/database.php` như sau
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
Cách sử dụng là `Db::connection('plugin.{plugin}.{tên_kết_nối}');`, ví dụ
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```
Nếu muốn sử dụng cơ sở dữ liệu của dự án chính, hãy sử dụng trực tiếp, ví dụ
```php
use support\Db;
Db::table('user')->first();
// Giả sử dự án chính còn cấu hình kết nối admin
Db::connection('admin')->table('admin')->first();
```

> **Gợi ý**
> Cách sử dụng cho thinkorm cũng tương tự.

## Redis
Cách sử dụng Redis tương tự cơ sở dữ liệu, ví dụ `plugin/cms/config/redis.php`
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
Sử dụng như sau
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
Tương tự, nếu muốn tái sử dụng cấu hình Redis của dự án chính
```php
use support\Redis;
Redis::get('key');
// Giả sử dự án chính còn cấu hình kết nối cache
Redis::connection('cache')->get('key');
```
## Nhật ký
Cách sử dụng lớp nhật ký cũng tương tự như cách sử dụng cơ sở dữ liệu
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Nếu muốn tái sử dụng cấu hình nhật ký của dự án chính, chỉ cần sử dụng
```php
use support\Log;
Log::info('Nội dung nhật ký');
// Giả sử dự án chính có cấu hình nhật ký test
Log::channel('test')->info('Nội dung nhật ký');
```

# Cài đặt và Gỡ bỏ Plugin ứng dụng
Khi cài đặt Plugin ứng dụng, bạn chỉ cần sao chép thư mục plugin của Plugin vào thư mục `{dự án chính}/plugin`, sau đó cần reload hoặc restart để có hiệu lực. 
Để gỡ bỏ, chỉ cần xóa thư mục plugin tương ứng trong `{dự án chính}/plugin`.
