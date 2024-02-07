# vlucas/phpdotenv

## 說明
`vlucas/phpdotenv`是一個環境變數加載組件，用來區分不同環境（如開發環境、測試環境等）的配置。

## 專案地址

https://github.com/vlucas/phpdotenv

## 安裝

```php
composer require vlucas/phpdotenv
```

## 使用

#### 專案根目錄新建`.env`文件
**.env**
```plaintext
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
    // 預設資料庫
    'default' => 'mysql',

    // 各種資料庫配置
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
> 建議將`.env`文件加入`.gitignore`列表，避免提交到程式庫。程式庫中增加一個`.env.example`配置範例文件，當專案部署時複製`.env.example`為`.env`，根據當前環境修改`.env`中的配置，這樣就可以讓專案在不同環境加載不同的配置了。

> **注意**
> `vlucas/phpdotenv`在PHP TS版本（線程安全版本）可能會有bug，請使用NTS版本（非線程安全版本）。
> 當前php是什麼版本可以通過執行 `php -v` 查看

## 更多內容

訪問 https://github.com/vlucas/phpdotenv
