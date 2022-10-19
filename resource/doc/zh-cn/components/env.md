# vlucas/phpdotenv

## 说明
`vlucas/phpdotenv`是一个环境变量加载组件，用来区分不同环境(如开发环境、测试环境等)的配置。

## 项目地址

https://github.com/vlucas/phpdotenv
  
## 安装
 
```php
composer require vlucas/phpdotenv
 ```
  
## 使用

#### 项目根目录新建`.env`文件
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### 修改配置文件
**config/database.php**
```php
return [
    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **提示**
> 建议将`.env`文件加入`.gitignore`列表，避免提交到代码库。代码库中增加一个`.env.example`配置样例文件，当项目部署时复制`.env.example`为`.env`，根据当前环境修改`.env`中的配置，这样就可以让项目在不同环境加载不同的配置了。

> **注意**
> `vlucas/phpdotenv`在PHP TS版本(线程安全版本)可能会有bug，请使用NTS版本(非线程安全版本)。
> 当前php是什么版本可以通过执行 `php -v` 查看 

## 更多内容

访问 https://github.com/vlucas/phpdotenv
  

