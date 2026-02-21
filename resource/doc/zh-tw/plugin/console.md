# 命令列

Webman 命令列元件

## 安裝
```
composer require webman/console
```

## 目錄

### 程式碼產生
- [make:controller](#make-controller) - 產生控制器類別
- [make:model](#make-model) - 從資料庫表產生模型類別
- [make:crud](#make-crud) - 產生完整 CRUD（模型 + 控制器 + 驗證器）
- [make:middleware](#make-middleware) - 產生中介軟體類別
- [make:command](#make-command) - 產生控制台命令類別
- [make:bootstrap](#make-bootstrap) - 產生啟動初始化類別
- [make:process](#make-process) - 產生自訂行程類別

### 建置與部署
- [build:phar](#build-phar) - 將專案打包為 PHAR 封存檔
- [build:bin](#build-bin) - 將專案打包為獨立二進位檔
- [install](#install) - 執行 Webman 安裝腳本

### 實用工具命令
- [version](#version) - 顯示 Webman 框架版本
- [fix-disable-functions](#fix-disable-functions) - 修復 php.ini 中的停用函式
- [route:list](#route-list) - 顯示所有已註冊的路由

### 應用外掛程式管理 (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - 建立新應用外掛程式
- [app-plugin:install](#app-plugin-install) - 安裝應用外掛程式
- [app-plugin:uninstall](#app-plugin-uninstall) - 移除應用外掛程式
- [app-plugin:update](#app-plugin-update) - 更新應用外掛程式
- [app-plugin:zip](#app-plugin-zip) - 將應用外掛程式打包為 ZIP

### 外掛程式管理 (plugin:*)
- [plugin:create](#plugin-create) - 建立新 Webman 外掛程式
- [plugin:install](#plugin-install) - 安裝 Webman 外掛程式
- [plugin:uninstall](#plugin-uninstall) - 移除 Webman 外掛程式
- [plugin:enable](#plugin-enable) - 啟用 Webman 外掛程式
- [plugin:disable](#plugin-disable) - 停用 Webman 外掛程式
- [plugin:export](#plugin-export) - 匯出外掛程式原始碼

### 服務管理
- [start](#start) - 啟動 Webman 工作行程
- [stop](#stop) - 停止 Webman 工作行程
- [restart](#restart) - 重新啟動 Webman 工作行程
- [reload](#reload) - 無停機重載程式碼
- [status](#status) - 檢視工作行程狀態
- [connections](#connections) - 取得工作行程連線資訊

## 程式碼產生

<a name="make-controller"></a>
### make:controller

產生控制器類別。

**用法：**
```bash
php webman make:controller <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 控制器名稱（不含後綴） |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--plugin` | `-p` | 在對應外掛程式目錄產生控制器 |
| `--path` | `-P` | 自訂控制器路徑 |
| `--force` | `-f` | 若檔案已存在則覆蓋 |
| `--no-suffix` | | 不追加 "Controller" 後綴 |

**範例：**
```bash
# 在 app/controller 中建立 UserController
php webman make:controller User

# 在外掛程式中建立
php webman make:controller AdminUser -p admin

# 自訂路徑
php webman make:controller User -P app/api/controller

# 覆蓋現有檔案
php webman make:controller User -f

# 建立時不帶 "Controller" 後綴
php webman make:controller UserHandler --no-suffix
```

**產生的檔案結構：**
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

**說明：**
- 控制器預設放在 `app/controller/` 目錄
- 自動追加設定的控制器後綴
- 若檔案已存在會提示是否覆蓋（下同）

<a name="make-model"></a>
### make:model

從資料庫表產生模型類別，支援 Laravel ORM 與 ThinkORM。

**用法：**
```bash
php webman make:model [名稱]
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 否 | 模型類別名稱，互動模式下可省略 |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--plugin` | `-p` | 在對應外掛程式目錄產生模型 |
| `--path` | `-P` | 目標目錄（相對專案根路徑） |
| `--table` | `-t` | 指定表名，表名不符合約定時建議顯式指定 |
| `--orm` | `-o` | 選擇 ORM：`laravel` 或 `thinkorm` |
| `--database` | `-d` | 指定資料庫連線名稱 |
| `--force` | `-f` | 覆蓋已存在檔案 |

**路徑說明：**
- 預設：`app/model/`（主應用）或 `plugin/<外掛程式>/app/model/`（外掛程式）
- `--path` 為相對專案根路徑，如 `plugin/admin/app/model`
- 同時使用 `--plugin` 與 `--path` 時，兩者必須指向同一目錄

**範例：**
```bash
# 在 app/model 中建立 User 模型
php webman make:model User

# 指定表名和 ORM
php webman make:model User -t wa_users -o laravel

# 在外掛程式中建立
php webman make:model AdminUser -p admin

# 自訂路徑
php webman make:model User -P plugin/admin/app/model
```

**互動模式：** 不傳名稱時進入互動流程：選表 → 輸入模型名稱 → 輸入路徑。支援：Enter 查看更多、`0` 建立空模型、`/關鍵字` 篩選表。

**產生的檔案結構：**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (主鍵)
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

根據表結構自動產生 `@property` 註解。支援 MySQL、PostgreSQL。

<a name="make-crud"></a>
### make:crud

根據資料庫表一次性產生模型、控制器和驗證器，形成完整 CRUD 能力。

**用法：**
```bash
php webman make:crud
```

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--table` | `-t` | 指定表名 |
| `--model` | `-m` | 模型類別名稱 |
| `--model-path` | `-M` | 模型目錄（相對專案根） |
| `--controller` | `-c` | 控制器類別名稱 |
| `--controller-path` | `-C` | 控制器目錄 |
| `--validator` | | 驗證器類別名稱（依賴 `webman/validation`） |
| `--validator-path` | | 驗證器目錄（依賴 `webman/validation`） |
| `--plugin` | `-p` | 在對應外掛程式目錄產生相關檔案 |
| `--orm` | `-o` | ORM：`laravel` 或 `thinkorm` |
| `--database` | `-d` | 資料庫連線名稱 |
| `--force` | `-f` | 覆蓋已存在檔案 |
| `--no-validator` | | 不產生驗證器 |
| `--no-interaction` | `-n` | 非互動模式，使用預設值 |

**執行流程：** 未指定 `--table` 時進入互動選表；模型名稱預設由表名推斷；控制器名稱預設由模型名稱 + 控制器後綴；驗證器名稱預設由控制器名稱去掉後綴 + `Validator`。各路徑預設：模型 `app/model/`，控制器 `app/controller/`，驗證器 `app/validation/`；外掛程式下為 `plugin/<外掛程式>/app/` 對應子目錄。

**範例：**
```bash
# 互動式產生（選表後逐步確認）
php webman make:crud

# 指定表名
php webman make:crud --table=users

# 指定表名和外掛程式
php webman make:crud --table=users --plugin=admin

# 指定各路徑
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# 不產生驗證器
php webman make:crud --table=users --no-validator

# 非互動 + 覆蓋
php webman make:crud --table=users --no-interaction --force
```

**產生的檔案結構：**

模型（`app/model/User.php`）：
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

控制器（`app/controller/UserController.php`）：
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

驗證器（`app/validation/UserValidator.php`）：
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
        'id' => '主鍵',
        'username' => '用戶名'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**說明：**
- 若沒安裝或沒啟用 `webman/validation` 則自動略過驗證器產生（安裝方法 `composer require webman/validation`）。
- 驗證器的 `attributes` 根據資料庫欄位註解自動產生，無註解則不產生 `attributes`。
- 驗證器錯誤訊息支援多語系，語言根據 `config('translation.locale')` 自動選擇。

<a name="make-middleware"></a>
### make:middleware

產生中介軟體類別，並自動註冊到 `config/middleware.php`（外掛程式則為 `plugin/<外掛程式>/config/middleware.php`）。

**用法：**
```bash
php webman make:middleware <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 中介軟體名稱 |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--plugin` | `-p` | 在對應外掛程式目錄產生中介軟體 |
| `--path` | `-P` | 目標目錄（相對專案根路徑） |
| `--force` | `-f` | 覆蓋已存在檔案 |

**範例：**
```bash
# 在 app/middleware 中建立 Auth 中介軟體
php webman make:middleware Auth

# 在外掛程式中建立
php webman make:middleware Auth -p admin

# 自訂路徑
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**產生的檔案結構：**
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

**說明：**
- 預設放在 `app/middleware/` 目錄
- 建立後會自動將類別名稱追加到對應 middleware 設定檔中自動啟用

<a name="make-command"></a>
### make:command

產生控制台命令類別。

**用法：**
```bash
php webman make:command <命令名>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `命令名` | 是 | 命令名稱，格式如 `group:action`（如 `user:list`） |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--plugin` | `-p` | 在對應外掛程式目錄產生命令 |
| `--path` | `-P` | 目標目錄（相對專案根路徑） |
| `--force` | `-f` | 覆蓋已存在檔案 |

**範例：**
```bash
# 在 app/command 中建立 user:list 命令
php webman make:command user:list

# 在外掛程式中建立
php webman make:command user:list -p admin

# 自訂路徑
php webman make:command user:list -P plugin/admin/app/command

# 覆蓋現有檔案
php webman make:command user:list -f
```

**產生的檔案結構：**
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

**說明：**
- 預設放在 `app/command/` 目錄

<a name="make-bootstrap"></a>
### make:bootstrap

產生啟動初始化類別（Bootstrap），行程啟動時會自動呼叫類別的 start 方法，一般用於在行程啟動時執行一些全域初始化操作。

**用法：**
```bash
php webman make:bootstrap <名稱> 
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | Bootstrap 類別名稱 |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--plugin` | `-p` | 在外掛程式目錄產生 |
| `--path` | `-P` | 目標目錄（相對專案根路徑） |
| `--force` | `-f` | 覆蓋已存在檔案 |

**範例：**
```bash
# 在 app/bootstrap 中建立 MyBootstrap
php webman make:bootstrap MyBootstrap

# 建立但不自動啟用
php webman make:bootstrap MyBootstrap no

# 在外掛程式中建立
php webman make:bootstrap MyBootstrap -p admin

# 自訂路徑
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# 覆蓋現有檔案
php webman make:bootstrap MyBootstrap -f
```

**產生的檔案結構：**
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

**說明：**
- 預設放在 `app/bootstrap/` 目錄
- 啟用時會將類別追加到 `config/bootstrap.php`（外掛程式為 `plugin/<外掛程式>/config/bootstrap.php`）

<a name="make-process"></a>
### make:process

產生自訂行程類別，並寫入 `config/process.php` 設定自動啟動。

**用法：**
```bash
php webman make:process <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 行程類別名稱（如 MyTcp、MyWebsocket） |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--plugin` | `-p` | 在外掛程式目錄產生 |
| `--path` | `-P` | 目標目錄（相對專案根路徑） |
| `--force` | `-f` | 覆蓋已存在檔案 |

**範例：**
```bash
# 在 app/process 中建立
php webman make:process MyTcp

# 在外掛程式中建立
php webman make:process MyProcess -p admin

# 自訂路徑
php webman make:process MyProcess -P plugin/admin/app/process

# 覆蓋現有檔案
php webman make:process MyProcess -f
```

**互動流程：** 執行後會依序詢問：是否監聽埠 → 協定類型（websocket/http/tcp/udp/unixsocket）→ 監聽位址（IP+埠或 unix socket 路徑）→ 行程數量。HTTP 協定還會詢問使用內建模式或自訂模式。

**產生的檔案結構：**

非監聽行程（僅 `onWorkerStart`）：
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

TCP/WebSocket 等監聽行程會產生對應的 `onConnect`、`onMessage`、`onClose` 等回呼範本。

**說明：**
- 預設放在 `app/process/` 目錄，行程設定寫入 `config/process.php`
- 設定鍵為類別名稱的 snake_case，若已存在則失敗
- HTTP 內建模式複用 `app\process\Http` 行程檔案，不產生新檔案
- 支援協定：websocket、http、tcp、udp、unixsocket

## 建置與部署

<a name="build-phar"></a>
### build:phar

將專案打包為 PHAR 封存檔，便於分發與部署。

**用法：**
```bash
php webman build:phar
```

**啟動：**

進入 build 目錄執行

```bash
php webman.phar start
```

**注意事項**
* 打包後的專案不支援 reload，更新程式碼需要 restart 重新啟動

* 為避免打包檔案尺寸過大佔用過多記憶體，可設定 config/plugin/webman/console/app.php 裡的 exclude_pattern exclude_files 選項將排除不必要的檔案。

* 執行 webman.phar 後會在 webman.phar 所在目錄產生 runtime 目錄，用於存放日誌等暫存檔案。

* 若專案中使用了 .env 檔案，需將 .env 檔案放在 webman.phar 所在目錄。

* 注意 webman.phar 不支援在 Windows 下開啟自訂行程

* 切勿將使用者上傳的檔案儲存在 phar 套件中，因為以 phar:// 協定操作使用者上傳的檔案非常危險（phar 反序列化漏洞）。使用者上傳的檔案必須單獨儲存在 phar 套件之外的磁碟中，參見下方。

* 若業務需要上傳檔案到 public 目錄，需將 public 目錄獨立出來放在 webman.phar 所在目錄，此時需設定 config/app.php。
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
業務可使用輔助函式 public_path($檔案相對位置) 找到實際的 public 目錄位置。


<a name="build-bin"></a>
### build:bin

將專案打包為獨立二進位檔，內含 PHP 執行環境，無需目標環境安裝 PHP。

**用法：**
```bash
php webman build:bin [版本]
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `版本` | 否 | PHP 版本號（如 8.1、8.2），預設為目前 PHP 版本，最低 8.1 |

**範例：**
```bash
# 使用目前 PHP 版本
php webman build:bin

# 指定 PHP 8.2
php webman build:bin 8.2
```

**啟動：**

進入 build 目錄執行

```bash
./webman.bin start
```

**注意事項：**
* 強烈建議本機 php 版本與打包版本一致，例如本機為 php8.1，打包也用 php8.1，避免出現相容性問題
* 打包會下載 php8 的原始碼，但並不會在本機安裝，不會影響本機 php 環境
* webman.bin 目前僅支援在 x86_64 架構的 Linux 系統執行，不支援在 Mac 系統執行
* 打包後的專案不支援 reload，更新程式碼需要 restart 重新啟動
* 預設不打包 env 檔案（config/plugin/webman/console/app.php 中 exclude_files 控制），故啟動時 env 檔案應放置於 webman.bin 相同目錄下
* 執行過程中會在 webman.bin 所在目錄產生 runtime 目錄，用於存放日誌檔案
* 目前 webman.bin 不會讀取外部 php.ini 檔案，如需自訂 php.ini，請在 /config/plugin/webman/console/app.php 檔案 custom_ini 中設定
* 有些檔案不需打包，可設定 config/plugin/webman/console/app.php 排除，避免打包後檔案過大
* 二進位打包不支援使用 swoole 協程
* 切勿將使用者上傳的檔案儲存在二進位套件中，因為以 phar:// 協定操作使用者上傳的檔案非常危險（phar 反序列化漏洞）。使用者上傳的檔案必須單獨儲存在套件之外的磁碟中。
* 若業務需要上傳檔案到 public 目錄，需將 public 目錄獨立出來放在 webman.bin 所在目錄，此時需設定 config/app.php 如下並重新打包。
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

執行 Webman 框架的安裝腳本（呼叫 `\Webman\Install::install()`），用於專案初始化。

**用法：**
```bash
php webman install
```

## 實用工具命令

<a name="version"></a>
### version

顯示 workerman/webman-framework 版本。

**用法：**
```bash
php webman version
```

**說明：** 從 `vendor/composer/installed.php` 讀取版本，若無法讀取則回傳失敗。

<a name="fix-disable-functions"></a>
### fix-disable-functions

修復 php.ini 中的 `disable_functions`，移除 Webman 執行所需函式。

**用法：**
```bash
php webman fix-disable-functions
```

**說明：** 會從 `disable_functions` 中移除以下函式（及其前綴比對）：`stream_socket_server`、`stream_socket_accept`、`stream_socket_client`、`pcntl_*`、`posix_*`、`proc_*`、`shell_exec`、`exec`。若找不到 php.ini 或 `disable_functions` 為空則略過。**會直接修改 php.ini 檔案**，建議先備份。

<a name="route-list"></a>
### route:list

以表格形式列出所有已註冊路由。

**用法：**
```bash
php webman route:list
```

**輸出範例：**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | 方法   | 回呼                                          | 中介軟體     | 名稱 |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | 閉包                                          | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**輸出欄：** URI、方法、回呼、中介軟體、名稱。閉包回呼顯示為「閉包」。

## 應用外掛程式管理 (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

建立新應用外掛程式，產生 `plugin/<名稱>` 下的完整目錄結構與基礎檔案。

**用法：**
```bash
php webman app-plugin:create <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式名稱，需符合 `[a-zA-Z0-9][a-zA-Z0-9_-]*`，不能含 `/` 或 `\` |

**範例：**
```bash
# 建立名為 foo 的應用外掛程式
php webman app-plugin:create foo

# 建立帶連字號的外掛程式
php webman app-plugin:create my-app
```

**產生目錄結構：**
```
plugin/<名稱>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php、route.php、menu.php 等
├── api/Install.php  # 安裝/移除/更新鉤子
├── public/
└── install.sql
```

**說明：**
- 外掛程式建立在 `plugin/<名稱>/` 下，若目錄已存在則失敗

<a name="app-plugin-install"></a>
### app-plugin:install

安裝應用外掛程式，執行 `plugin/<名稱>/api/Install::install($version)`。

**用法：**
```bash
php webman app-plugin:install <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式名稱，需符合 `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**範例：**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

移除應用外掛程式，執行 `plugin/<名稱>/api/Install::uninstall($version)`。

**用法：**
```bash
php webman app-plugin:uninstall <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式名稱 |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--yes` | `-y` | 略過確認，直接執行 |

**範例：**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

更新應用外掛程式，依序執行 `Install::beforeUpdate($from, $to)` 和 `Install::update($from, $to, $context)`。

**用法：**
```bash
php webman app-plugin:update <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式名稱 |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--from` | `-f` | 起始版本，預設為目前版本 |
| `--to` | `-t` | 目標版本，預設為目前版本 |

**範例：**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

將應用外掛程式打包為 ZIP 檔案，輸出到 `plugin/<名稱>.zip`。

**用法：**
```bash
php webman app-plugin:zip <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式名稱 |

**範例：**
```bash
php webman app-plugin:zip foo
```

**說明：**
- 自動排除 `node_modules`、`.git`、`.idea`、`.vscode`、`__pycache__` 等目錄

## 外掛程式管理 (plugin:*)

<a name="plugin-create"></a>
### plugin:create

建立新 Webman 外掛程式（Composer 套件形式），產生 `config/plugin/<名稱>` 設定目錄與 `vendor/<名稱>` 外掛程式原始碼目錄。

**用法：**
```bash
php webman plugin:create <名稱>
php webman plugin:create --name <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式套件名稱，格式為 `vendor/package`（如 `foo/my-admin`），需符合 Composer 套件名稱規範 |

**範例：**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**產生結構：**
- `config/plugin/<名稱>/app.php`：外掛程式設定（含 `enable` 開關）
- `vendor/<名稱>/composer.json`：外掛程式套件定義
- `vendor/<名稱>/src/`：外掛程式原始碼目錄
- 自動向專案根 `composer.json` 新增 PSR-4 對應
- 執行 `composer dumpautoload` 重新整理自動載入

**說明：**
- 名稱必須為 `vendor/package` 格式，僅小寫字母、數字、`-`、`_`、`.`，且必須包含一個 `/`
- 若 `config/plugin/<名稱>` 或 `vendor/<名稱>` 已存在則失敗
- 同時傳入參數與 `--name` 且值不同時會報錯

<a name="plugin-install"></a>
### plugin:install

執行外掛程式的安裝腳本（`Install::install()`），將外掛程式資源複製到專案目錄。

**用法：**
```bash
php webman plugin:install <名稱>
php webman plugin:install --name <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式套件名稱，格式為 `vendor/package`（如 `foo/my-admin`） |

**選項：**

| 選項 | 描述 |
|--------|-------------|
| `--name` | 以選項形式指定外掛程式名稱，與參數二選一 |

**範例：**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

執行外掛程式的移除腳本（`Install::uninstall()`），移除外掛程式複製到專案中的資源。

**用法：**
```bash
php webman plugin:uninstall <名稱>
php webman plugin:uninstall --name <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式套件名稱，格式為 `vendor/package` |

**選項：**

| 選項 | 描述 |
|--------|-------------|
| `--name` | 以選項形式指定外掛程式名稱，與參數二選一 |

**範例：**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

啟用外掛程式，將 `config/plugin/<名稱>/app.php` 中的 `enable` 設為 `true`。

**用法：**
```bash
php webman plugin:enable <名稱>
php webman plugin:enable --name <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式套件名稱，格式為 `vendor/package` |

**選項：**

| 選項 | 描述 |
|--------|-------------|
| `--name` | 以選項形式指定外掛程式名稱，與參數二選一 |

**範例：**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

停用外掛程式，將 `config/plugin/<名稱>/app.php` 中的 `enable` 設為 `false`。

**用法：**
```bash
php webman plugin:disable <名稱>
php webman plugin:disable --name <名稱>
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式套件名稱，格式為 `vendor/package` |

**選項：**

| 選項 | 描述 |
|--------|-------------|
| `--name` | 以選項形式指定外掛程式名稱，與參數二選一 |

**範例：**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

將專案中的外掛程式設定及指定目錄匯出到 `vendor/<名稱>/src/`，並產生 `Install.php`，便於打包發布。

**用法：**
```bash
php webman plugin:export <名稱> [--source=路徑]...
php webman plugin:export --name <名稱> [--source=路徑]...
```

**參數：**

| 參數 | 必需 | 描述 |
|----------|----------|-------------|
| `名稱` | 是 | 外掛程式套件名稱，格式為 `vendor/package` |

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--name` | | 以選項形式指定外掛程式名稱，與參數二選一 |
| `--source` | `-s` | 要匯出的路徑（相對專案根），可多次指定 |

**範例：**
```bash
# 匯出外掛程式，預設包含 config/plugin/<名稱>
php webman plugin:export foo/my-admin

# 額外匯出 app、config 等目錄
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**說明：**
- 外掛程式名稱需符合 Composer 套件名稱規範（`vendor/package`）
- 若 `config/plugin/<名稱>` 存在且未在 `--source` 中，會自動加入匯出清單
- 匯出的 `Install.php` 含 `pathRelation`，供 `plugin:install` / `plugin:uninstall` 使用
- `plugin:install`、`plugin:uninstall` 要求外掛程式已存在於 `vendor/<名稱>`，且存在 `Install` 類別及 `WEBMAN_PLUGIN` 常數

## 服務管理

<a name="start"></a>
### start

啟動 Webman 工作行程，預設為 DEBUG 模式（前景執行）。

**用法：**
```bash
php webman start
```

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--daemon` | `-d` | 以 DAEMON 模式啟動（背景執行） |

<a name="stop"></a>
### stop

停止 Webman 工作行程。

**用法：**
```bash
php webman stop
```

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--graceful` | `-g` | 平滑停止，等待目前請求處理完成後再結束 |

<a name="restart"></a>
### restart

重新啟動 Webman 工作行程。

**用法：**
```bash
php webman restart
```

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--daemon` | `-d` | 重新啟動後以 DAEMON 模式執行 |
| `--graceful` | `-g` | 平滑停止後再重新啟動 |

<a name="reload"></a>
### reload

無停機重載程式碼，適用於程式碼更新後熱載入。

**用法：**
```bash
php webman reload
```

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--graceful` | `-g` | 平滑重載，等待目前請求處理完成後再重載 |

<a name="status"></a>
### status

檢視工作行程執行狀態。

**用法：**
```bash
php webman status
```

**選項：**

| 選項 | 快捷方式 | 描述 |
|--------|----------|-------------|
| `--live` | `-d` | 顯示詳情（即時狀態） |

<a name="connections"></a>
### connections

取得工作行程連線資訊。

**用法：**
```bash
php webman connections
```
