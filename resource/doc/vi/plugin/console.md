# Dòng Lệnh

Thành phần dòng lệnh của Webman

## Cài đặt
```
composer require webman/console
```

## Mục Lục

### Sinh Mã
- [make:controller](#make-controller) - Sinh lớp bộ điều khiển
- [make:model](#make-model) - Sinh lớp mô hình từ bảng cơ sở dữ liệu
- [make:crud](#make-crud) - Sinh CRUD hoàn chỉnh (mô hình + bộ điều khiển + trình xác thực)
- [make:middleware](#make-middleware) - Sinh lớp phần mềm trung gian
- [make:command](#make-command) - Sinh lớp lệnh console
- [make:bootstrap](#make-bootstrap) - Sinh lớp khởi tạo bootstrap
- [make:process](#make-process) - Sinh lớp tiến trình tùy chỉnh

### Build và Triển Khai
- [build:phar](#build-phar) - Đóng gói dự án thành file lưu trữ PHAR
- [build:bin](#build-bin) - Đóng gói dự án thành file nhị phân độc lập
- [install](#install) - Chạy script cài đặt Webman

### Lệnh Tiện Ích
- [version](#version) - Hiển thị phiên bản framework Webman
- [fix-disable-functions](#fix-disable-functions) - Sửa các hàm bị vô hiệu hóa trong php.ini
- [route:list](#route-list) - Hiển thị tất cả các route đã đăng ký

### Quản Lý Tiện Ích Ứng Dụng (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Tạo tiện ích ứng dụng mới
- [app-plugin:install](#app-plugin-install) - Cài đặt tiện ích ứng dụng
- [app-plugin:uninstall](#app-plugin-uninstall) - Gỡ cài đặt tiện ích ứng dụng
- [app-plugin:update](#app-plugin-update) - Cập nhật tiện ích ứng dụng
- [app-plugin:zip](#app-plugin-zip) - Đóng gói tiện ích ứng dụng thành ZIP

### Quản Lý Tiện Ích (plugin:*)
- [plugin:create](#plugin-create) - Tạo tiện ích Webman mới
- [plugin:install](#plugin-install) - Cài đặt tiện ích Webman
- [plugin:uninstall](#plugin-uninstall) - Gỡ cài đặt tiện ích Webman
- [plugin:enable](#plugin-enable) - Bật tiện ích Webman
- [plugin:disable](#plugin-disable) - Tắt tiện ích Webman
- [plugin:export](#plugin-export) - Xuất mã nguồn tiện ích

### Quản Lý Dịch Vụ
- [start](#start) - Khởi động các tiến trình worker Webman
- [stop](#stop) - Dừng các tiến trình worker Webman
- [restart](#restart) - Khởi động lại các tiến trình worker Webman
- [reload](#reload) - Tải lại mã mà không ngừng hoạt động
- [status](#status) - Xem trạng thái tiến trình worker
- [connections](#connections) - Lấy thông tin kết nối tiến trình worker

## Sinh Mã

<a name="make-controller"></a>
### make:controller

Sinh lớp bộ điều khiển.

**Cách dùng:**
```bash
php webman make:controller <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên bộ điều khiển (không có hậu tố) |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--plugin` | `-p` | Sinh bộ điều khiển trong thư mục tiện ích được chỉ định |
| `--path` | `-P` | Đường dẫn bộ điều khiển tùy chỉnh |
| `--force` | `-f` | Ghi đè nếu file đã tồn tại |
| `--no-suffix` | | Không thêm hậu tố "Controller" |

**Ví dụ:**
```bash
# Tạo UserController trong app/controller
php webman make:controller User

# Tạo trong tiện ích
php webman make:controller AdminUser -p admin

# Đường dẫn tùy chỉnh
php webman make:controller User -P app/api/controller

# Ghi đè file đã tồn tại
php webman make:controller User -f

# Tạo không có hậu tố "Controller"
php webman make:controller UserHandler --no-suffix
```

**Cấu trúc file sinh ra:**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**Lưu ý:**
- Bộ điều khiển mặc định đặt trong `app/controller/`
- Hậu tố bộ điều khiển từ config được tự động thêm vào
- Hỏi xác nhận ghi đè nếu file đã tồn tại (tương tự các lệnh khác)

<a name="make-model"></a>
### make:model

Sinh lớp mô hình từ bảng cơ sở dữ liệu. Hỗ trợ Laravel ORM và ThinkORM.

**Cách dùng:**
```bash
php webman make:model [name]
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Không | Tên lớp mô hình, có thể bỏ qua trong chế độ tương tác |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--plugin` | `-p` | Sinh mô hình trong thư mục tiện ích được chỉ định |
| `--path` | `-P` | Thư mục đích (tương đối so với thư mục gốc dự án) |
| `--table` | `-t` | Chỉ định tên bảng; khuyến nghị khi tên bảng không theo quy ước |
| `--orm` | `-o` | Chọn ORM: `laravel` hoặc `thinkorm` |
| `--database` | `-d` | Chỉ định tên kết nối cơ sở dữ liệu |
| `--force` | `-f` | Ghi đè file đã tồn tại |

**Lưu ý đường dẫn:**
- Mặc định: `app/model/` (ứng dụng chính) hoặc `plugin/<plugin>/app/model/` (tiện ích)
- `--path` tương đối so với thư mục gốc dự án, ví dụ `plugin/admin/app/model`
- Khi dùng cả `--plugin` và `--path`, chúng phải trỏ đến cùng một thư mục

**Ví dụ:**
```bash
# Tạo mô hình User trong app/model
php webman make:model User

# Chỉ định tên bảng và ORM
php webman make:model User -t wa_users -o laravel

# Tạo trong tiện ích
php webman make:model AdminUser -p admin

# Đường dẫn tùy chỉnh
php webman make:model User -P plugin/admin/app/model
```

**Chế độ tương tác:** Khi bỏ qua name, vào luồng tương tác: chọn bảng → nhập tên mô hình → nhập đường dẫn. Hỗ trợ: Enter để xem thêm, `0` để tạo mô hình rỗng, `/keyword` để lọc bảng.

**Cấu trúc file sinh ra:**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (primary key)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

Chú thích `@property` được tự động sinh từ cấu trúc bảng. Hỗ trợ MySQL và PostgreSQL.

<a name="make-crud"></a>
### make:crud

Sinh mô hình, bộ điều khiển và trình xác thực từ bảng cơ sở dữ liệu trong một lệnh, tạo khả năng CRUD hoàn chỉnh.

**Cách dùng:**
```bash
php webman make:crud
```

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--table` | `-t` | Chỉ định tên bảng |
| `--model` | `-m` | Tên lớp mô hình |
| `--model-path` | `-M` | Thư mục mô hình (tương đối so với thư mục gốc dự án) |
| `--controller` | `-c` | Tên lớp bộ điều khiển |
| `--controller-path` | `-C` | Thư mục bộ điều khiển |
| `--validator` | | Tên lớp trình xác thực (yêu cầu `webman/validation`) |
| `--validator-path` | | Thư mục trình xác thực (yêu cầu `webman/validation`) |
| `--plugin` | `-p` | Sinh file trong thư mục tiện ích được chỉ định |
| `--orm` | `-o` | ORM: `laravel` hoặc `thinkorm` |
| `--database` | `-d` | Tên kết nối cơ sở dữ liệu |
| `--force` | `-f` | Ghi đè file đã tồn tại |
| `--no-validator` | | Không sinh trình xác thực |
| `--no-interaction` | `-n` | Chế độ không tương tác, dùng giá trị mặc định |

**Luồng thực thi:** Khi không chỉ định `--table`, vào chế độ chọn bảng tương tác; tên mô hình mặc định suy ra từ tên bảng; tên bộ điều khiển mặc định là tên mô hình + hậu tố bộ điều khiển; tên trình xác thực mặc định là tên bộ điều khiển không hậu tố + `Validator`. Đường dẫn mặc định: mô hình `app/model/`, bộ điều khiển `app/controller/`, trình xác thực `app/validation/`; với tiện ích: các thư mục con tương ứng dưới `plugin/<plugin>/app/`.

**Ví dụ:**
```bash
# Sinh tương tác (xác nhận từng bước sau khi chọn bảng)
php webman make:crud

# Chỉ định tên bảng
php webman make:crud --table=users

# Chỉ định tên bảng và tiện ích
php webman make:crud --table=users --plugin=admin

# Chỉ định đường dẫn
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Không sinh trình xác thực
php webman make:crud --table=users --no-validator

# Không tương tác + ghi đè
php webman make:crud --table=users --no-interaction --force
```

**Cấu trúc file sinh ra:**

Mô hình (`app/model/User.php`):
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

Bộ điều khiển (`app/controller/UserController.php`):
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

Trình xác thực (`app/validation/UserValidator.php`):
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => 'Khóa chính',
        'username' => 'Tên người dùng'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Lưu ý:**
- Sinh trình xác thực được bỏ qua nếu `webman/validation` chưa cài đặt hoặc chưa bật (cài với `composer require webman/validation`)
- `attributes` của trình xác thực được tự động sinh từ chú thích trường cơ sở dữ liệu; không có chú thích thì không có `attributes`
- Thông báo lỗi trình xác thực hỗ trợ i18n; ngôn ngữ được chọn từ `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

Sinh lớp phần mềm trung gian và tự động đăng ký vào `config/middleware.php` (hoặc `plugin/<plugin>/config/middleware.php` cho tiện ích).

**Cách dùng:**
```bash
php webman make:middleware <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên phần mềm trung gian |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--plugin` | `-p` | Sinh phần mềm trung gian trong thư mục tiện ích được chỉ định |
| `--path` | `-P` | Thư mục đích (tương đối so với thư mục gốc dự án) |
| `--force` | `-f` | Ghi đè file đã tồn tại |

**Ví dụ:**
```bash
# Tạo phần mềm trung gian Auth trong app/middleware
php webman make:middleware Auth

# Tạo trong tiện ích
php webman make:middleware Auth -p admin

# Đường dẫn tùy chỉnh
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Cấu trúc file sinh ra:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**Lưu ý:**
- Mặc định đặt trong `app/middleware/`
- Tên lớp được tự động thêm vào file config phần mềm trung gian để kích hoạt

<a name="make-command"></a>
### make:command

Sinh lớp lệnh console.

**Cách dùng:**
```bash
php webman make:command <command-name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `command-name` | Có | Tên lệnh theo định dạng `group:action` (ví dụ `user:list`) |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--plugin` | `-p` | Sinh lệnh trong thư mục tiện ích được chỉ định |
| `--path` | `-P` | Thư mục đích (tương đối so với thư mục gốc dự án) |
| `--force` | `-f` | Ghi đè file đã tồn tại |

**Ví dụ:**
```bash
# Tạo lệnh user:list trong app/command
php webman make:command user:list

# Tạo trong tiện ích
php webman make:command user:list -p admin

# Đường dẫn tùy chỉnh
php webman make:command user:list -P plugin/admin/app/command

# Ghi đè file đã tồn tại
php webman make:command user:list -f
```

**Cấu trúc file sinh ra:**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**Lưu ý:**
- Mặc định đặt trong `app/command/`

<a name="make-bootstrap"></a>
### make:bootstrap

Sinh lớp khởi tạo bootstrap. Phương thức `start` được gọi tự động khi tiến trình khởi động, thường dùng cho khởi tạo toàn cục.

**Cách dùng:**
```bash
php webman make:bootstrap <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên lớp Bootstrap |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--plugin` | `-p` | Sinh trong thư mục tiện ích được chỉ định |
| `--path` | `-P` | Thư mục đích (tương đối so với thư mục gốc dự án) |
| `--force` | `-f` | Ghi đè file đã tồn tại |

**Ví dụ:**
```bash
# Tạo MyBootstrap trong app/bootstrap
php webman make:bootstrap MyBootstrap

# Tạo không tự động bật
php webman make:bootstrap MyBootstrap no

# Tạo trong tiện ích
php webman make:bootstrap MyBootstrap -p admin

# Đường dẫn tùy chỉnh
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Ghi đè file đã tồn tại
php webman make:bootstrap MyBootstrap -f
```

**Cấu trúc file sinh ra:**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**Lưu ý:**
- Mặc định đặt trong `app/bootstrap/`
- Khi bật, lớp được thêm vào `config/bootstrap.php` (hoặc `plugin/<plugin>/config/bootstrap.php` cho tiện ích)

<a name="make-process"></a>
### make:process

Sinh lớp tiến trình tùy chỉnh và ghi vào `config/process.php` để tự động khởi động.

**Cách dùng:**
```bash
php webman make:process <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên lớp tiến trình (ví dụ MyTcp, MyWebsocket) |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--plugin` | `-p` | Sinh trong thư mục tiện ích được chỉ định |
| `--path` | `-P` | Thư mục đích (tương đối so với thư mục gốc dự án) |
| `--force` | `-f` | Ghi đè file đã tồn tại |

**Ví dụ:**
```bash
# Tạo trong app/process
php webman make:process MyTcp

# Tạo trong tiện ích
php webman make:process MyProcess -p admin

# Đường dẫn tùy chỉnh
php webman make:process MyProcess -P plugin/admin/app/process

# Ghi đè file đã tồn tại
php webman make:process MyProcess -f
```

**Luồng tương tác:** Hỏi theo thứ tự: lắng nghe cổng? → loại giao thức (websocket/http/tcp/udp/unixsocket) → địa chỉ lắng nghe (IP:port hoặc đường dẫn unix socket) → số lượng tiến trình. Giao thức HTTP còn hỏi chế độ tích hợp sẵn hay tùy chỉnh.

**Cấu trúc file sinh ra:**

Tiến trình không lắng nghe (chỉ có `onWorkerStart`):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Viết logic nghiệp vụ của bạn ở đây.
    }
}
```

Tiến trình lắng nghe TCP/WebSocket sinh mẫu callback `onConnect`, `onMessage`, `onClose` tương ứng.

**Lưu ý:**
- Mặc định đặt trong `app/process/`; config tiến trình ghi vào `config/process.php`
- Khóa config là snake_case của tên lớp; lỗi nếu đã tồn tại
- Chế độ HTTP tích hợp sẵn dùng lại file tiến trình `app\process\Http`, không sinh file mới
- Giao thức hỗ trợ: websocket, http, tcp, udp, unixsocket

## Build và Triển Khai

<a name="build-phar"></a>
### build:phar

Đóng gói dự án thành file lưu trữ PHAR để phân phối và triển khai.

**Cách dùng:**
```bash
php webman build:phar
```

**Khởi động:**

Điều hướng đến thư mục build và chạy

```bash
php webman.phar start
```

**Lưu ý:**
* Dự án đóng gói không hỗ trợ reload; dùng restart để cập nhật mã

* Để tránh kích thước file lớn và sử dụng bộ nhớ cao, cấu hình exclude_pattern và exclude_files trong config/plugin/webman/console/app.php để loại trừ các file không cần thiết.

* Chạy webman.phar tạo thư mục runtime tại cùng vị trí cho log và file tạm.

* Nếu dự án dùng file .env, đặt .env cùng thư mục với webman.phar.

* webman.phar không hỗ trợ tiến trình tùy chỉnh trên Windows

* Không bao giờ lưu file người dùng tải lên bên trong gói phar; thao tác với tải lên người dùng qua phar:// là nguy hiểm (lỗ hổng deserialization phar). File tải lên người dùng phải lưu riêng trên đĩa bên ngoài phar. Xem bên dưới.

* Nếu nghiệp vụ cần tải file lên thư mục public, giải nén thư mục public ra cùng vị trí với webman.phar và cấu hình config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Dùng hàm trợ giúp public_path($relative_path) để lấy đường dẫn thư mục public thực tế.


<a name="build-bin"></a>
### build:bin

Đóng gói dự án thành file nhị phân độc lập với PHP runtime nhúng. Không cần cài đặt PHP trên môi trường đích.

**Cách dùng:**
```bash
php webman build:bin [version]
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `version` | Không | Phiên bản PHP (ví dụ 8.1, 8.2), mặc định phiên bản PHP hiện tại, tối thiểu 8.1 |

**Ví dụ:**
```bash
# Dùng phiên bản PHP hiện tại
php webman build:bin

# Chỉ định PHP 8.2
php webman build:bin 8.2
```

**Khởi động:**

Điều hướng đến thư mục build và chạy

```bash
./webman.bin start
```

**Lưu ý:**
* Khuyến nghị mạnh: phiên bản PHP local nên khớp phiên bản build (ví dụ PHP 8.1 local → build với 8.1) để tránh vấn đề tương thích
* Build tải mã nguồn PHP 8 nhưng không cài đặt local; không ảnh hưởng môi trường PHP local
* webman.bin hiện chỉ chạy trên x86_64 Linux; không hỗ trợ trên macOS
* Dự án đóng gói không hỗ trợ reload; dùng restart để cập nhật mã
* .env không được đóng gói mặc định (điều khiển bởi exclude_files trong config/plugin/webman/console/app.php); đặt .env cùng thư mục với webman.bin khi khởi động
* Thư mục runtime được tạo trong thư mục webman.bin cho file log
* webman.bin không đọc php.ini bên ngoài; để cài đặt php.ini tùy chỉnh, dùng custom_ini trong config/plugin/webman/console/app.php
* Loại trừ file không cần thiết qua config/plugin/webman/console/app.php để tránh kích thước gói lớn
* Build nhị phân không hỗ trợ coroutine Swoole
* Không bao giờ lưu file người dùng tải lên bên trong gói nhị phân; thao tác qua phar:// là nguy hiểm (lỗ hổng deserialization phar). File tải lên người dùng phải lưu riêng trên đĩa bên ngoài gói.
* Nếu nghiệp vụ cần tải file lên thư mục public, giải nén thư mục public ra cùng vị trí với webman.bin và cấu hình config/app.php như bên dưới, sau đó build lại:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Thực thi script cài đặt framework Webman (gọi `\Webman\Install::install()`), để khởi tạo dự án.

**Cách dùng:**
```bash
php webman install
```

## Lệnh Tiện Ích

<a name="version"></a>
### version

Hiển thị phiên bản workerman/webman-framework.

**Cách dùng:**
```bash
php webman version
```

**Lưu ý:** Đọc phiên bản từ `vendor/composer/installed.php`; trả về lỗi nếu không đọc được.

<a name="fix-disable-functions"></a>
### fix-disable-functions

Sửa `disable_functions` trong php.ini, loại bỏ các hàm Webman cần.

**Cách dùng:**
```bash
php webman fix-disable-functions
```

**Lưu ý:** Loại bỏ các hàm sau (và khớp tiền tố) khỏi `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Bỏ qua nếu không tìm thấy php.ini hoặc `disable_functions` rỗng. **Sửa trực tiếp file php.ini**; khuyến nghị sao lưu.

<a name="route-list"></a>
### route:list

Liệt kê tất cả các route đã đăng ký dạng bảng.

**Cách dùng:**
```bash
php webman route:list
```

**Ví dụ đầu ra:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Cột đầu ra:** URI, Method, Callback, Middleware, Name. Callback Closure hiển thị là "Closure".

## Quản Lý Tiện Ích Ứng Dụng (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Tạo tiện ích ứng dụng mới, sinh cấu trúc thư mục hoàn chỉnh và file cơ sở dưới `plugin/<name>`.

**Cách dùng:**
```bash
php webman app-plugin:create <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên tiện ích; phải khớp `[a-zA-Z0-9][a-zA-Z0-9_-]*`, không được chứa `/` hoặc `\` |

**Ví dụ:**
```bash
# Tạo tiện ích ứng dụng tên foo
php webman app-plugin:create foo

# Tạo tiện ích có dấu gạch ngang
php webman app-plugin:create my-app
```

**Cấu trúc thư mục sinh ra:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, v.v.
├── api/Install.php  # Hook cài đặt/gỡ cài đặt/cập nhật
├── public/
└── install.sql
```

**Lưu ý:**
- Tiện ích tạo dưới `plugin/<name>/`; lỗi nếu thư mục đã tồn tại

<a name="app-plugin-install"></a>
### app-plugin:install

Cài đặt tiện ích ứng dụng, thực thi `plugin/<name>/api/Install::install($version)`.

**Cách dùng:**
```bash
php webman app-plugin:install <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên tiện ích; phải khớp `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Ví dụ:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Gỡ cài đặt tiện ích ứng dụng, thực thi `plugin/<name>/api/Install::uninstall($version)`.

**Cách dùng:**
```bash
php webman app-plugin:uninstall <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên tiện ích |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--yes` | `-y` | Bỏ qua xác nhận, thực thi trực tiếp |

**Ví dụ:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Cập nhật tiện ích ứng dụng, thực thi `Install::beforeUpdate($from, $to)` và `Install::update($from, $to, $context)` theo thứ tự.

**Cách dùng:**
```bash
php webman app-plugin:update <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên tiện ích |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--from` | `-f` | Phiên bản từ, mặc định phiên bản hiện tại |
| `--to` | `-t` | Phiên bản đến, mặc định phiên bản hiện tại |

**Ví dụ:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Đóng gói tiện ích ứng dụng thành file ZIP, xuất ra `plugin/<name>.zip`.

**Cách dùng:**
```bash
php webman app-plugin:zip <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên tiện ích |

**Ví dụ:**
```bash
php webman app-plugin:zip foo
```

**Lưu ý:**
- Tự động loại trừ `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__`, v.v.

## Quản Lý Tiện Ích (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Tạo tiện ích Webman mới (dạng gói Composer), sinh thư mục config `config/plugin/<name>` và thư mục mã nguồn tiện ích `vendor/<name>`.

**Cách dùng:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên gói tiện ích theo định dạng `vendor/package` (ví dụ `foo/my-admin`); phải theo quy ước đặt tên gói Composer |

**Ví dụ:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Cấu trúc sinh ra:**
- `config/plugin/<name>/app.php`: Config tiện ích (gồm công tắc `enable`)
- `vendor/<name>/composer.json`: Định nghĩa gói tiện ích
- `vendor/<name>/src/`: Thư mục mã nguồn tiện ích
- Tự động thêm ánh xạ PSR-4 vào `composer.json` gốc dự án
- Chạy `composer dumpautoload` để làm mới autoloading

**Lưu ý:**
- Tên phải theo định dạng `vendor/package`: chữ thường, số, `-`, `_`, `.`, và phải chứa một `/`
- Lỗi nếu `config/plugin/<name>` hoặc `vendor/<name>` đã tồn tại
- Lỗi nếu cung cấp cả tham số và `--name` với giá trị khác nhau

<a name="plugin-install"></a>
### plugin:install

Thực thi script cài đặt tiện ích (`Install::install()`), sao chép tài nguyên tiện ích vào thư mục dự án.

**Cách dùng:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên gói tiện ích theo định dạng `vendor/package` (ví dụ `foo/my-admin`) |

**Tùy chọn:**

| Tùy chọn | Mô tả |
|--------|-------------|
| `--name` | Chỉ định tên tiện ích; dùng tùy chọn này hoặc tham số |

**Ví dụ:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Thực thi script gỡ cài đặt tiện ích (`Install::uninstall()`), xóa tài nguyên tiện ích khỏi dự án.

**Cách dùng:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên gói tiện ích theo định dạng `vendor/package` |

**Tùy chọn:**

| Tùy chọn | Mô tả |
|--------|-------------|
| `--name` | Chỉ định tên tiện ích; dùng tùy chọn này hoặc tham số |

**Ví dụ:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Bật tiện ích, đặt `enable` thành `true` trong `config/plugin/<name>/app.php`.

**Cách dùng:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên gói tiện ích theo định dạng `vendor/package` |

**Tùy chọn:**

| Tùy chọn | Mô tả |
|--------|-------------|
| `--name` | Chỉ định tên tiện ích; dùng tùy chọn này hoặc tham số |

**Ví dụ:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Tắt tiện ích, đặt `enable` thành `false` trong `config/plugin/<name>/app.php`.

**Cách dùng:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên gói tiện ích theo định dạng `vendor/package` |

**Tùy chọn:**

| Tùy chọn | Mô tả |
|--------|-------------|
| `--name` | Chỉ định tên tiện ích; dùng tùy chọn này hoặc tham số |

**Ví dụ:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Xuất config tiện ích và các thư mục được chỉ định từ dự án sang `vendor/<name>/src/`, và sinh `Install.php` để đóng gói và phát hành.

**Cách dùng:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Tham số:**

| Tham số | Bắt buộc | Mô tả |
|----------|----------|-------------|
| `name` | Có | Tên gói tiện ích theo định dạng `vendor/package` |

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--name` | | Chỉ định tên tiện ích; dùng tùy chọn này hoặc tham số |
| `--source` | `-s` | Đường dẫn xuất (tương đối so với thư mục gốc dự án); có thể chỉ định nhiều lần |

**Ví dụ:**
```bash
# Xuất tiện ích, mặc định gồm config/plugin/<name>
php webman plugin:export foo/my-admin

# Xuất thêm app, config, v.v.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Lưu ý:**
- Tên tiện ích phải theo quy ước đặt tên gói Composer (`vendor/package`)
- Nếu `config/plugin/<name>` tồn tại và không có trong `--source`, tự động thêm vào danh sách xuất
- `Install.php` xuất ra gồm `pathRelation` để dùng bởi `plugin:install` / `plugin:uninstall`
- `plugin:install` và `plugin:uninstall` yêu cầu tiện ích tồn tại trong `vendor/<name>`, có lớp `Install` và hằng số `WEBMAN_PLUGIN`

## Quản Lý Dịch Vụ

<a name="start"></a>
### start

Khởi động các tiến trình worker Webman. Mặc định chế độ DEBUG (tiền cảnh).

**Cách dùng:**
```bash
php webman start
```

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--daemon` | `-d` | Khởi động ở chế độ DAEMON (nền) |

<a name="stop"></a>
### stop

Dừng các tiến trình worker Webman.

**Cách dùng:**
```bash
php webman stop
```

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--graceful` | `-g` | Dừng nhẹ nhàng; chờ request hiện tại hoàn thành trước khi thoát |

<a name="restart"></a>
### restart

Khởi động lại các tiến trình worker Webman.

**Cách dùng:**
```bash
php webman restart
```

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--daemon` | `-d` | Chạy ở chế độ DAEMON sau khi khởi động lại |
| `--graceful` | `-g` | Dừng nhẹ nhàng trước khi khởi động lại |

<a name="reload"></a>
### reload

Tải lại mã mà không ngừng hoạt động. Dùng để hot-reload sau khi cập nhật mã.

**Cách dùng:**
```bash
php webman reload
```

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--graceful` | `-g` | Reload nhẹ nhàng; chờ request hiện tại hoàn thành trước khi reload |

<a name="status"></a>
### status

Xem trạng thái chạy tiến trình worker.

**Cách dùng:**
```bash
php webman status
```

**Tùy chọn:**

| Tùy chọn | Viết tắt | Mô tả |
|--------|----------|-------------|
| `--live` | `-d` | Hiển thị chi tiết (trạng thái trực tiếp) |

<a name="connections"></a>
### connections

Lấy thông tin kết nối tiến trình worker.

**Cách dùng:**
```bash
php webman connections
```
