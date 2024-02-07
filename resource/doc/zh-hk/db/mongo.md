# MongoDB

webman預設使用 [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) 作為mongodb組件，它是從laravel項目中抽離出來的，用法與laravel相同。

在使用`jenssegers/mongodb`之前必須先給`php-cli`安裝mongodb擴展。

> 使用命令`php -m | grep mongodb`查看`php-cli`是否裝了mongodb擴展。注意：即使你在`php-fpm`安裝了mongodb擴展，不代表你在`php-cli`可以使用它，因為`php-cli`和`php-fpm`是不同的應用程序，可能使用的是不同的`php.ini`配置。使用命令`php --ini`來查看你的`php-cli`使用的是哪個`php.ini`配置文件。

## 安裝

PHP>7.2時
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2時
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

安裝後需要restart重啟(reload無效)

## 配置
在 `config/database.php` 裡增加 `mongodb` connection， 類似如下：
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...這裡省略了其他配置...

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
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## 更多內容請訪問

https://github.com/jenssegers/laravel-mongodb
