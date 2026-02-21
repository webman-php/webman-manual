# Command Line

Webman command-line component

## Installation
```
composer require webman/console
```

## Table of Contents

### Code Generation
- [make:controller](#make-controller) - Generate controller class
- [make:model](#make-model) - Generate model class from database table
- [make:crud](#make-crud) - Generate complete CRUD (model + controller + validator)
- [make:middleware](#make-middleware) - Generate middleware class
- [make:command](#make-command) - Generate console command class
- [make:bootstrap](#make-bootstrap) - Generate bootstrap initialization class
- [make:process](#make-process) - Generate custom process class

### Build and Deployment
- [build:phar](#build-phar) - Package project as PHAR archive
- [build:bin](#build-bin) - Package project as standalone binary
- [install](#install) - Run Webman installation script

### Utility Commands
- [version](#version) - Display Webman framework version
- [fix-disable-functions](#fix-disable-functions) - Fix disabled functions in php.ini
- [route:list](#route-list) - Display all registered routes

### App Plugin Management (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Create new app plugin
- [app-plugin:install](#app-plugin-install) - Install app plugin
- [app-plugin:uninstall](#app-plugin-uninstall) - Uninstall app plugin
- [app-plugin:update](#app-plugin-update) - Update app plugin
- [app-plugin:zip](#app-plugin-zip) - Package app plugin as ZIP

### Plugin Management (plugin:*)
- [plugin:create](#plugin-create) - Create new Webman plugin
- [plugin:install](#plugin-install) - Install Webman plugin
- [plugin:uninstall](#plugin-uninstall) - Uninstall Webman plugin
- [plugin:enable](#plugin-enable) - Enable Webman plugin
- [plugin:disable](#plugin-disable) - Disable Webman plugin
- [plugin:export](#plugin-export) - Export plugin source code

### Service Management
- [start](#start) - Start Webman worker processes
- [stop](#stop) - Stop Webman worker processes
- [restart](#restart) - Restart Webman worker processes
- [reload](#reload) - Reload code without downtime
- [status](#status) - View worker process status
- [connections](#connections) - Get worker process connection info

## Code Generation

<a name="make-controller"></a>
### make:controller

Generate controller class.

**Usage:**
```bash
php webman make:controller <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Controller name (without suffix) |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Generate controller in specified plugin directory |
| `--path` | `-P` | Custom controller path |
| `--force` | `-f` | Overwrite if file exists |
| `--no-suffix` | | Do not append "Controller" suffix |

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
- Controllers are placed in `app/controller/` by default
- Controller suffix from config is automatically appended
- Prompts for overwrite confirmation if file exists (same for other commands)

<a name="make-model"></a>
### make:model

Generate model class from database table. Supports Laravel ORM and ThinkORM.

**Usage:**
```bash
php webman make:model [name]
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | No | Model class name, can be omitted in interactive mode |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Generate model in specified plugin directory |
| `--path` | `-P` | Target directory (relative to project root) |
| `--table` | `-t` | Specify table name; recommended when table name doesn't follow convention |
| `--orm` | `-o` | Choose ORM: `laravel` or `thinkorm` |
| `--database` | `-d` | Specify database connection name |
| `--force` | `-f` | Overwrite existing file |

**Path notes:**
- Default: `app/model/` (main app) or `plugin/<plugin>/app/model/` (plugin)
- `--path` is relative to project root, e.g. `plugin/admin/app/model`
- When using both `--plugin` and `--path`, they must point to the same directory

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

**Interactive mode:** When name is omitted, enters interactive flow: select table → enter model name → enter path. Supports: Enter to see more, `0` to create empty model, `/keyword` to filter tables.

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

`@property` annotations are auto-generated from table structure. Supports MySQL and PostgreSQL.

<a name="make-crud"></a>
### make:crud

Generate model, controller and validator from database table in one go, forming complete CRUD capability.

**Usage:**
```bash
php webman make:crud
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--table` | `-t` | Specify table name |
| `--model` | `-m` | Model class name |
| `--model-path` | `-M` | Model directory (relative to project root) |
| `--controller` | `-c` | Controller class name |
| `--controller-path` | `-C` | Controller directory |
| `--validator` | | Validator class name (requires `webman/validation`) |
| `--validator-path` | | Validator directory (requires `webman/validation`) |
| `--plugin` | `-p` | Generate files in specified plugin directory |
| `--orm` | `-o` | ORM: `laravel` or `thinkorm` |
| `--database` | `-d` | Database connection name |
| `--force` | `-f` | Overwrite existing files |
| `--no-validator` | | Do not generate validator |
| `--no-interaction` | `-n` | Non-interactive mode, use defaults |

**Execution flow:** When `--table` is not specified, enters interactive table selection; model name defaults to inferred from table name; controller name defaults to model name + controller suffix; validator name defaults to controller name without suffix + `Validator`. Default paths: model `app/model/`, controller `app/controller/`, validator `app/validation/`; for plugins: corresponding subdirectories under `plugin/<plugin>/app/`.

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
        'id' => 'Primary key',
        'username' => 'Username'
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
- Validator generation is skipped if `webman/validation` is not installed or enabled (install with `composer require webman/validation`)
- Validator `attributes` are auto-generated from database field comments; no comments means no `attributes`
- Validator error messages support i18n; language is selected from `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

Generate middleware class and auto-register to `config/middleware.php` (or `plugin/<plugin>/config/middleware.php` for plugins).

**Usage:**
```bash
php webman make:middleware <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Middleware name |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Generate middleware in specified plugin directory |
| `--path` | `-P` | Target directory (relative to project root) |
| `--force` | `-f` | Overwrite existing file |

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
- Placed in `app/middleware/` by default
- Class name is automatically appended to middleware config file for activation

<a name="make-command"></a>
### make:command

Generate console command class.

**Usage:**
```bash
php webman make:command <command-name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `command-name` | Yes | Command name in format `group:action` (e.g. `user:list`) |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Generate command in specified plugin directory |
| `--path` | `-P` | Target directory (relative to project root) |
| `--force` | `-f` | Overwrite existing file |

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
- Placed in `app/command/` by default

<a name="make-bootstrap"></a>
### make:bootstrap

Generate bootstrap initialization class. The `start` method is automatically called when the process starts, typically for global initialization.

**Usage:**
```bash
php webman make:bootstrap <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Bootstrap class name |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Generate in specified plugin directory |
| `--path` | `-P` | Target directory (relative to project root) |
| `--force` | `-f` | Overwrite existing file |

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
- Placed in `app/bootstrap/` by default
- When enabling, class is appended to `config/bootstrap.php` (or `plugin/<plugin>/config/bootstrap.php` for plugins)

<a name="make-process"></a>
### make:process

Generate custom process class and write to `config/process.php` for auto-start.

**Usage:**
```bash
php webman make:process <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Process class name (e.g. MyTcp, MyWebsocket) |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Generate in specified plugin directory |
| `--path` | `-P` | Target directory (relative to project root) |
| `--force` | `-f` | Overwrite existing file |

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

**Interactive flow:** Prompts in order: listen on port? → protocol type (websocket/http/tcp/udp/unixsocket) → listen address (IP:port or unix socket path) → process count. HTTP protocol also asks for built-in or custom mode.

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
- Placed in `app/process/` by default; process config written to `config/process.php`
- Config key is snake_case of class name; fails if already exists
- HTTP built-in mode reuses `app\process\Http` process file, does not generate new file
- Supported protocols: websocket, http, tcp, udp, unixsocket

## Build and Deployment

<a name="build-phar"></a>
### build:phar

Package project as PHAR archive for distribution and deployment.

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
* Packaged project does not support reload; use restart to update code

* To avoid large file size and memory usage, configure exclude_pattern and exclude_files in config/plugin/webman/console/app.php to exclude unnecessary files.

* Running webman.phar creates a runtime directory in the same location for logs and temporary files.

* If your project uses .env file, place .env in the same directory as webman.phar.

* webman.phar does not support custom processes on Windows

* Never store user-uploaded files inside the phar package; operating on user uploads via phar:// is dangerous (phar deserialization vulnerability). User uploads must be stored separately on disk outside the phar. See below.

* If your business needs to upload files to the public directory, extract the public directory to the same location as webman.phar and configure config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Use the helper function public_path($relative_path) to get the actual public directory path.


<a name="build-bin"></a>
### build:bin

Package project as standalone binary with embedded PHP runtime. No PHP installation required on target environment.

**Usage:**
```bash
php webman build:bin [version]
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `version` | No | PHP version (e.g. 8.1, 8.2), defaults to current PHP version, minimum 8.1 |

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
* Strongly recommended: local PHP version should match build version (e.g. PHP 8.1 local → build with 8.1) to avoid compatibility issues
* Build downloads PHP 8 source but does not install locally; does not affect local PHP environment
* webman.bin currently only runs on x86_64 Linux; not supported on macOS
* Packaged project does not support reload; use restart to update code
* .env is not packaged by default (controlled by exclude_files in config/plugin/webman/console/app.php); place .env in the same directory as webman.bin when starting
* A runtime directory is created in the webman.bin directory for log files
* webman.bin does not read external php.ini; for custom php.ini settings, use custom_ini in config/plugin/webman/console/app.php
* Exclude unnecessary files via config/plugin/webman/console/app.php to avoid large package size
* Binary build does not support Swoole coroutines
* Never store user-uploaded files inside the binary package; operating via phar:// is dangerous (phar deserialization vulnerability). User uploads must be stored separately on disk outside the package.
* If your business needs to upload files to the public directory, extract the public directory to the same location as webman.bin and configure config/app.php as below, then rebuild:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Execute Webman framework installation script (calls `\Webman\Install::install()`), for project initialization.

**Usage:**
```bash
php webman install
```

## Utility Commands

<a name="version"></a>
### version

Display workerman/webman-framework version.

**Usage:**
```bash
php webman version
```

**Notes:** Reads version from `vendor/composer/installed.php`; returns failure if unable to read.

<a name="fix-disable-functions"></a>
### fix-disable-functions

Fix `disable_functions` in php.ini, removing functions required by Webman.

**Usage:**
```bash
php webman fix-disable-functions
```

**Notes:** Removes the following functions (and prefix matches) from `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Skips if php.ini not found or `disable_functions` is empty. **Directly modifies php.ini file**; backup recommended.

<a name="route-list"></a>
### route:list

List all registered routes in table format.

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

Create new app plugin, generating complete directory structure and base files under `plugin/<name>`.

**Usage:**
```bash
php webman app-plugin:create <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin name; must match `[a-zA-Z0-9][a-zA-Z0-9_-]*`, cannot contain `/` or `\` |

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
- Plugin created under `plugin/<name>/`; fails if directory already exists

<a name="app-plugin-install"></a>
### app-plugin:install

Install app plugin, executing `plugin/<name>/api/Install::install($version)`.

**Usage:**
```bash
php webman app-plugin:install <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin name; must match `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Examples:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Uninstall app plugin, executing `plugin/<name>/api/Install::uninstall($version)`.

**Usage:**
```bash
php webman app-plugin:uninstall <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin name |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--yes` | `-y` | Skip confirmation, execute directly |

**Examples:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Update app plugin, executing `Install::beforeUpdate($from, $to)` and `Install::update($from, $to, $context)` in order.

**Usage:**
```bash
php webman app-plugin:update <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin name |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--from` | `-f` | From version, defaults to current version |
| `--to` | `-t` | To version, defaults to current version |

**Examples:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Package app plugin as ZIP file, output to `plugin/<name>.zip`.

**Usage:**
```bash
php webman app-plugin:zip <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin name |

**Examples:**
```bash
php webman app-plugin:zip foo
```

**Notes:**
- Automatically excludes `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__`, etc.

## Plugin Management (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Create new Webman plugin (Composer package form), generating `config/plugin/<name>` config directory and `vendor/<name>` plugin source directory.

**Usage:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin package name in format `vendor/package` (e.g. `foo/my-admin`); must follow Composer package naming |

**Examples:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Generated structure:**
- `config/plugin/<name>/app.php`: Plugin config (includes `enable` switch)
- `vendor/<name>/composer.json`: Plugin package definition
- `vendor/<name>/src/`: Plugin source directory
- Automatically adds PSR-4 mapping to project root `composer.json`
- Runs `composer dumpautoload` to refresh autoloading

**Notes:**
- Name must be `vendor/package` format: lowercase letters, numbers, `-`, `_`, `.`, and must contain one `/`
- Fails if `config/plugin/<name>` or `vendor/<name>` already exists
- Error if both argument and `--name` are provided with different values

<a name="plugin-install"></a>
### plugin:install

Execute plugin installation script (`Install::install()`), copying plugin resources to project directory.

**Usage:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin package name in format `vendor/package` (e.g. `foo/my-admin`) |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | Specify plugin name as option; use either this or argument |

**Examples:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Execute plugin uninstallation script (`Install::uninstall()`), removing plugin resources from project.

**Usage:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin package name in format `vendor/package` |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | Specify plugin name as option; use either this or argument |

**Examples:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Enable plugin, setting `enable` to `true` in `config/plugin/<name>/app.php`.

**Usage:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin package name in format `vendor/package` |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | Specify plugin name as option; use either this or argument |

**Examples:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Disable plugin, setting `enable` to `false` in `config/plugin/<name>/app.php`.

**Usage:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin package name in format `vendor/package` |

**Options:**

| Option | Description |
|--------|-------------|
| `--name` | Specify plugin name as option; use either this or argument |

**Examples:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Export plugin config and specified directories from project to `vendor/<name>/src/`, and generate `Install.php` for packaging and release.

**Usage:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Arguments:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin package name in format `vendor/package` |

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--name` | | Specify plugin name as option; use either this or argument |
| `--source` | `-s` | Path to export (relative to project root); can be specified multiple times |

**Examples:**
```bash
# Export plugin, default includes config/plugin/<name>
php webman plugin:export foo/my-admin

# Additionally export app, config, etc.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Notes:**
- Plugin name must follow Composer package naming (`vendor/package`)
- If `config/plugin/<name>` exists and is not in `--source`, it is automatically added to export list
- Exported `Install.php` includes `pathRelation` for use by `plugin:install` / `plugin:uninstall`
- `plugin:install` and `plugin:uninstall` require plugin to exist in `vendor/<name>`, with `Install` class and `WEBMAN_PLUGIN` constant

## Service Management

<a name="start"></a>
### start

Start Webman worker processes. Default DEBUG mode (foreground).

**Usage:**
```bash
php webman start
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | Start in DAEMON mode (background) |

<a name="stop"></a>
### stop

Stop Webman worker processes.

**Usage:**
```bash
php webman stop
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | Graceful stop; wait for current requests to complete before exiting |

<a name="restart"></a>
### restart

Restart Webman worker processes.

**Usage:**
```bash
php webman restart
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | Run in DAEMON mode after restart |
| `--graceful` | `-g` | Graceful stop before restart |

<a name="reload"></a>
### reload

Reload code without downtime. For hot-reload after code updates.

**Usage:**
```bash
php webman reload
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | Graceful reload; wait for current requests to complete before reloading |

<a name="status"></a>
### status

View worker process run status.

**Usage:**
```bash
php webman status
```

**Options:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--live` | `-d` | Show details (live status) |

<a name="connections"></a>
### connections

Get worker process connection info.

**Usage:**
```bash
php webman connections
```

