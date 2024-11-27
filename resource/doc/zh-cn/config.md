# 配置文件

## 位置
webman的配置文件在`config/`目录下，项目中可以通过`config()`函数来获取对应的配置。

## 获取配置

获取所有配置
```php
config();
```

获取`config/app.php`里的所有配置
```php
config('app');
```

获取`config/app.php`里的`debug`配置
```php
config('app.debug');
```

如果配置是数组，可以通过`.`来获取数组内部元素的值，例如
```php
config('file.key1.key2');
```

## 默认值
```php
config($key, $default);
```
config通过第二个参数传递默认值，如果配置不存在则返回默认值。
配置不存在且没有设置默认值则返回null。


## 自定义配置
开发者可以在`config/`目录下添加自己的配置文件，例如

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**获取配置时使用**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## 更改配置
webman不支持动态修改配置，所有配置必须手动修改对应的配置文件，并reload或restart重启

> **注意**
> 服务器配置`config/server.php`以及进程配置`config/process.php`不支持reload，需要restart重启才能生效

## 特别提醒
如果你是要在config下的子目录创建配置文件并读取，比如：`config/order/status.php`，那么`config/order`目录下需要有一个`app.php`文件，内容如下
```php
<?php
return [
    'enable' => true,
];
```
`enable`为`true`代表让框架读取这个目录的配置。
最终配置文件目录树类似下面这样
```
├── config
│   ├── order
│   │   ├── app.php
│   │   └── status.php
```
这样你就可以通过`config.order.status`读取`status.php`中返回的数组或者特定的key数据了。


## 配置文件讲解

#### server.php
```php
return [
    'listen' => 'http://0.0.0.0:8787', // 监听端口(从1.6.0版本开始移除, 改在config/process.php中配置)
    'transport' => 'tcp', // 传输层协议(从1.6.0版本开始移除, 改在config/process.php中配置)
    'context' => [], // ssl等配置(从1.6.0版本开始移除, 改在config/process.php中配置)
    'name' => 'webman', // 进程名(从1.6.0版本开始移除, 改在config/process.php中配置)
    'count' => cpu_count() * 4, // 进程数量(从1.6.0版本开始移除, 改在config/process.php中配置)
    'user' => '', // 用户(从1.6.0版本开始移除, 改在config/process.php中配置)
    'group' => '', // 用户组(从1.6.0版本开始移除, 改在config/process.php中配置)
    'reusePort' => false, // 是否开启端口复用(从1.6.0版本开始移除, 改在config/process.php中配置)
    'event_loop' => '',  // 事件循环类，默认自动选择
    'stop_timeout' => 2, // 收到stop/restart/reload信号时，等待处理完成的最大时间，超过这个时间进程未退出则强制退出
    'pid_file' => runtime_path() . '/webman.pid', // pid文件存储位置
    'status_file' => runtime_path() . '/webman.status', // status文件存储位置
    'stdout_file' => runtime_path() . '/logs/stdout.log', // 标准输出文件位置，webman启动后所有输出都会写入这个文件
    'log_file' => runtime_path() . '/logs/workerman.log', // workerman日志文件位置
    'max_package_size' => 10 * 1024 * 1024 // 最大数据包大小，10M。上传文件大小受到此限制
];
```

#### app.php
```php
return [
    'debug' => true,  // 是否开启debug模式，开启后页面报错会输出更多调试信息
    'error_reporting' => E_ALL, // 错误报告级别
    'default_timezone' => 'Asia/Shanghai', // 默认时区
    'public_path' => base_path() . DIRECTORY_SEPARATOR . 'public', // public目录位置
    'runtime_path' => base_path(false) . DIRECTORY_SEPARATOR . 'runtime', // runtime目录位置
    'controller_suffix' => 'Controller', // 控制器后缀
    'controller_reuse' => false, // 控制器是否复用
];
```

#### process.php
```php
use support\Log;
use support\Request;
use app\process\Http;
global $argv;

return [
     // webman进程配置
    'webman' => [ 
        'handler' => Http::class, // 进程处理类
        'listen' => 'http://0.0.0.0:8787', // 监听地址
        'count' => cpu_count() * 4, // 进程数量，默认cpu的4倍
        'user' => '', // 进程运行的用户，应该使用低级别用户
        'group' => '', // 进程运行的用户组，应该使用低级别用户组
        'reusePort' => false, // 是否开启reusePort，开启后连接会均匀分布到不同的worker进程
        'eventLoop' => '', // 事件循环类，为空时自动使用server.event_loop配置 
        'context' => [], // 监听上下文配置，例如ssl
        'constructor' => [ // 进程处理类构造函数参数，本例中是Http类的构造函数参数
            'requestClass' => Request::class, // 可以自定义请求类
            'logger' => Log::channel('default'), // 日志实例
            'appPath' => app_path(), // app目录位置
            'publicPath' => public_path() // public目录位置
        ]
    ],
    // 监控进程，用于检测文件更新自动加载和内存泄漏
    'monitor' => [
        'handler' => app\process\Monitor::class, // 处理类
        'reloadable' => false, // 当前进程不执行reload
        'constructor' => [ // 进程处理类构造函数参数
            // 监听的目录，不要过多，会导致检测变慢
            'monitorDir' => array_merge([
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // 监听这些后缀文件的更新
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            // 其它选项
            'options' => [
                // 是否开启文件监控，仅在linux下有效，默认守护进程模式不开启文件监控
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                // 是否开启内存监控，仅支持在linux下开启
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];
```

