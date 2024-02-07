# 应用插件
每个应用插件是一个完整的应用，源码放置于`{主项目}/plugin`目录下

> **提示**
> 使用命令`php webman app-plugin:create {插件名}` (需要webman/console>=1.2.16) 可以在本地创建一个应用插件，
> 例如 `php webman app-plugin:create cms` 将创建如下目录结构

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

我们看到一个应用插件有着与webman相同的目录结构及配置文件。实际上开发一个应用插件与开发一个webman项目体验基本一致，只需要注意以下几个方面。

## 命名空间
插件目录及命名遵循PSR4规范，因为插件都放置于plugin目录下，所以命名空间都以plugin开头，例如`plugin\cms\app\controller\UserController`，这里cms是插件的源码主目录。

## url访问
应用插件url地址路径都以`/app`开头，例如`plugin\cms\app\controller\UserController`url地址是 `http://127.0.0.1:8787/app/cms/user`。

## 静态文件
静态文件放置于`plugin/{插件}/public`下，例如访问`http://127.0.0.1:8787/app/cms/avatar.png`实际上是获取`plugin/cms/public/avatar.png`文件。

## 配置文件
插件的配置与普通webman项目一样，不过插件的配置一般只对当前插件有效，对主项目一般无影响。
例如`plugin.cms.app.controller_suffix`的值只影响插件的控制器后缀，对主项目没有影响。
例如`plugin.cms.app.controller_reuse`的值只影响插件是否复用控制器，对主项目没有影响。
例如`plugin.cms.middleware`的值只影响插件的中间件，对主项目没有影响。
例如`plugin.cms.view`的值只影响插件所使用的视图，对主项目没有影响。
例如`plugin.cms.container`的值只影响插件所使用的容器，对主项目没有影响。
例如`plugin.cms.exception`的值只影响插件的异常处理类，对主项目没有影响。

但是因为路由是全局的，所以插件配置的路由也是影响全局的。

## 获取配置
获取某个插件配置方法为 `config('plugin.{插件}.{具体的配置}');`，例如获取`plugin/cms/config/app.php`的所有配置方法为`config('plugin.cms.app')`
同样的，主项目或者其它插件都可以用`config('plugin.cms.xxx')`来获取cms插件的配置。

## 不支持的配置
应用插件不支持server.php，session.php配置，不支持`app.request_class`，`app.public_path`，`app.runtime_path`配置。

## 数据库
插件可以配置自己的数据库，例如`plugin/cms/config/database.php`内容如下
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql为连接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '数据库',
            'username'    => '用户名',
            'password'    => '密码',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin为连接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '数据库',
            'username'    => '用户名',
            'password'    => '密码',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
引用方式为`Db::connection('plugin.{插件}.{连接名}');`，例如
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

如果想使用主项目的数据库，则直接使用即可，例如
```php
use support\Db;
Db::table('user')->first();
// 假设主项目还配置了一个admin连接
Db::connection('admin')->table('admin')->first();
```

> **提示**
> thinkorm也是类似的用法

## Redis
Redis用法与数据库类似，例如 `plugin/cms/config/redis.php`
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
使用时
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

同样的，如果想复用主项目的Redis配置
```php
use support\Redis;
Redis::get('key');
// 假设主项目还配置了一个cache连接
Redis::connection('cache')->get('key');
```

## 日志
日志类用法也与数据库用法类似
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

如果想复用主项目的日志配置，直接使用
```php
use support\Log;
Log::info('日志内容');
// 假设主项目有个test日志配置
Log::channel('test')->info('日志内容');
```

# 应用插件安装与卸载
应用插件安装时只需要将插件目录拷贝到`{主项目}/plugin`目录下即可，需要reload或restart才能生效。
卸载时直接删除`{主项目}/plugin`下对应的插件目录即可。
