# Migration数据库迁移工具 Phinx

## 说明

Phinx 可以让开发者简洁的修改和维护数据库。 它避免了人为的手写 SQL 语句，它使用强大的 PHP API 去管理数据库迁移。开发者可以使用版本控制管理他们的数据库迁移。 Phinx 可以方便的进行不同数据库之间数据迁移。还可以追踪到哪些迁移脚本被执行，开发者可以不再担心数据库的状态从而更加关注如何编写出更好的系统。
  
## 项目地址

https://github.com/cakephp/phinx

## 安装
 
  ```php
  composer require robmorgan/phinx
  ```
  
## 官方中文文档地址

详细使用可以去看官方中文文档，这里只讲怎么在webman中配置使用

https://tsy12321.gitbooks.io/phinx-doc/content/

## 迁移文件目录结构

```
.
├── app                           应用目录
│   ├── controller                控制器目录
│   │   └── Index.php             控制器
│   ├── model                     模型目录
......
├── database                      数据库文件
│   ├── migrations                迁移文件
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     测试数据
│   │   └── UserSeeder.php
......
```

## phinx.php 配置

在项目根目录创建 phinx.php 文件

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

## 使用建议

迁移文件一旦代码合并后不允许再次修改，出现问题必须新建修改或者删除操作文件进行处理。

#### 数据表创建操作文件命名规则

`{time(auto create)}_create_{表名英文小写}`

#### 数据表修改操作文件命名规则

`{time(auto create)}_modify_{表名英文小写+具体修改项英文小写}`

### 数据表删除操作文件命名规则

`{time(auto create)}_delete_{表名英文小写+具体修改项英文小写}`

### 填充数据文件命名规则

`{time(auto create)}_fill_{表名英文小写+具体修改项英文小写}`




