# Плагины приложений
Каждый плагин приложения представляет собой полноценное приложение, исходные коды которого находятся в каталоге `{главный проект}/plugin`.

> **Совет**
> С помощью команды `php webman app-plugin:create {название плагина}` (требуется webman/console>=1.2.16) можно создать локальный плагин приложения, например, `php webman app-plugin:create cms`, что создаст следующую структуру каталогов:

```plaintext
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

Мы видим, что у плагина приложения есть та же структура каталогов и файлов конфигурации, что и у webman. Фактически, разработка плагина приложения практически не отличается от разработки проекта webman, просто необходимо учитывать несколько аспектов.

## Пространство имен
Каталог и имя плагина следуют соглашению PSR4. Поскольку плагины размещаются в каталоге plugin, пространства имен начинаются с plugin, например `plugin\cms\app\controller\UserController`, где cms - это основной каталог исходного кода плагина.

## Доступ по URL
Пути URL для плагинов приложений начинаются с `/app`, например URL-адрес `plugin\cms\app\controller\UserController` будет `http://127.0.0.1:8787/app/cms/user`.

## Статические файлы
Статические файлы находятся в `plugin/{плагин}/public`. Например, для доступа к файлу `http://127.0.0.1:8787/app/cms/avatar.png` фактически загружается файл `plugin/cms/public/avatar.png`.

## Файлы конфигурации
Конфигурация плагина такая же, как у обычного проекта webman, но обычно конфигурация плагина действует только в пределах этого плагина и не влияет на основной проект.
Например, значение `plugin.cms.app.controller_suffix` влияет только на суффикс контроллера плагина, но не влияет на основной проект.
Точно так же и для других настроек, таких как `plugin.cms.app.controller_reuse`, `plugin.cms.middleware`, `plugin.cms.view`, `plugin.cms.container`, `plugin.cms.exception`.

Однако, поскольку маршруты глобальны, настройки маршрутов плагина также влияют на весь сервер.

## Получение конфигурации
Получить конфигурацию для конкретного плагина можно с помощью `config('plugin.{плагин}.{конкретная конфигурация}')`, например получение всей конфигурации `plugin/cms/config/app.php` осуществляется с помощью `config('plugin.cms.app')`.
Точно так же, конфигурацию плагина cms могут использовать и основной проект, или другие плагины, с помощью `config('plugin.cms.xxx')`.

## Неподдерживаемые настройки
Плагины приложений не поддерживают server.php, session.php конфигурации, не поддерживают настройки `app.request_class`, `app.public_path`, `app.runtime_path`.

## База данных
Плагины могут иметь собственную базу данных, например содержимое `plugin/cms/config/database.php` следующее:
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql - имя соединения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'база данных',
            'username'    => 'имя пользователя',
            'password'    => 'пароль',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin - имя соединения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'база данных',
            'username'    => 'имя пользователя',
            'password'    => 'пароль',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Использование: `Db::connection('plugin.{плагин}.{имя соединения}');`, например
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```
Если нужно использовать базу данных основного проекта, просто используйте:
```php
use support\Db;
Db::table('user')->first();
// Предположим, что в основном проекте также настроено соединение admin
Db::connection('admin')->table('admin')->first();
```

> **Совет**
> Аналогично работает и thinkorm.

## Redis
Использование Redis аналогично использованию базы данных, например для `plugin/cms/config/redis.php`:
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
Использование:
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
Точно так же, чтобы использовать конфигурацию Redis основного проекта:
```php
use support\Redis;
Redis::get('key');
// Предположим, что основной проект также настроил соединение cache
Redis::connection('cache')->get('key');
```

## Журнал
Использование журнала подобно использованию базы данных:
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```
Чтобы использовать конфигурацию журнала основного проекта, просто используйте:
```php
use support\Log;
Log::info('содержание журнала');
// Предположим, что в основном проекте есть конфигурация журнала test
Log::channel('test')->info('содержание журнала');
```

# Установка и удаление плагинов приложений
Установка плагинов приложений происходит путем копирования каталога плагина в каталог `{главный проект}/plugin`, после чего необходимо выполнить reload или restart для активации.
Для удаления просто удалите каталог плагина из каталога `{главный проект}/plugin`.
