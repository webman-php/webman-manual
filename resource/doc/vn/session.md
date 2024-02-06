# Quản lý session

## Ví dụ
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

Sử dụng `$request->session(); ` để lấy ra một thể hiện của `Workerman\Protocols\Http\Session`, từ đó có thể thêm, sửa đổi hoặc xóa dữ liệu session sử dụng các phương thức của thể hiện này.

> Lưu ý: Khi thể hiện session bị hủy, dữ liệu session sẽ được tự động lưu, vì vậy không nên lưu thể hiện trả về từ `$request->session()` trong mảng toàn cục hoặc thành viên của lớp để tránh việc không lưu được session.

## Lấy tất cả dữ liệu session
```php
$session = $request->session();
$all = $session->all();
```
Trả về một mảng. Nếu không có dữ liệu session nào, thì trả về một mảng rỗng.

## Lấy giá trị của session
```php
$session = $request->session();
$name = $session->get('name');
```
Nếu dữ liệu không tồn tại, thì trả về null.

Bạn cũng có thể truyền một giá trị mặc định vào tham số thứ hai của phương thức get, nếu không tìm thấy giá trị session tương ứng thì sẽ trả về giá trị mặc định. Ví dụ:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Lưu trữ session
Khi lưu trữ một dữ liệu sử dụng phương thức set.
```php
$session = $request->session();
$session->set('name', 'tom');
```
Phương thức set không trả về giá trị, khi thể hiện session bị hủy, session sẽ tự động lưu.

Khi lưu trữ nhiều giá trị sử dụng phương thức put.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Tương tự, phương thức put cũng không trả về giá trị.

## Xóa dữ liệu session
Khi muốn xóa một hoặc nhiều dữ liệu session, bạn sử dụng phương thức `forget`.
```php
$session = $request->session();
// Xóa một mục
$session->forget('name');
// Xóa nhiều mục
$session->forget(['name', 'age']);
```

Ngoài ra, hệ thống cũng cung cấp phương thức `delete`, khác với phương thức `forget`, phương thức `delete` chỉ có thể xóa một mục.
```php
$session = $request->session();
// Tương đương với $session->forget('name');
$session->delete('name');
```

## Lấy và xóa giá trị session
```php
$session = $request->session();
$name = $session->pull('name');
```
Hiệu quả tương đương với đoạn mã sau
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Nếu session tương ứng không tồn tại, thì trả về null.

## Xóa tất cả dữ liệu session
```php
$request->session()->flush();
```
Không trả về giá trị, khi thể hiện session bị hủy, toàn bộ dữ liệu session sẽ tự động bị xóa khỏi bộ nhớ.

## Kiểm tra xem dữ liệu session tương ứng có tồn tại hay không
```php
$session = $request->session();
$has = $session->has('name');
```
Khi dữ liệu session tương ứng không tồn tại hoặc giá trị của session tương ứng là null, sẽ trả về false, ngược lại trả về true.

```
$session = $request->session();
$has = $session->exists('name');
```
Đoạn mã trên cũng được sử dụng để kiểm tra xem dữ liệu session có tồn tại hay không, khác biệt là khi giá trị session tương ứng là null, nó cũng sẽ trả về true.

## Hàm trợ giúp session()
> 2020-12-09 Thêm mới

webman cung cấp hàm trợ giúp `session()` để thực hiện các chức năng tương tự.
```php
// Lấy thể hiện session
$session = session();
// Tương đương với
$session = $request->session();

// Lấy giá trị cụ thể
$value = session('key', 'default');
// Tương đương với
$value = session()->get('key', 'default');
// Tương đương với
$value = $request->session()->get('key', 'default');

// Gán giá trị cho session
session(['key1'=>'value1', 'key2' => 'value2']);
// Tương đương với
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Tương đương với
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## Tệp cấu hình
Tệp cấu hình session nằm trong `config/session.php`, nội dung giống như sau:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class hoặc RedisSessionHandler::class hoặc RedisClusterSessionHandler::class
    'handler' => FileSessionHandler::class,
    
    // Khi handler là FileSessionHandler::class thì giá trị là file,
    // Khi handler là RedisSessionHandler::class thì giá trị là redis
    // Khi handler là RedisClusterSessionHandler::class thì giá trị là redis_cluster, tức là cụm redis
    'type'    => 'file',

    // Cấu hình khác nhau cho mỗi handler
    'config' => [
        // Cấu hình khi type là file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Cấu hình khi type là redis
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // Tên của cookie lưu trữ session_id
    
    // === Cấu hình dưới đây yêu cầu webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Có tự động làm mới session không, mặc định không bật
    'lifetime' => 7*24*60*60,          // Thời gian hết hạn của session
    'cookie_lifetime' => 365*24*60*60, // Thời gian hết hạn của cookie lưu trữ session_id
    'cookie_path' => '/',              // Đường dẫn của cookie lưu trữ session_id
    'domain' => '',                    // Tên miền của cookie lưu trữ session_id
    'http_only' => true,               // Có bật httpOnly hay không, mặc định là bật
    'secure' => false,                 // Chỉ bật session trên https, mặt định không bật
    'same_site' => '',                 // Sử dụng để ngăn chặn tấn công CSRF và theo dõi người dùng, giá trị có thể chọn từ "strict/lax/none"
    'gc_probability' => [1, 1000],     // Xác suất thu hồi session
];
```

> **Lưu ý** 
>
> Từ webman 1.4.0 trở đi, không gian tên của SessionHandler được thay đổi từ 
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> thành  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## Cấu hình thời gian hết hạn
Khi webman-framework < 1.3.14, thời gian hết hạn session trong webman cần phải được cấu hình trong `php.ini`.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Giả sử thiết lập thời gian hết hạn là 1440 giây, thì cấu hình như sau
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Lưu ý**
> Có thể sử dụng lệnh `php --ini` để tìm vị trí của `php.ini`.
