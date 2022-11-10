# Application Plugin
Each application plugin is a complete application with the source code placed in the `{main project}/plugin` directory

> **hint**
> can be used`php webman app-plugin:create {Plugin Name}` (needwebman/console>=1.2.16) You can create one locallyApplication Plugin，
> For example `php webman app-plugin:create cms` will create the following directory structure

```
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

with database operationsApplication Pluginhas withwebmansame directory structure andconfiguration file。actually develop oneApplication PluginDirectory copy towebmanThe project experience is basically the same，Just a few things to keep in mind。

## Namespace
Only one processPSR4norm，because the plugins are placed inplugindirectory，soNamespaceall start withpluginbeginning，example`plugin\cms\app\controller\UserController`，herecmsis the main directory of the plugin's source code。

## urlAccess
Application Pluginurland then pass it to`/app`beginning，example`plugin\cms\app\controller\UserController`urlAddress is `http://127.0.0.1:8787/app/cms/user`。

## Static File
Static FilePlaced in`plugin/{Plugin}/public`下，exampleAccess`http://127.0.0.1:8787/app/cms/avatar.png`This example passes`plugin/cms/public/avatar.png`file。

## configuration file
The configuration of a plugin is the same as a normal webman project, but the configuration of a plugin is generally only valid for the current plugin and generally has no effect on the main project。
For example the value of `plugin.cms.app.controller_suffix` only affects the plugin's controller suffix and has no effect on the main project。
For example, the value of `plugin.cms.app.controller_reuse` only affects whether the plugin reuses the controller, and has no effect on the main project。
For example, the value of `plugin.cms.middleware` only affects the plugin's middleware and has no effect on the main project。
For example, the value of `plugin.cms.view` only affects the view used by the plugin and has no effect on the main project。
For example, the value of `plugin.cms.container` only affects the container used by the plugin, and has no effect on the main project。
For example, the value of `plugin.cms.exception` only affects the plugin's exception handling class and has no effect on the main project.。

But since the routes are global, the routes configured by the plugin also affect the global。

## Get Configuration
Get a plugin configuration method as `config('plugin.{plugin}. {specific configuration}');`, for example get all configuration methods for `plugin/cms/config/app.php` as `config('plugin.cms.app')`
Similarly, the main project or other plugins can use `config('plugin.cms.xxx')` to get the configuration of the cms plugin。

## Unsupported configurations
Application PluginNot supportedserver.php，session.phpconfigure，Not supported`app.request_class`，`app.public_path`，`app.runtime_path`configure。

## Database
Plugins can configure their own database, for example `plugin/cms/config/database.php` reads as follows
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlfor connection name
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'Database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // adminfor connection name
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'Database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Reference as `Db::connection('plugin.{plugin}. {connection name}');`, for example
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

If you want to use the database of the main project, just use it directly, for example
```php
use support\Db;
Db::table('user')->first();
// Assumes that the main project also has an admin connection configured
Db::connection('admin')->table('admin')->first();
```

> **hint**
> thinkormAlso similar usage

## Redis
RedisUsage is similar to database, for example `plugin/cms/config/redis.php`
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
Use when
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Similarly, if you want to reuse the Redis configuration of the main project
```php
use support\Redis;
Redis::get('key');
// Assuming the main project is also configured with a cache connection
Redis::connection('cache')->get('key');
```

## log
Log class usage is also similar to database usage
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

If you want to reuse the logging configuration of the main project, use it directly
```php
use support\Log;
Log::info('Log Contents');
// Assume the main project has a test log configuration
Log::channel('test')->info('Log Contents');
```

# Application Plugin Installation and Uninstallation
The application plugin installation only needs to copy the plugin directory to the `{main project}/plugin` directory, it needs to reload or restart to take effect。
Delete the corresponding plugin directory under `{main project}/plugin` when uninstalling。
