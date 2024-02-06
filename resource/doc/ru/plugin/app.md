# Плагины приложения
Каждый плагин приложения является полноценным приложением, исходный код которого находится в каталоге `{основной проект}/plugin`

> **Совет**
> Используя команду `php webman app-plugin:create {имя плагина}` (требуется webman/console>=1.2.16), можно создать локальный плагин приложения,
> например, `php webman app-plugin:create cms` создаст следующую структуру каталогов

```
plugin/
└── cms
    ├── app
    │   ├── контроллер
    │   │   └── IndexController.php
    │   ├── исключение
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── промежуточное ПО
    │   ├── модель
    │   └── вид
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

Мы видим, что у плагина приложения имеется та же структура каталогов и файлов конфигурации, что и у webman. Фактически, разработка плагина приложения и разработка проекта веб-приложения в целом имеют в общем одинаковый опыт, необходимо обратить внимание только на несколько аспектов.

## Пространства имен
Каталог и именование плагинов следуют стандарту PSR4. Поскольку все плагины находятся в каталоге plugin, пространства имен начинаются с plugin, например `plugin\cms\app\controller\UserController`, где cms - это основной каталог исходного кода плагина.

## URL-адреса доступа
Пути адресов URL плагина приложения начинаются с `/app`, например URL-адрес для `plugin\cms\app\controller\UserController` - `http://127.0.0.1:8787/app/cms/user`.

## Статические файлы
Статические файлы находятся в `plugin/{плагин}/public`. Например, обращение к `http://127.0.0.1:8787/app/cms/avatar.png` фактически означает получение файла `plugin/cms/public/avatar.png`.

## Файлы конфигурации
Настройки плагина аналогичны настройкам обычного проекта webman, однако обычно настройки плагина применяются только к текущему плагину и не влияют на основной проект.
Например, значение `plugin.cms.app.controller_suffix` влияет только на суффикс контроллера плагина, но не влияет на основной проект.
То же самое относится и к другим настройкам, таким как `plugin.cms.app.controller_reuse`, `plugin.cms.middleware`, `plugin.cms.view`, `plugin.cms.container`, `plugin.cms.exception`.

Однако так как маршруты являются глобальными, конфигурация маршрутов плагина также влияет на всю систему.

## Получение конфигурации
Для получения конфигурации конкретного плагина используется метод `config('plugin.{плагин}.{конкретная настройка}')`, например, получение всех настроек `plugin/cms/config/app.php` осуществляется с помощью `config('plugin.cms.app')`. 
Точно так же, основной проект или другие плагины могут использовать `config('plugin.cms.xxx')` для получения настроек плагина cms.

## Неподдерживаемые конфигурации
Плагины приложения не поддерживают server.php, session.php и настройки `app.request_class`, `app.public_path`, `app.runtime_path`.

## База данных
Плагины могут настраивать свою собственную базу данных, например содержимое `plugin/cms/config/database.php` может выглядеть следующим образом:
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql - это имя соединения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin - это имя соединения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
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
Если вы хотите использовать базу данных основного проекта, то используйте напрямую, например
```php
use support\Db;
Db::table('user')->first();
// Предположим, что в основном проекте также есть соединение admin
Db::connection('admin')->table('admin')->first();
```

> **Совет**
> Подобным образом можно использовать thinkorm.

## Redis
Использование Redis аналогично использованию базы данных, например, `plugin/cms/config/redis.php`:
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
Точно так же, если вы хотите использовать настройки Redis основного проекта, то используйте напрямую, например
```php
use support\Redis;
Redis::get('key');
// Предположим, что в основном проекте также есть соединение cache
Redis::connection('cache')->get('key');
```

## Логирование
Использование логирования аналогично использованию базы данных
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```
Если вы хотите использовать настройки журналирования основного проекта, просто используйте их напрямую
```php
use support\Log;
Log::info('содержание журнала');
// Предположим, что в основном проекте есть настройка журналирования test
Log::channel('test')->info('содержание журнала');
```

# Установка и удаление плагинов приложения
Для установки плагинов приложения достаточно скопировать каталог плагина в каталог `{основной проект}/plugin`, но требуется выполнить reload или restart для активации.
Для удаления просто удалите соответствующий каталог плагина в `{основной проект}/plugin`.