#### container.php
```php
// 返回一个psr-11依赖注入容器实例
return new Webman\Container;
```

#### dependence.php
```php
// 用于配置依赖注入容器中的服务和依赖关系
return [];
```

#### route.php
```php

use support\Route;
// 定义/test路径的路由
Route::any('/test', function (Request $request) {
    return response('test');
});
```

#### view.php
```php
use support\view\Raw;
use support\view\Twig;
use support\view\Blade;
use support\view\ThinkPHP;

return [
    'handler' => Raw::class // 默认视图处理类 
];
```

### autoload.php
```php
// 配置框架自动加载的文件
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php',
        base_path() . '/support/Response.php',
    ]
];
```

#### cache.php
```php
// 缓存配置
return [
    'default' => 'file', // 默认文件
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache') // 缓存文件存储位置
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default' // redis连接名，对应redis.php里的配置
        ],
        'array' => [
            'driver' => 'array' // 内存缓存，重启后失效
        ]
    ]
];
```

#### redis.php
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
];
```

#### database.php
```php
eturn [
 // 默认数据库
 'default' => 'mysql',
 // 各种数据库配置
 'connections' => [

     'mysql' => [
         'driver'      => 'mysql',
         'host'        => '127.0.0.1',
         'port'        => 3306,
         'database'    => 'webman',
         'username'    => 'webman',
         'password'    => '',
         'unix_socket' => '',
         'charset'     => 'utf8',
         'collation'   => 'utf8_unicode_ci',
         'prefix'      => '',
         'strict'      => true,
         'engine'      => null,
     ],

     'sqlite' => [
         'driver'   => 'sqlite',
         'database' => '',
         'prefix'   => '',
     ],

     'pgsql' => [
         'driver'   => 'pgsql',
         'host'     => '127.0.0.1',
         'port'     => 5432,
         'database' => 'webman',
         'username' => 'webman',
         'password' => '',
         'charset'  => 'utf8',
         'prefix'   => '',
         'schema'   => 'public',
         'sslmode'  => 'prefer',
     ],

     'sqlsrv' => [
         'driver'   => 'sqlsrv',
         'host'     => 'localhost',
         'port'     => 1433,
         'database' => 'webman',
         'username' => 'webman',
         'password' => '',
         'charset'  => 'utf8',
         'prefix'   => '',
     ],
 ],
];
```

#### exception.php
```php
return [
    // 设置异常处理类 
    '' => support\exception\Handler::class,
];
```

#### log.php
```php
return [
    'default' => [
        'handlers' => [
            [
                'class' => Monolog\Handler\RotatingFileHandler::class, // 处理器
                'constructor' => [
                    runtime_path() . '/logs/webman.log', // 日志名
                    7, //$maxFiles // 保留7天内的日志
                    Monolog\Logger::DEBUG, // 日志级别
                ],
                'formatter' => [
                    'class' => Monolog\Formatter\LineFormatter::class, // 格式化器
                    'constructor' => [null, 'Y-m-d H:i:s', true], // 格式化参数
                ],
            ]
        ],
    ],
];
```

#### session.php
```php
return [
     // 类型
    'type' => 'file', // or redis or redis_cluster
     // 处理器
    'handler' => FileSessionHandler::class,
     // 配置
    'config' => [
        'file' => [
            'save_path' => runtime_path() . '/sessions', // 存储目录
        ],
        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => '',
            'timeout' => 2,
            'database' => '',
            'prefix' => 'redis_session_',
        ],
        'redis_cluster' => [
            'host' => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth' => '',
            'prefix' => 'redis_session_',
        ]
    ],
    'session_name' => 'PHPSID', // session名
    'auto_update_timestamp' => false, // 是否自动更新时间戳，避免session过期
    'lifetime' => 7*24*60*60, // 生命周期
    'cookie_lifetime' => 365*24*60*60, // cookie生命周期
    'cookie_path' => '/', // cookie路径
    'domain' => '', // cookie域
    'http_only' => true, // 仅http访问
    'secure' => false, // 仅https访问
    'same_site' => '', // SameSite属性
    'gc_probability' => [1, 1000], // session回收概率
];
```

#### middleware.php
```php
// 设置中间件
return [];
```

#### static.php
```php
return [
    'enable' => true, // 是否开启webman的静态文件访问
    'middleware' => [ // 静态文件中间件，可用于设置缓存策略、跨域等
        //app\middleware\StaticFile::class,
    ],
];
```

#### translation.php
```php
return [
    // 默认语言
    'locale' => 'zh_CN',
    // 回退语言
    'fallback_locale' => ['zh_CN', 'en'],
    // 语言文件存储位置
    'path' => base_path() . '/resource/translations',
];
```
