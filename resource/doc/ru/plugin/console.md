# Командная строка

Компонент командной строки Webman

## Установка
```
composer require webman/console
```

## Содержание

### Генерация кода
- [make:controller](#make-controller) — Генерация класса контроллера
- [make:model](#make-model) — Генерация класса модели из таблицы БД
- [make:crud](#make-crud) — Генерация полного CRUD (модель + контроллер + валидатор)
- [make:middleware](#make-middleware) — Генерация класса промежуточного ПО
- [make:command](#make-command) — Генерация класса консольной команды
- [make:bootstrap](#make-bootstrap) — Генерация класса инициализации при запуске
- [make:process](#make-process) — Генерация класса пользовательского процесса

### Сборка и развёртывание
- [build:phar](#build-phar) — Упаковка проекта в PHAR-архив
- [build:bin](#build-bin) — Упаковка проекта в автономный исполняемый файл
- [install](#install) — Запуск скрипта установки Webman

### Служебные команды
- [version](#version) — Отображение версии фреймворка Webman
- [fix-disable-functions](#fix-disable-functions) — Исправление отключённых функций в php.ini
- [route:list](#route-list) — Отображение всех зарегистрированных маршрутов

### Управление прикладными плагинами (app-plugin:*)
- [app-plugin:create](#app-plugin-create) — Создание нового прикладного плагина
- [app-plugin:install](#app-plugin-install) — Установка прикладного плагина
- [app-plugin:uninstall](#app-plugin-uninstall) — Удаление прикладного плагина
- [app-plugin:update](#app-plugin-update) — Обновление прикладного плагина
- [app-plugin:zip](#app-plugin-zip) — Упаковка прикладного плагина в ZIP

### Управление плагинами (plugin:*)
- [plugin:create](#plugin-create) — Создание нового плагина Webman
- [plugin:install](#plugin-install) — Установка плагина Webman
- [plugin:uninstall](#plugin-uninstall) — Удаление плагина Webman
- [plugin:enable](#plugin-enable) — Включение плагина Webman
- [plugin:disable](#plugin-disable) — Отключение плагина Webman
- [plugin:export](#plugin-export) — Экспорт исходного кода плагина

### Управление сервисом
- [start](#start) — Запуск рабочих процессов Webman
- [stop](#stop) — Остановка рабочих процессов Webman
- [restart](#restart) — Перезапуск рабочих процессов Webman
- [reload](#reload) — Перезагрузка кода без простоя
- [status](#status) — Просмотр статуса рабочих процессов
- [connections](#connections) — Получение информации о подключениях рабочих процессов

## Генерация кода

<a name="make-controller"></a>
### make:controller

Генерация класса контроллера.

**Использование:**
```bash
php webman make:controller <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя контроллера (без суффикса) |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--plugin` | `-p` | Генерация контроллера в указанной директории плагина |
| `--path` | `-P` | Пользовательский путь к контроллеру |
| `--force` | `-f` | Перезаписать при существовании файла |
| `--no-suffix` | | Не добавлять суффикс «Controller» |

**Примеры:**
```bash
# Создать UserController в app/controller
php webman make:controller User

# Создать в плагине
php webman make:controller AdminUser -p admin

# Пользовательский путь
php webman make:controller User -P app/api/controller

# Перезаписать существующий файл
php webman make:controller User -f

# Создать без суффикса «Controller»
php webman make:controller UserHandler --no-suffix
```

**Структура генерируемого файла:**
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

**Примечания:**
- По умолчанию контроллеры размещаются в `app/controller/`
- Суффикс контроллера из конфигурации добавляется автоматически
- При существовании файла запрашивается подтверждение перезаписи (аналогично для других команд)

<a name="make-model"></a>
### make:model

Генерация класса модели из таблицы базы данных. Поддерживаются Laravel ORM и ThinkORM.

**Использование:**
```bash
php webman make:model [name]
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Нет | Имя класса модели, может быть опущено в интерактивном режиме |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--plugin` | `-p` | Генерация модели в указанной директории плагина |
| `--path` | `-P` | Целевая директория (относительно корня проекта) |
| `--table` | `-t` | Указание имени таблицы; рекомендуется, когда имя таблицы не соответствует соглашению |
| `--orm` | `-o` | Выбор ORM: `laravel` или `thinkorm` |
| `--database` | `-d` | Указание имени подключения к БД |
| `--force` | `-f` | Перезаписать существующий файл |

**Примечания по путям:**
- По умолчанию: `app/model/` (основное приложение) или `plugin/<plugin>/app/model/` (плагин)
- `--path` указывается относительно корня проекта, например `plugin/admin/app/model`
- При одновременном использовании `--plugin` и `--path` они должны указывать на одну и ту же директорию

**Примеры:**
```bash
# Создать модель User в app/model
php webman make:model User

# Указать имя таблицы и ORM
php webman make:model User -t wa_users -o laravel

# Создать в плагине
php webman make:model AdminUser -p admin

# Пользовательский путь
php webman make:model User -P plugin/admin/app/model
```

**Интерактивный режим:** При отсутствии имени запускается интерактивный процесс: выбор таблицы → ввод имени модели → ввод пути. Поддерживается: Enter для просмотра дополнительных записей, `0` для создания пустой модели, `/ключевое_слово` для фильтрации таблиц.

**Структура генерируемого файла:**
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

Аннотации `@property` генерируются автоматически на основе структуры таблицы. Поддерживаются MySQL и PostgreSQL.

<a name="make-crud"></a>
### make:crud

Генерация модели, контроллера и валидатора из таблицы БД за один шаг, формируя полную CRUD-функциональность.

**Использование:**
```bash
php webman make:crud
```

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--table` | `-t` | Указание имени таблицы |
| `--model` | `-m` | Имя класса модели |
| `--model-path` | `-M` | Директория модели (относительно корня проекта) |
| `--controller` | `-c` | Имя класса контроллера |
| `--controller-path` | `-C` | Директория контроллера |
| `--validator` | | Имя класса валидатора (требуется `webman/validation`) |
| `--validator-path` | | Директория валидатора (требуется `webman/validation`) |
| `--plugin` | `-p` | Генерация файлов в указанной директории плагина |
| `--orm` | `-o` | ORM: `laravel` или `thinkorm` |
| `--database` | `-d` | Имя подключения к БД |
| `--force` | `-f` | Перезаписать существующие файлы |
| `--no-validator` | | Не генерировать валидатор |
| `--no-interaction` | `-n` | Неинтерактивный режим, использование значений по умолчанию |

**Порядок выполнения:** При неуказанном `--table` запускается интерактивный выбор таблицы; имя модели по умолчанию выводится из имени таблицы; имя контроллера по умолчанию — имя модели + суффикс контроллера; имя валидатора по умолчанию — имя контроллера без суффикса + `Validator`. Пути по умолчанию: модель `app/model/`, контроллер `app/controller/`, валидатор `app/validation/`; для плагинов — соответствующие поддиректории в `plugin/<plugin>/app/`.

**Примеры:**
```bash
# Интерактивная генерация (пошаговое подтверждение после выбора таблицы)
php webman make:crud

# Указать имя таблицы
php webman make:crud --table=users

# Указать имя таблицы и плагин
php webman make:crud --table=users --plugin=admin

# Указать пути
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Не генерировать валидатор
php webman make:crud --table=users --no-validator

# Неинтерактивный режим + перезапись
php webman make:crud --table=users --no-interaction --force
```

**Структура генерируемых файлов:**

Модель (`app/model/User.php`):
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

Контроллер (`app/controller/UserController.php`):
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

Валидатор (`app/validation/UserValidator.php`):
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
        'id' => 'Первичный ключ',
        'username' => 'Имя пользователя'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Примечания:**
- Генерация валидатора пропускается, если `webman/validation` не установлен или не включён (установка: `composer require webman/validation`)
- Атрибуты `attributes` валидатора генерируются автоматически из комментариев полей БД; при отсутствии комментариев `attributes` не создаётся
- Сообщения об ошибках валидатора поддерживают интернационализацию; язык выбирается из `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

Генерация класса промежуточного ПО и автоматическая регистрация в `config/middleware.php` (или `plugin/<plugin>/config/middleware.php` для плагинов).

**Использование:**
```bash
php webman make:middleware <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя промежуточного ПО |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--plugin` | `-p` | Генерация промежуточного ПО в указанной директории плагина |
| `--path` | `-P` | Целевая директория (относительно корня проекта) |
| `--force` | `-f` | Перезаписать существующий файл |

**Примеры:**
```bash
# Создать промежуточное ПО Auth в app/middleware
php webman make:middleware Auth

# Создать в плагине
php webman make:middleware Auth -p admin

# Пользовательский путь
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Структура генерируемого файла:**
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

**Примечания:**
- По умолчанию размещается в `app/middleware/`
- Имя класса автоматически добавляется в конфигурационный файл промежуточного ПО для активации

<a name="make-command"></a>
### make:command

Генерация класса консольной команды.

**Использование:**
```bash
php webman make:command <command-name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `command-name` | Да | Имя команды в формате `group:action` (например, `user:list`) |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--plugin` | `-p` | Генерация команды в указанной директории плагина |
| `--path` | `-P` | Целевая директория (относительно корня проекта) |
| `--force` | `-f` | Перезаписать существующий файл |

**Примеры:**
```bash
# Создать команду user:list в app/command
php webman make:command user:list

# Создать в плагине
php webman make:command user:list -p admin

# Пользовательский путь
php webman make:command user:list -P plugin/admin/app/command

# Перезаписать существующий файл
php webman make:command user:list -f
```

**Структура генерируемого файла:**
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

**Примечания:**
- По умолчанию размещается в `app/command/`

<a name="make-bootstrap"></a>
### make:bootstrap

Генерация класса инициализации при запуске. Метод `start` вызывается автоматически при запуске процесса, обычно для глобальной инициализации.

**Использование:**
```bash
php webman make:bootstrap <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя класса Bootstrap |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--plugin` | `-p` | Генерация в указанной директории плагина |
| `--path` | `-P` | Целевая директория (относительно корня проекта) |
| `--force` | `-f` | Перезаписать существующий файл |

**Примеры:**
```bash
# Создать MyBootstrap в app/bootstrap
php webman make:bootstrap MyBootstrap

# Создать без автоматического включения
php webman make:bootstrap MyBootstrap no

# Создать в плагине
php webman make:bootstrap MyBootstrap -p admin

# Пользовательский путь
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Перезаписать существующий файл
php webman make:bootstrap MyBootstrap -f
```

**Структура генерируемого файла:**
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

**Примечания:**
- По умолчанию размещается в `app/bootstrap/`
- При включении класс добавляется в `config/bootstrap.php` (или `plugin/<plugin>/config/bootstrap.php` для плагинов)

<a name="make-process"></a>
### make:process

Генерация класса пользовательского процесса и запись в `config/process.php` для автоматического запуска.

**Использование:**
```bash
php webman make:process <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя класса процесса (например, MyTcp, MyWebsocket) |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--plugin` | `-p` | Генерация в указанной директории плагина |
| `--path` | `-P` | Целевая директория (относительно корня проекта) |
| `--force` | `-f` | Перезаписать существующий файл |

**Примеры:**
```bash
# Создать в app/process
php webman make:process MyTcp

# Создать в плагине
php webman make:process MyProcess -p admin

# Пользовательский путь
php webman make:process MyProcess -P plugin/admin/app/process

# Перезаписать существующий файл
php webman make:process MyProcess -f
```

**Интерактивный процесс:** Последовательно запрашивается: прослушивать порт? → тип протокола (websocket/http/tcp/udp/unixsocket) → адрес прослушивания (IP:порт или путь unix socket) → количество процессов. Для протокола HTTP дополнительно запрашивается встроенный или пользовательский режим.

**Структура генерируемого файла:**

Непрослушивающий процесс (только `onWorkerStart`):
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

Процессы с прослушиванием TCP/WebSocket генерируют соответствующие шаблоны обратных вызовов `onConnect`, `onMessage`, `onClose`.

**Примечания:**
- По умолчанию размещается в `app/process/`; конфигурация процесса записывается в `config/process.php`
- Ключ конфигурации — snake_case от имени класса; при уже существующем ключе выполнение завершается с ошибкой
- Встроенный режим HTTP использует файл процесса `app\process\Http`, новый файл не создаётся
- Поддерживаемые протоколы: websocket, http, tcp, udp, unixsocket

## Сборка и развёртывание

<a name="build-phar"></a>
### build:phar

Упаковка проекта в PHAR-архив для распространения и развёртывания.

**Использование:**
```bash
php webman build:phar
```

**Запуск:**

Перейдите в директорию сборки и выполните

```bash
php webman.phar start
```

**Примечания:**
* Упакованный проект не поддерживает reload; для обновления кода используйте restart

* Чтобы избежать большого размера файла и потребления памяти, настройте exclude_pattern и exclude_files в config/plugin/webman/console/app.php для исключения ненужных файлов.

* При запуске webman.phar в том же каталоге создаётся директория runtime для логов и временных файлов.

* Если проект использует файл .env, поместите .env в тот же каталог, что и webman.phar.

* webman.phar не поддерживает пользовательские процессы в Windows

* Никогда не храните загруженные пользователем файлы внутри phar-пакета; работа с пользовательскими загрузками через phar:// опасна (уязвимость десериализации phar). Загруженные пользователем файлы должны храниться отдельно на диске вне phar. См. ниже.

* Если требуется загрузка файлов в публичную директорию, извлеките публичную директорию в тот же каталог, что и webman.phar, и настройте config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Используйте вспомогательную функцию public_path($relative_path) для получения фактического пути к публичной директории.


<a name="build-bin"></a>
### build:bin

Упаковка проекта в автономный исполняемый файл со встроенной средой выполнения PHP. Установка PHP в целевой среде не требуется.

**Использование:**
```bash
php webman build:bin [version]
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `version` | Нет | Версия PHP (например, 8.1, 8.2), по умолчанию текущая версия PHP, минимум 8.1 |

**Примеры:**
```bash
# Использовать текущую версию PHP
php webman build:bin

# Указать PHP 8.2
php webman build:bin 8.2
```

**Запуск:**

Перейдите в директорию сборки и выполните

```bash
./webman.bin start
```

**Примечания:**
* Настоятельно рекомендуется: локальная версия PHP должна совпадать с версией сборки (например, локально PHP 8.1 → сборка с 8.1) во избежание проблем совместимости
* Сборка загружает исходный код PHP 8, но не устанавливает его локально; локальная среда PHP не затрагивается
* webman.bin в настоящее время работает только на x86_64 Linux; на macOS не поддерживается
* Упакованный проект не поддерживает reload; для обновления кода используйте restart
* .env по умолчанию не упаковывается (управляется exclude_files в config/plugin/webman/console/app.php); поместите .env в тот же каталог, что и webman.bin, при запуске
* В каталоге webman.bin создаётся директория runtime для файлов логов
* webman.bin не читает внешний php.ini; для пользовательских настроек php.ini используйте custom_ini в config/plugin/webman/console/app.php
* Исключите ненужные файлы через config/plugin/webman/console/app.php, чтобы избежать большого размера пакета
* Сборка в бинарный файл не поддерживает корутины Swoole
* Никогда не храните загруженные пользователем файлы внутри бинарного пакета; работа через phar:// опасна (уязвимость десериализации phar). Загруженные пользователем файлы должны храниться отдельно на диске вне пакета.
* Если требуется загрузка файлов в публичную директорию, извлеките публичную директорию в тот же каталог, что и webman.bin, и настройте config/app.php как указано ниже, затем пересоберите:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Выполнение скрипта установки фреймворка Webman (вызов `\Webman\Install::install()`), для инициализации проекта.

**Использование:**
```bash
php webman install
```

## Служебные команды

<a name="version"></a>
### version

Отображение версии workerman/webman-framework.

**Использование:**
```bash
php webman version
```

**Примечания:** Версия читается из `vendor/composer/installed.php`; при невозможности чтения возвращается ошибка.

<a name="fix-disable-functions"></a>
### fix-disable-functions

Исправление `disable_functions` в php.ini, удаление функций, необходимых для работы Webman.

**Использование:**
```bash
php webman fix-disable-functions
```

**Примечания:** Удаляет следующие функции (и совпадения по префиксу) из `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Пропускается, если php.ini не найден или `disable_functions` пуст. **Напрямую изменяет файл php.ini**; рекомендуется предварительная резервная копия.

<a name="route-list"></a>
### route:list

Отображение всех зарегистрированных маршрутов в табличном формате.

**Использование:**
```bash
php webman route:list
```

**Пример вывода:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Столбцы вывода:** URI, Method, Callback, Middleware, Name. Обратные вызовы-замыкания отображаются как «Closure».

## Управление прикладными плагинами (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Создание нового прикладного плагина с полной структурой директорий и базовыми файлами в `plugin/<name>`.

**Использование:**
```bash
php webman app-plugin:create <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя плагина; должно соответствовать `[a-zA-Z0-9][a-zA-Z0-9_-]*`, не может содержать `/` или `\` |

**Примеры:**
```bash
# Создать прикладной плагин с именем foo
php webman app-plugin:create foo

# Создать плагин с дефисом
php webman app-plugin:create my-app
```

**Структура генерируемых директорий:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php и т.д.
├── api/Install.php  # Хуки установки/удаления/обновления
├── public/
└── install.sql
```

**Примечания:**
- Плагин создаётся в `plugin/<name>/`; при уже существующей директории выполнение завершается с ошибкой

<a name="app-plugin-install"></a>
### app-plugin:install

Установка прикладного плагина, выполнение `plugin/<name>/api/Install::install($version)`.

**Использование:**
```bash
php webman app-plugin:install <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя плагина; должно соответствовать `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Примеры:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Удаление прикладного плагина, выполнение `plugin/<name>/api/Install::uninstall($version)`.

**Использование:**
```bash
php webman app-plugin:uninstall <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя плагина |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--yes` | `-y` | Пропустить подтверждение, выполнить напрямую |

**Примеры:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Обновление прикладного плагина, последовательное выполнение `Install::beforeUpdate($from, $to)` и `Install::update($from, $to, $context)`.

**Использование:**
```bash
php webman app-plugin:update <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя плагина |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--from` | `-f` | Исходная версия, по умолчанию текущая версия |
| `--to` | `-t` | Целевая версия, по умолчанию текущая версия |

**Примеры:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Упаковка прикладного плагина в ZIP-файл, вывод в `plugin/<name>.zip`.

**Использование:**
```bash
php webman app-plugin:zip <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя плагина |

**Примеры:**
```bash
php webman app-plugin:zip foo
```

**Примечания:**
- Автоматически исключаются `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__` и т.д.

## Управление плагинами (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Создание нового плагина Webman (в форме пакета Composer), генерация конфигурационной директории `config/plugin/<name>` и директории исходного кода плагина `vendor/<name>`.

**Использование:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя пакета плагина в формате `vendor/package` (например, `foo/my-admin`); должно соответствовать соглашению об именовании пакетов Composer |

**Примеры:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Генерируемая структура:**
- `config/plugin/<name>/app.php`: конфигурация плагина (включая переключатель `enable`)
- `vendor/<name>/composer.json`: определение пакета плагина
- `vendor/<name>/src/`: директория исходного кода плагина
- Автоматическое добавление PSR-4-маппинга в корневой `composer.json` проекта
- Выполнение `composer dumpautoload` для обновления автозагрузки

**Примечания:**
- Имя должно быть в формате `vendor/package`: строчные буквы, цифры, `-`, `_`, `.`, и обязательно содержать один `/`
- При уже существующих `config/plugin/<name>` или `vendor/<name>` выполнение завершается с ошибкой
- Ошибка при одновременной передаче аргумента и `--name` с разными значениями

<a name="plugin-install"></a>
### plugin:install

Выполнение скрипта установки плагина (`Install::install()`), копирование ресурсов плагина в директорию проекта.

**Использование:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя пакета плагина в формате `vendor/package` (например, `foo/my-admin`) |

**Опции:**

| Опция | Описание |
|--------|-------------|
| `--name` | Указать имя плагина в виде опции; использовать либо её, либо аргумент |

**Примеры:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Выполнение скрипта удаления плагина (`Install::uninstall()`), удаление ресурсов плагина из проекта.

**Использование:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя пакета плагина в формате `vendor/package` |

**Опции:**

| Опция | Описание |
|--------|-------------|
| `--name` | Указать имя плагина в виде опции; использовать либо её, либо аргумент |

**Примеры:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Включение плагина, установка `enable` в `true` в `config/plugin/<name>/app.php`.

**Использование:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя пакета плагина в формате `vendor/package` |

**Опции:**

| Опция | Описание |
|--------|-------------|
| `--name` | Указать имя плагина в виде опции; использовать либо её, либо аргумент |

**Примеры:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Отключение плагина, установка `enable` в `false` в `config/plugin/<name>/app.php`.

**Использование:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя пакета плагина в формате `vendor/package` |

**Опции:**

| Опция | Описание |
|--------|-------------|
| `--name` | Указать имя плагина в виде опции; использовать либо её, либо аргумент |

**Примеры:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Экспорт конфигурации плагина и указанных директорий из проекта в `vendor/<name>/src/` с генерацией `Install.php` для упаковки и распространения.

**Использование:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Аргументы:**

| Аргумент | Обязательный | Описание |
|----------|----------|-------------|
| `name` | Да | Имя пакета плагина в формате `vendor/package` |

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--name` | | Указать имя плагина в виде опции; использовать либо её, либо аргумент |
| `--source` | `-s` | Путь для экспорта (относительно корня проекта); может указываться несколько раз |

**Примеры:**
```bash
# Экспорт плагина, по умолчанию включает config/plugin/<name>
php webman plugin:export foo/my-admin

# Дополнительный экспорт app, config и т.д.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Примечания:**
- Имя плагина должно соответствовать соглашению об именовании пакетов Composer (`vendor/package`)
- Если `config/plugin/<name>` существует и не указан в `--source`, он автоматически добавляется в список экспорта
- Экспортируемый `Install.php` содержит `pathRelation` для использования в `plugin:install` / `plugin:uninstall`
- Для `plugin:install` и `plugin:uninstall` требуется наличие плагина в `vendor/<name>`, класса `Install` и константы `WEBMAN_PLUGIN`

## Управление сервисом

<a name="start"></a>
### start

Запуск рабочих процессов Webman. По умолчанию режим DEBUG (передний план).

**Использование:**
```bash
php webman start
```

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--daemon` | `-d` | Запуск в режиме DAEMON (фоновый режим) |

<a name="stop"></a>
### stop

Остановка рабочих процессов Webman.

**Использование:**
```bash
php webman stop
```

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--graceful` | `-g` | Плавная остановка; ожидание завершения текущих запросов перед выходом |

<a name="restart"></a>
### restart

Перезапуск рабочих процессов Webman.

**Использование:**
```bash
php webman restart
```

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--daemon` | `-d` | Режим DAEMON после перезапуска |
| `--graceful` | `-g` | Плавная остановка перед перезапуском |

<a name="reload"></a>
### reload

Перезагрузка кода без простоя. Для горячей перезагрузки после обновления кода.

**Использование:**
```bash
php webman reload
```

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--graceful` | `-g` | Плавная перезагрузка; ожидание завершения текущих запросов перед перезагрузкой |

<a name="status"></a>
### status

Просмотр статуса работы рабочих процессов.

**Использование:**
```bash
php webman status
```

**Опции:**

| Опция | Сокращение | Описание |
|--------|----------|-------------|
| `--live` | `-d` | Показать подробности (текущий статус) |

<a name="connections"></a>
### connections

Получение информации о подключениях рабочих процессов.

**Использование:**
```bash
php webman connections
```
