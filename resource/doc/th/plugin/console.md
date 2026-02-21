# บรรทัดคำสั่ง

คอมโพเนนต์บรรทัดคำสั่งของ Webman

## การติดตั้ง
```
composer require webman/console
```

## สารบัญ

### การสร้างโค้ด
- [make:controller](#make-controller) - สร้างคลาสตัวควบคุม
- [make:model](#make-model) - สร้างคลาสโมเดลจากตารางฐานข้อมูล
- [make:crud](#make-crud) - สร้าง CRUD แบบสมบูรณ์ (โมเดล + ตัวควบคุม + ตัวตรวจสอบ)
- [make:middleware](#make-middleware) - สร้างคลาสมิดเดิลแวร์
- [make:command](#make-command) - สร้างคลาสคำสั่งคอนโซล
- [make:bootstrap](#make-bootstrap) - สร้างคลาสเริ่มต้นบูตสแตรป
- [make:process](#make-process) - สร้างคลาสโปรเซสกำหนดเอง

### การ build และการ deploy
- [build:phar](#build-phar) - บีบอัดโปรเจกต์เป็นไฟล์เก็บ PHAR
- [build:bin](#build-bin) - บีบอัดโปรเจกต์เป็นไฟล์ไบนารีแบบสแตนด์อโลน
- [install](#install) - รันสคริปต์ติดตั้ง Webman

### คำสั่งยูทิลิตี้
- [version](#version) - แสดงเวอร์ชันเฟรมเวิร์ก Webman
- [fix-disable-functions](#fix-disable-functions) - แก้ไขฟังก์ชันที่ถูกปิดใช้งานใน php.ini
- [route:list](#route-list) - แสดงเส้นทางที่ลงทะเบียนทั้งหมด

### การจัดการปลั๊กอินแอป (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - สร้างปลั๊กอินแอปพลิเคชันใหม่
- [app-plugin:install](#app-plugin-install) - ติดตั้งปลั๊กอินแอปพลิเคชัน
- [app-plugin:uninstall](#app-plugin-uninstall) - ถอนการติดตั้งปลั๊กอินแอปพลิเคชัน
- [app-plugin:update](#app-plugin-update) - อัปเดตปลั๊กอินแอปพลิเคชัน
- [app-plugin:zip](#app-plugin-zip) - บีบอัดปลั๊กอินแอปพลิเคชันเป็น ZIP

### การจัดการปลั๊กอิน (plugin:*)
- [plugin:create](#plugin-create) - สร้างปลั๊กอิน Webman ใหม่
- [plugin:install](#plugin-install) - ติดตั้งปลั๊กอิน Webman
- [plugin:uninstall](#plugin-uninstall) - ถอนการติดตั้งปลั๊กอิน Webman
- [plugin:enable](#plugin-enable) - เปิดใช้งานปลั๊กอิน Webman
- [plugin:disable](#plugin-disable) - ปิดใช้งานปลั๊กอิน Webman
- [plugin:export](#plugin-export) - ส่งออกซอร์สโค้ดปลั๊กอิน

### การจัดการบริการ
- [start](#start) - เริ่มกระบวนการเวิร์กเกอร์ Webman
- [stop](#stop) - หยุดกระบวนการเวิร์กเกอร์ Webman
- [restart](#restart) - รีสตาร์ทกระบวนการเวิร์กเกอร์ Webman
- [reload](#reload) - โหลดโค้ดใหม่โดยไม่หยุดทำงาน
- [status](#status) - ดูสถานะกระบวนการเวิร์กเกอร์
- [connections](#connections) - ดูข้อมูลการเชื่อมต่อกระบวนการเวิร์กเกอร์

## Code Generation

<a name="make-controller"></a>
### make:controller

สร้างคลาสตัวควบคุม

**Usage:**
```bash
php webman make:controller <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อตัวควบคุม (ไม่รวมคำต่อท้าย) |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | สร้างตัวควบคุมในไดเรกทอรีปลั๊กอินที่กำหนด |
| `--path` | `-P` | เส้นทางตัวควบคุมแบบกำหนดเอง |
| `--force` | `-f` | เขียนทับหากไฟล์มีอยู่แล้ว |
| `--no-suffix` | | ไม่เพิ่มคำต่อท้าย "Controller" |

**Examples:**
```bash
# Create UserController in app/controller
php webman make:controller User

# Create in plugin
php webman make:controller AdminUser -p admin

# Custom path
php webman make:controller User -P app/api/controller

# Overwrite existing file
php webman make:controller User -f

# Create without "Controller" suffix
php webman make:controller UserHandler --no-suffix
```

**Generated file structure:**
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

**Notes:**
- ตัวควบคุมจะถูกวางใน `app/controller/` โดยค่าเริ่มต้น
- คำต่อท้ายตัวควบคุมจาก config จะถูกเพิ่มโดยอัตโนมัติ
- จะถามยืนยันการเขียนทับหากไฟล์มีอยู่แล้ว (เช่นเดียวกับคำสั่งอื่น)

<a name="make-model"></a>
### make:model

สร้างคลาสโมเดลจากตารางฐานข้อมูล รองรับ Laravel ORM และ ThinkORM

**Usage:**
```bash
php webman make:model [name]
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | No | ชื่อคลาสโมเดล สามารถละเว้นได้ในโหมดโต้ตอบ |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | สร้างโมเดลในไดเรกทอรีปลั๊กอินที่กำหนด |
| `--path` | `-P` | ไดเรกทอรีเป้าหมาย (สัมพันธ์กับรูทโปรเจกต์) |
| `--table` | `-t` | ระบุชื่อตาราง แนะนำเมื่อชื่อตารางไม่เป็นไปตามข้อตกลง |
| `--orm` | `-o` | เลือก ORM: `laravel` หรือ `thinkorm` |
| `--database` | `-d` | ระบุชื่อการเชื่อมต่อฐานข้อมูล |
| `--force` | `-f` | เขียนทับไฟล์ที่มีอยู่แล้ว |

**Path notes:**
- ค่าเริ่มต้น: `app/model/` (แอปหลัก) หรือ `plugin/<plugin>/app/model/` (ปลั๊กอิน)
- `--path` สัมพันธ์กับรูทโปรเจกต์ เช่น `plugin/admin/app/model`
- เมื่อใช้ทั้ง `--plugin` และ `--path` พวกเขาต้องชี้ไปที่ไดเรกทอรีเดียวกัน

**Examples:**
```bash
# Create User model in app/model
php webman make:model User

# Specify table name and ORM
php webman make:model User -t wa_users -o laravel

# Create in plugin
php webman make:model AdminUser -p admin

# Custom path
php webman make:model User -P plugin/admin/app/model
```

**Interactive mode:** เมื่อไม่ระบุชื่อ จะเข้าสู่โฟลว์โต้ตอบ: เลือกตาราง → ใส่ชื่อโมเดล → ใส่เส้นทาง รองรับ: Enter เพื่อดูเพิ่มเติม, `0` เพื่อสร้างโมเดลว่าง, `/keyword` เพื่อกรองตาราง

**Generated file structure:**
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

`@property` annotations จะถูกสร้างอัตโนมัติจากโครงสร้างตาราง รองรับ MySQL และ PostgreSQL

<a name="make-crud"></a>
### make:crud

สร้างโมเดล ตัวควบคุม และตัวตรวจสอบจากตารางฐานข้อมูลในครั้งเดียว เพื่อความสามารถ CRUD แบบสมบูรณ์

**Usage:**
```bash
php webman make:crud
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--table` | `-t` | ระบุชื่อตาราง |
| `--model` | `-m` | ชื่อคลาสโมเดล |
| `--model-path` | `-M` | ไดเรกทอรีโมเดล (สัมพันธ์กับรูทโปรเจกต์) |
| `--controller` | `-c` | ชื่อคลาสตัวควบคุม |
| `--controller-path` | `-C` | ไดเรกทอรีตัวควบคุม |
| `--validator` | | ชื่อคลาสตัวตรวจสอบ (ต้องมี `webman/validation`) |
| `--validator-path` | | ไดเรกทอรีตัวตรวจสอบ (ต้องมี `webman/validation`) |
| `--plugin` | `-p` | สร้างไฟล์ในไดเรกทอรีปลั๊กอินที่กำหนด |
| `--orm` | `-o` | ORM: `laravel` หรือ `thinkorm` |
| `--database` | `-d` | ชื่อการเชื่อมต่อฐานข้อมูล |
| `--force` | `-f` | เขียนทับไฟล์ที่มีอยู่แล้ว |
| `--no-validator` | | ไม่สร้างตัวตรวจสอบ |
| `--no-interaction` | `-n` | โหมดไม่โต้ตอบ ใช้ค่าเริ่มต้น |

**Execution flow:** เมื่อไม่ระบุ `--table` จะเข้าสู่การเลือกตารางแบบโต้ตอบ ชื่อโมเดลเริ่มต้นจะอนุมานจากชื่อตาราง ชื่อตัวควบคุมเริ่มต้นคือชื่อโมเดล + คำต่อท้ายตัวควบคุม ชื่อตัวตรวจสอบเริ่มต้นคือชื่อตัวควบคุมโดยไม่มีคำต่อท้าย + `Validator` เส้นทางเริ่มต้น: โมเดล `app/model/`, ตัวควบคุม `app/controller/`, ตัวตรวจสอบ `app/validation/` สำหรับปลั๊กอิน: ไดเรกทอรีย่อยที่สอดคล้องกันภายใต้ `plugin/<plugin>/app/`

**Examples:**
```bash
# Interactive generation (step-by-step confirmation after table selection)
php webman make:crud

# Specify table name
php webman make:crud --table=users

# Specify table name and plugin
php webman make:crud --table=users --plugin=admin

# Specify paths
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Do not generate validator
php webman make:crud --table=users --no-validator

# Non-interactive + overwrite
php webman make:crud --table=users --no-interaction --force
```

**Generated file structure:**

Model (`app/model/User.php`):
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

Controller (`app/controller/UserController.php`):
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

Validator (`app/validation/UserValidator.php`):
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
        'id' => 'คีย์หลัก',
        'username' => 'ชื่อผู้ใช้'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Notes:**
- การสร้างตัวตรวจสอบจะถูกข้ามหากไม่ได้ติดตั้งหรือเปิดใช้งาน `webman/validation` (ติดตั้งด้วย `composer require webman/validation`)
- `attributes` ของตัวตรวจสอบจะถูกสร้างอัตโนมัติจากคอมเมนต์ฟิลด์ฐานข้อมูล หากไม่มีคอมเมนต์จะไม่มี `attributes`
- ข้อความข้อผิดพลาดของตัวตรวจสอบรองรับ i18n ภาษาจะถูกเลือกจาก `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

สร้างคลาสมิดเดิลแวร์และลงทะเบียนอัตโนมัติไปยัง `config/middleware.php` (หรือ `plugin/<plugin>/config/middleware.php` สำหรับปลั๊กอิน)

**Usage:**
```bash
php webman make:middleware <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อมิดเดิลแวร์ |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | สร้างมิดเดิลแวร์ในไดเรกทอรีปลั๊กอินที่กำหนด |
| `--path` | `-P` | ไดเรกทอรีเป้าหมาย (สัมพันธ์กับรูทโปรเจกต์) |
| `--force` | `-f` | เขียนทับไฟล์ที่มีอยู่แล้ว |

**Examples:**
```bash
# Create Auth middleware in app/middleware
php webman make:middleware Auth

# Create in plugin
php webman make:middleware Auth -p admin

# Custom path
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Generated file structure:**
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

**Notes:**
- วางใน `app/middleware/` โดยค่าเริ่มต้น
- ชื่อคลาสจะถูกเพิ่มไปยังไฟล์ config มิดเดิลแวร์โดยอัตโนมัติเพื่อเปิดใช้งาน

<a name="make-command"></a>
### make:command

สร้างคลาสคำสั่งคอนโซล

**Usage:**
```bash
php webman make:command <command-name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `command-name` | Yes | ชื่อคำสั่งในรูปแบบ `group:action` (เช่น `user:list`) |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | สร้างคำสั่งในไดเรกทอรีปลั๊กอินที่กำหนด |
| `--path` | `-P` | ไดเรกทอรีเป้าหมาย (สัมพันธ์กับรูทโปรเจกต์) |
| `--force` | `-f` | เขียนทับไฟล์ที่มีอยู่แล้ว |

**Examples:**
```bash
# Create user:list command in app/command
php webman make:command user:list

# Create in plugin
php webman make:command user:list -p admin

# Custom path
php webman make:command user:list -P plugin/admin/app/command

# Overwrite existing file
php webman make:command user:list -f
```

**Generated file structure:**
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

**Notes:**
- วางใน `app/command/` โดยค่าเริ่มต้น

<a name="make-bootstrap"></a>
### make:bootstrap

สร้างคลาสเริ่มต้นบูตสแตรป เมธอด `start` จะถูกเรียกโดยอัตโนมัติเมื่อโปรเซสเริ่มทำงาน โดยทั่วไปใช้สำหรับการเริ่มต้นแบบโกลบอล

**Usage:**
```bash
php webman make:bootstrap <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อคลาส Bootstrap |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | สร้างในไดเรกทอรีปลั๊กอินที่กำหนด |
| `--path` | `-P` | ไดเรกทอรีเป้าหมาย (สัมพันธ์กับรูทโปรเจกต์) |
| `--force` | `-f` | เขียนทับไฟล์ที่มีอยู่แล้ว |

**Examples:**
```bash
# Create MyBootstrap in app/bootstrap
php webman make:bootstrap MyBootstrap

# Create without auto-enabling
php webman make:bootstrap MyBootstrap no

# Create in plugin
php webman make:bootstrap MyBootstrap -p admin

# Custom path
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Overwrite existing file
php webman make:bootstrap MyBootstrap -f
```

**Generated file structure:**
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

**Notes:**
- วางใน `app/bootstrap/` โดยค่าเริ่มต้น
- เมื่อเปิดใช้งาน คลาสจะถูกเพิ่มไปยัง `config/bootstrap.php` (หรือ `plugin/<plugin>/config/bootstrap.php` สำหรับปลั๊กอิน)

<a name="make-process"></a>
### make:process

สร้างคลาสโปรเซสกำหนดเองและเขียนไปยัง `config/process.php` เพื่อเริ่มทำงานอัตโนมัติ

**Usage:**
```bash
php webman make:process <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อคลาสโปรเซส (เช่น MyTcp, MyWebsocket) |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | สร้างในไดเรกทอรีปลั๊กอินที่กำหนด |
| `--path` | `-P` | ไดเรกทอรีเป้าหมาย (สัมพันธ์กับรูทโปรเจกต์) |
| `--force` | `-f` | เขียนทับไฟล์ที่มีอยู่แล้ว |

**Examples:**
```bash
# Create in app/process
php webman make:process MyTcp

# Create in plugin
php webman make:process MyProcess -p admin

# Custom path
php webman make:process MyProcess -P plugin/admin/app/process

# Overwrite existing file
php webman make:process MyProcess -f
```

**Interactive flow:** จะถามตามลำดับ: ฟังพอร์ตหรือไม่ → ประเภทโปรโตคอล (websocket/http/tcp/udp/unixsocket) → ที่อยู่ฟัง (IP:port หรือเส้นทาง unix socket) → จำนวนโปรเซส โปรโตคอล HTTP จะถามโหมดในตัวหรือกำหนดเองด้วย

**Generated file structure:**

Non-listening process (only `onWorkerStart`):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

TCP/WebSocket listening processes generate corresponding `onConnect`, `onMessage`, `onClose` callback templates.

**Notes:**
- วางใน `app/process/` โดยค่าเริ่มต้น config โปรเซสเขียนไปยัง `config/process.php`
- คีย์ config เป็น snake_case ของชื่อคลาส จะล้มเหลวหากมีอยู่แล้ว
- โหมด HTTP ในตัวจะใช้ไฟล์โปรเซส `app\process\Http` ซ้ำ ไม่สร้างไฟล์ใหม่
- โปรโตคอลที่รองรับ: websocket, http, tcp, udp, unixsocket

## Build and Deployment

<a name="build-phar"></a>
### build:phar

บีบอัดโปรเจกต์เป็นไฟล์เก็บ PHAR เพื่อการแจกจ่ายและติดตั้ง

**Usage:**
```bash
php webman build:phar
```

**Start:**

Navigate to build directory and run

```bash
php webman.phar start
```

**Notes:**
* โปรเจกต์ที่บีบอัดแล้วไม่รองรับ reload ใช้ restart เพื่ออัปเดตโค้ด

* เพื่อหลีกเลี่ยงขนาดไฟล์ใหญ่และการใช้หน่วยความจำมาก ให้กำหนด exclude_pattern และ exclude_files ใน config/plugin/webman/console/app.php เพื่อยกเว้นไฟล์ที่ไม่จำเป็น

* การรัน webman.phar จะสร้างไดเรกทอรี runtime ในตำแหน่งเดียวกันสำหรับล็อกและไฟล์ชั่วคราว

* หากโปรเจกต์ใช้ไฟล์ .env ให้วาง .env ในไดเรกทอรีเดียวกับ webman.phar

* webman.phar ไม่รองรับโปรเซสกำหนดเองบน Windows

* ห้ามเก็บไฟล์ที่ผู้ใช้อัปโหลดไว้ในแพ็กเกจ phar การดำเนินการกับไฟล์อัปโหลดของผู้ใช้ผ่าน phar:// เป็นอันตราย (ช่องโหว่ phar deserialization) ไฟล์อัปโหลดของผู้ใช้ต้องเก็บแยกต่างหากบนดิสก์นอก phar ดูด้านล่าง

* หากธุรกิจของคุณต้องอัปโหลดไฟล์ไปยังไดเรกทอรี public ให้แตกไดเรกทอรี public ออกมาวางในตำแหน่งเดียวกับ webman.phar และกำหนด config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
ใช้ฟังก์ชันช่วย public_path($relative_path) เพื่อรับเส้นทางไดเรกทอรี public จริง


<a name="build-bin"></a>
### build:bin

บีบอัดโปรเจกต์เป็นไฟล์ไบนารีแบบสแตนด์อโลนพร้อม PHP runtime ในตัว ไม่ต้องติดตั้ง PHP บนสภาพแวดล้อมเป้าหมาย

**Usage:**
```bash
php webman build:bin [version]
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `version` | No | เวอร์ชัน PHP (เช่น 8.1, 8.2) ค่าเริ่มต้นเป็นเวอร์ชัน PHP ปัจจุบัน ขั้นต่ำ 8.1 |

**Examples:**
```bash
# Use current PHP version
php webman build:bin

# Specify PHP 8.2
php webman build:bin 8.2
```

**Start:**

Navigate to build directory and run

```bash
./webman.bin start
```

**Notes:**
* แนะนำอย่างยิ่ง: เวอร์ชัน PHP ในเครื่องควรตรงกับเวอร์ชันที่ใช้บีบอัด (เช่น PHP 8.1 ในเครื่อง → บีบอัดด้วย 8.1) เพื่อหลีกเลี่ยงปัญหาความเข้ากันได้
* การบีบอัดจะดาวน์โหลดซอร์ส PHP 8 แต่จะไม่ติดตั้งในเครื่อง ไม่กระทบสภาพแวดล้อม PHP ในเครื่อง
* webman.bin ปัจจุบันรันได้เฉพาะบน x86_64 Linux ไม่รองรับบน macOS
* โปรเจกต์ที่บีบอัดแล้วไม่รองรับ reload ใช้ restart เพื่ออัปเดตโค้ด
* .env ไม่ถูกบีบอัดโดยค่าเริ่มต้น (ควบคุมโดย exclude_files ใน config/plugin/webman/console/app.php) ให้วาง .env ในไดเรกทอรีเดียวกับ webman.bin เมื่อเริ่มทำงาน
* จะสร้างไดเรกทอรี runtime ในไดเรกทอรี webman.bin สำหรับไฟล์ล็อก
* webman.bin ไม่อ่าน php.ini ภายนอก สำหรับการตั้งค่า php.ini แบบกำหนดเอง ให้ใช้ custom_ini ใน config/plugin/webman/console/app.php
* ยกเว้นไฟล์ที่ไม่จำเป็นผ่าน config/plugin/webman/console/app.php เพื่อหลีกเลี่ยงขนาดแพ็กเกจใหญ่
* การบีบอัดไบนารีไม่รองรับ Swoole coroutines
* ห้ามเก็บไฟล์ที่ผู้ใช้อัปโหลดไว้ในแพ็กเกจไบนารี การดำเนินการผ่าน phar:// เป็นอันตราย (ช่องโหว่ phar deserialization) ไฟล์อัปโหลดของผู้ใช้ต้องเก็บแยกต่างหากบนดิสก์นอกแพ็กเกจ
* หากธุรกิจของคุณต้องอัปโหลดไฟล์ไปยังไดเรกทอรี public ให้แตกไดเรกทอรี public ออกมาวางในตำแหน่งเดียวกับ webman.bin และกำหนด config/app.php ดังด้านล่าง จากนั้นบีบอัดใหม่:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

รันสคริปต์ติดตั้งเฟรมเวิร์ก Webman (เรียก `\Webman\Install::install()` สำหรับการเริ่มต้นโปรเจกต์)

**Usage:**
```bash
php webman install
```

## Utility Commands

<a name="version"></a>
### version

แสดงเวอร์ชัน workerman/webman-framework

**Usage:**
```bash
php webman version
```

**Notes:** อ่านเวอร์ชันจาก `vendor/composer/installed.php` คืนค่าความล้มเหลวหากอ่านไม่ได้

<a name="fix-disable-functions"></a>
### fix-disable-functions

แก้ไข `disable_functions` ใน php.ini ลบฟังก์ชันที่ Webman ต้องการ

**Usage:**
```bash
php webman fix-disable-functions
```

**Notes:** ลบฟังก์ชันต่อไปนี้ (และตัวที่ตรงกับคำนำหน้า) ออกจาก `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec` ข้ามหากไม่พบ php.ini หรือ `disable_functions` ว่างเปล่า **จะแก้ไขไฟล์ php.ini โดยตรง** แนะนำให้สำรองข้อมูลก่อน

<a name="route-list"></a>
### route:list

แสดงเส้นทางที่ลงทะเบียนทั้งหมดในรูปแบบตาราง

**Usage:**
```bash
php webman route:list
```

**Output example:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Output columns:** URI, Method, Callback, Middleware, Name. Closure callbacks display as "Closure".

## App Plugin Management (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

สร้างปลั๊กอินแอปพลิเคชันใหม่ สร้างโครงสร้างไดเรกทอรีและไฟล์พื้นฐานครบถ้วนภายใต้ `plugin/<name>`

**Usage:**
```bash
php webman app-plugin:create <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อปลั๊กอิน ต้องตรงกับ `[a-zA-Z0-9][a-zA-Z0-9_-]*` ไม่มี `/` หรือ `\` |

**Examples:**
```bash
# Create app plugin named foo
php webman app-plugin:create foo

# Create plugin with hyphen
php webman app-plugin:create my-app
```

**Generated directory structure:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, etc.
├── api/Install.php  # Install/uninstall/update hooks
├── public/
└── install.sql
```

**Notes:**
- ปลั๊กอินถูกสร้างภายใต้ `plugin/<name>/` จะล้มเหลวหากไดเรกทอรีมีอยู่แล้ว

<a name="app-plugin-install"></a>
### app-plugin:install

ติดตั้งปลั๊กอินแอปพลิเคชัน รัน `plugin/<name>/api/Install::install($version)`

**Usage:**
```bash
php webman app-plugin:install <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อปลั๊กอิน ต้องตรงกับ `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Examples:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

ถอนการติดตั้งปลั๊กอินแอปพลิเคชัน รัน `plugin/<name>/api/Install::uninstall($version)`

**Usage:**
```bash
php webman app-plugin:uninstall <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อปลั๊กอิน |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--yes` | `-y` | ข้ามการยืนยัน รันทันที |

**Examples:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

อัปเดตปลั๊กอินแอปพลิเคชัน รัน `Install::beforeUpdate($from, $to)` และ `Install::update($from, $to, $context)` ตามลำดับ

**Usage:**
```bash
php webman app-plugin:update <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อปลั๊กอิน |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--from` | `-f` | เวอร์ชันต้นทาง ค่าเริ่มต้นเป็นเวอร์ชันปัจจุบัน |
| `--to` | `-t` | เวอร์ชันปลายทาง ค่าเริ่มต้นเป็นเวอร์ชันปัจจุบัน |

**Examples:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

บีบอัดปลั๊กอินแอปพลิเคชันเป็นไฟล์ ZIP  output ไปยัง `plugin/<name>.zip`

**Usage:**
```bash
php webman app-plugin:zip <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อปลั๊กอิน |

**Examples:**
```bash
php webman app-plugin:zip foo
```

**Notes:**
- ยกเว้นอัตโนมัติ `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__` เป็นต้น

## Plugin Management (plugin:*)

<a name="plugin-create"></a>
### plugin:create

สร้างปลั๊กอิน Webman ใหม่ (รูปแบบแพ็กเกจ Composer) สร้างไดเรกทอรี config `config/plugin/<name>` และไดเรกทอรีซอร์สปลั๊กอิน `vendor/<name>`

**Usage:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อแพ็กเกจปลั๊กอิน ในรูปแบบ `vendor/package` (เช่น `foo/my-admin`) ต้องเป็นไปตามการตั้งชื่อแพ็กเกจ Composer |

**Examples:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Generated structure:**
- `config/plugin/<name>/app.php`: config ปลั๊กอิน (รวมสวิตช์ `enable`)
- `vendor/<name>/composer.json`: คำจำกัดความแพ็กเกจปลั๊กอิน
- `vendor/<name>/src/`: ไดเรกทอรีซอร์สปลั๊กอิน
- เพิ่ม PSR-4 mapping ไปยัง `composer.json` ที่รูทโปรเจกต์โดยอัตโนมัติ
- รัน `composer dumpautoload` เพื่อรีเฟรช autoloading

**Notes:**
- ชื่อต้องเป็นรูปแบบ `vendor/package`: ตัวอักษรพิมพ์เล็ก ตัวเลข `-` `_` `.` และต้องมี `/` หนึ่งตัว
- จะล้มเหลวหาก `config/plugin/<name>` หรือ `vendor/<name>` มีอยู่แล้ว
- ข้อผิดพลาดหากส่งทั้ง argument และ `--name` พร้อมค่าต่างกัน

<a name="plugin-install"></a>
### plugin:install

รันสคริปต์ติดตั้งปลั๊กอิน (`Install::install()`) คัดลอกทรัพยากรปลั๊กอินไปยังไดเรกทอรีโปรเจกต์

**Usage:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อแพ็กเกจปลั๊กอิน ในรูปแบบ `vendor/package` (เช่น `foo/my-admin`) |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | ระบุชื่อปลั๊กอินเป็นตัวเลือก ใช้ตัวใดตัวหนึ่งกับ argument |

**Examples:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

รันสคริปต์ถอนการติดตั้งปลั๊กอิน (`Install::uninstall()`) ลบทรัพยากรปลั๊กอินออกจากโปรเจกต์

**Usage:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อแพ็กเกจปลั๊กอิน ในรูปแบบ `vendor/package` |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | ระบุชื่อปลั๊กอินเป็นตัวเลือก ใช้ตัวใดตัวหนึ่งกับ argument |

**Examples:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

เปิดใช้งานปลั๊กอิน ตั้ง `enable` เป็น `true` ใน `config/plugin/<name>/app.php`

**Usage:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อแพ็กเกจปลั๊กอิน ในรูปแบบ `vendor/package` |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | ระบุชื่อปลั๊กอินเป็นตัวเลือก ใช้ตัวใดตัวหนึ่งกับ argument |

**Examples:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

ปิดใช้งานปลั๊กอิน ตั้ง `enable` เป็น `false` ใน `config/plugin/<name>/app.php`

**Usage:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อแพ็กเกจปลั๊กอิน ในรูปแบบ `vendor/package` |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | ระบุชื่อปลั๊กอินเป็นตัวเลือก ใช้ตัวใดตัวหนึ่งกับ argument |

**Examples:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

ส่งออก config ปลั๊กอินและไดเรกทอรีที่กำหนดจากโปรเจกต์ไปยัง `vendor/<name>/src/` และสร้าง `Install.php` สำหรับการบีบอัดและเผยแพร่

**Usage:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | ชื่อแพ็กเกจปลั๊กอิน ในรูปแบบ `vendor/package` |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--name` | | ระบุชื่อปลั๊กอินเป็นตัวเลือก ใช้ตัวใดตัวหนึ่งกับ argument |
| `--source` | `-s` | เส้นทางที่จะส่งออก (สัมพันธ์กับรูทโปรเจกต์) ระบุได้หลายครั้ง |

**Examples:**
```bash
# Export plugin, default includes config/plugin/<name>
php webman plugin:export foo/my-admin

# Additionally export app, config, etc.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Notes:**
- ชื่อปลั๊กอินต้องเป็นไปตามการตั้งชื่อแพ็กเกจ Composer (`vendor/package`)
- หากมี `config/plugin/<name>` และไม่อยู่ใน `--source` จะถูกเพิ่มเข้าไปในรายการส่งออกโดยอัตโนมัติ
- `Install.php` ที่ส่งออกมี `pathRelation` สำหรับใช้โดย `plugin:install` / `plugin:uninstall`
- `plugin:install` และ `plugin:uninstall` ต้องมีปลั๊กอินใน `vendor/<name>` พร้อมคลาส `Install` และค่าคงที่ `WEBMAN_PLUGIN`

## Service Management

<a name="start"></a>
### start

เริ่มกระบวนการเวิร์กเกอร์ Webman โหมด DEBUG เป็นค่าเริ่มต้น (foreground)

**Usage:**
```bash
php webman start
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | เริ่มในโหมด DAEMON (background) |

<a name="stop"></a>
### stop

หยุดกระบวนการเวิร์กเกอร์ Webman

**Usage:**
```bash
php webman stop
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | หยุดอย่างนุ่มนวล รอให้คำขอปัจจุบันเสร็จก่อนออก |

<a name="restart"></a>
### restart

รีสตาร์ทกระบวนการเวิร์กเกอร์ Webman

**Usage:**
```bash
php webman restart
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | รันในโหมด DAEMON หลังรีสตาร์ท |
| `--graceful` | `-g` | หยุดอย่างนุ่มนวลก่อนรีสตาร์ท |

<a name="reload"></a>
### reload

โหลดโค้ดใหม่โดยไม่หยุดทำงาน สำหรับ hot-reload หลังอัปเดตโค้ด

**Usage:**
```bash
php webman reload
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | โหลดใหม่อย่างนุ่มนวล รอให้คำขอปัจจุบันเสร็จก่อนโหลดใหม่ |

<a name="status"></a>
### status

ดูสถานะการรันของกระบวนการเวิร์กเกอร์

**Usage:**
```bash
php webman status
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--live` | `-d` | แสดงรายละเอียด (สถานะแบบเรียลไทม์) |

<a name="connections"></a>
### connections

ดูข้อมูลการเชื่อมต่อของกระบวนการเวิร์กเกอร์

**Usage:**
```bash
php webman connections
```
