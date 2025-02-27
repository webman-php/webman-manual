# MongoDB

webman默认使用 [mongodb/laravel-mongodb](https://github.com/mongodb/laravel-mongodb) 作为mongodb组件，它是从laravel项目中抽离出来的，用法与laravel相同。

使用`jenssegers/mongodb`之前必须先给`php-cli`安装mongodb扩展。

> **注意**
> 当前手册为 webman v2 版本，如果您使用的是webman v1版本，请查看 [v1版本手册](/doc/webman-v1/db/mongo.html)
> 使用命令`php -m | grep mongodb`查看`php-cli`是否装了mongodb扩展。注意：即使你在`php-fpm`安装了mongodb扩展，不代表你在`php-cli`可以使用它，因为`php-cli`和`php-fpm`是不同的应用程序，可能使用的是不同的`php.ini`配置。使用命令`php --ini`来查看你的`php-cli`使用的是哪个`php.ini`配置文件。

## 安装

```php
composer require -W webman/database mongodb/laravel-mongodb ^4.8
```

安装后需要restart重启(reload无效)

## 配置
在 `config/database.php` 里增加 `mongodb` connection， 类似如下：
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...这里省略了其它配置...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // here you can pass more settings to the Mongo Driver Manager
                // see https://www.php.net/manual/en/mongodb-driver-manager.construct.php under "Uri Options" for a list of complete parameters that you can use

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## 示例
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->table('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->table('test')->get());
    }
}
```

## 模型示例
```php
<?php
namespace app\model;

use DateTimeInterface;
use support\MongoModel as Model;

class Test extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'test';

    public $timestamps = true;

    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}

```

## 更多内容请访问

https://github.com/mongodb/laravel-mongodb

