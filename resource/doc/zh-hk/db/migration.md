# Migration數據庫遷移工具 Phinx

## 說明

Phinx 可以讓開發者簡潔地修改和維護數據庫。 它避免了人為的手寫 SQL 語句，它使用強大的 PHP API 去管理數據庫遷移。開發者可以使用版本控制管理他們的數據庫遷移。 Phinx 可以方便地進行不同數據庫之間數據遷移。還可以追踪到哪些遷移腳本被執行，開發者可以不再擔心數據庫的狀態從而更加關注如何編寫出更好的系統。

## 專案地址

https://github.com/cakephp/phinx

## 安裝

```php
composer require robmorgan/phinx
```

## 官方中文文檔地址

詳細使用可以去看官方中文文檔，這裡只講怎麼在webman中配置使用

https://tsy12321.gitbooks.io/phinx-doc/content/

## 遷移文件目錄結構

```
.
├── app                           應用目錄
│   ├── controller                控制器目錄
│   │   └── Index.php             控制器
│   ├── model                     模型目錄
......
├── database                      數據庫文件
│   ├── migrations                遷移文件
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     測試數據
│   │   └── UserSeeder.php
......
```

## phinx.php 配置

在專案根目錄創建 phinx.php 文件

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## 使用建議

遷移文件一旦程式合併後不允許再次修改，出現問題必須新建修改或者刪除操作文件進行處理。

#### 數據表創建操作文件命名規則

`{time(自動創建)}_create_{表名英文小寫}`

#### 數據表修改操作文件命名規則

`{time(自動創建)}_modify_{表名英文小寫+具體修改項英文小寫}`

### 數據表刪除操作文件命名規則

`{time(自動創建)}_delete_{表名英文小寫+具體修改項英文小寫}`

### 填充數據文件命名規則

`{time(自動創建)}_fill_{表名英文小寫+具體修改項英文小寫}`