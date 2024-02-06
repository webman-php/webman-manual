# webman/console কমান্ড লাইন প্লাগইন

`webman/console` এর ভিত্তি সংযুক্ত `symfony/console`

> প্লাগইনের জন্য আবশ্যক webman>=1.2.2 এবং webman-framework>=1.2.1

## ইনস্টলেশন

```sh
composer require webman/console
```

## সমর্থিত কমান্ডস
**ব্যবহার পদ্ধতি**  
`php webman কমান্ড` বা `php webman কমান্ড`।
উদাহরণ: `php webman version` বা `php webman version`

## সমর্থিত কমান্ডস
### সংস্করণ
**webman এর সংস্করণ নম্বর ছাপা**

### রাউট: তালিকা
**বর্তমান রুট কনফিগারেশন ছাপা**

### make:controller
**একটি কন্ট্রোলার ফাইল তৈরি করুন** 
উদাহরণ: `php webman make:controller admin` একটি `app/controller/AdminController.php` তৈরি হবে
উদাহরণ: `php webman make:controller api/user` একটি `app/api/controller/UserController.php` তৈরি হবে

### make:model
**একটি মডেল ফাইল তৈরি করুন**
উদাহরণ: `php webman make:model admin` একটি `app/model/Admin.php` তৈরি হবে
উদাহরণ: `php webman make:model api/user` একটি `app/api/model/User.php` তৈরি হবে

### make:middleware
**একটি মিডলওয়ের ফাইল তৈরি করুন**
উদাহরণ: `php webman make:middleware Auth` একটি `app/middleware/Auth.php` তৈরি হবে

### make:command
**কাস্টম কমান্ড ফাইল তৈরি করুন**
উদাহরণ: `php webman make:command db:config` একটি `app\command\DbConfigCommand.php` তৈরি হবে

### plugin:create
**একটি মৌলিক প্লাগইন তৈরি করুন**
উদাহরণ: `php webman plugin:create --name=foo/admin` `config/plugin/foo/admin` এবং `vendor/foo/admin` দুটি ডিরেক্টরি তৈরি করবে
দেখুন[মৌলিক প্লাগইন তৈরি](/doc/webman/plugin/create.html)

### plugin:export
**মৌলিক প্লাগইন নির্যাতন**
উদাহরণ: `php webman plugin:export --name=foo/admin` 
দেখুন[মৌলিক প্লাগইন তৈরি](/doc/webman/plugin/create.html)

### plugin:export
**অ্যাপ্লিকেশন প্লাগইন নির্যাতন**
উদাহরণ: `php webman plugin:export shop`
দেখুন[অ্যাপ্লিকেশন প্লাগইন](/doc/webman/plugin/app.html)

### phar:pack
**ওয়েবম্যান প্রজেক্টকে phar ফাইলে প্যাক করুন**
লিংক দেখুন[phar প্যাক](/doc/webman/others/phar.html)
> এই বৈশিষ্ট্যটি webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 প্রয়োজন

## কাস্টম কমান্ড
ব্যবহারকারীরা নিজের কমান্ড ডিফাইন করতে পারেন, উদাহরণ হ্লদিক ডাটাবেস কনফিগারেশন প্রিন্ট করার জন্য নিম্নলিখিত একটি কমান্ড

* `php webman make:command config:mysql` নিষ্পাদন করুন
* `app/command/ConfigMySQLCommand.php` ফাইল খোলুন এবং নিম্নলিখিতে পরিবর্তন করুন

```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigMySQLCommand extends Command
{
    protected static $defaultName = 'config:mysql';
    protected static $defaultDescription = 'বর্তমান MySQL সার্ভার কনফিগারেশন প্রদর্শন';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL কনফিগারেশন তথ্য নিম্নলিখিতটি দেখুন:');
        $config = config('database');
        $headers = ['name', 'default', 'driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefix', 'strict', 'engine', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'true' : 'false';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['default'] == $name) {
                array_unshift($rows, $row);
            } else {
                $rows[] = $row;
            }
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}
```
  
## পরীক্ষা

কমান্ড লাইনে নিম্নলিখিত কমান্ড চালান: `php webman config:mysql`

নিম্নলিখিত মন্তব্য প্রাপ্ত হবে:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## অধিক তথ্যের জন্য দেখুন
http://www.symfonychina.com/doc/current/components/console.html
