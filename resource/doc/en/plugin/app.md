# Application Plugins
Each application plugin is a complete application, and the source code is placed in the `{main project}/plugin` directory.

> **Note**
> Use the command `php webman app-plugin:create {plugin_name}` (requires webman/console>=1.2.16) to create an application plugin locally. For example, `php webman app-plugin:create cms` will create the following directory structure:

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

We can see that an application plugin has the same directory structure and configuration files as webman. In fact, developing an application plugin is basically the same experience as developing a webman project, with just a few considerations.

## Namespace
The plugin directory and naming follow the PSR-4 specification. Since the plugins are placed in the plugin directory, the namespaces all start with `plugin`, for example `plugin\cms\app\controller\UserController`, where cms is the main directory of the plugin's source code.

## URL Access
The URL paths for application plugins all start with `/app`. For example, the URL for `plugin\cms\app\controller\UserController` is `http://127.0.0.1:8787/app/cms/user`.

## Static Files
Static files are placed in `plugin/{plugin}/public`. For example, accessing `http://127.0.0.1:8787/app/cms/avatar.png` actually retrieves the file `plugin/cms/public/avatar.png`.

## Configuration Files
The configuration of the plugin is similar to that of a regular webman project. However, the configuration of a plugin generally only affects the current plugin and does not affect the main project.
For example, the value of `plugin.cms.app.controller_suffix` only affects the controller suffix of the plugin, without affecting the main project. Similarly, other configurations such as `plugin.cms.app.controller_reuse`, `plugin.cms.middleware`, `plugin.cms.view`, `plugin.cms.container`, and `plugin.cms.exception` only affect the plugin without impacting the main project. However, since routes are global, the route configured for the plugin also affects the global configuration.

## Accessing Configuration
To access a specific plugin's configuration, use `config('plugin.{plugin}.{specific_configuration}')`. For example, to retrieve all configurations from `plugin/cms/config/app.php`, use `config('plugin.cms.app')`. Similarly, the main project or other plugins can use `config('plugin.cms.xxx')` to retrieve the configuration of the cms plugin.

## Unsupported Configurations
Application plugins do not support `server.php` and `session.php` configurations, and do not support `app.request_class`, `app.public_path`, and `app.runtime_path` configurations.

## Database
Plugins can configure their own databases. For example, the contents of `plugin/cms/config/database.php` are as follows:
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql is the connection name
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database_name',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin is the connection name
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database_name',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
To reference it, use `Db::connection('plugin.{plugin}.{connection_name}');`. For example:
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```
If you want to use the main project's database, simply use it as follows:
```php
use support\Db;
Db::table('user')->first();
// Assuming the main project also configures an 'admin' connection
Db::connection('admin')->table('admin')->first();
```
> **Note**
> ThinkORM has a similar usage.

## Redis
The usage of Redis is similar to that of the database. For example, in `plugin/cms/config/redis.php`:
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
To use it:
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
Similarly, if you want to reuse the main project's Redis configuration:
```php
use support\Redis;
Redis::get('key');
// Assuming the main project also configures a 'cache' connection
Redis::connection('cache')->get('key');
```

## Logging
The usage of the logging class is similar to that of the database:
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```
If you want to reuse the main project's logging configuration, just use it directly:
```php
use support\Log;
Log::info('log content');
// Assuming the main project has a 'test' log configuration
Log::channel('test')->info('log content');
```

# Installing and Uninstalling Application Plugins
To install an application plugin, simply copy the plugin directory to the `{main project}/plugin` directory, and then reload or restart for the changes to take effect. To uninstall, simply delete the corresponding plugin directory under `{main project}/plugin`.
