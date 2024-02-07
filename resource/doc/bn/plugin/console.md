# webman/console কমান্ড লাইন প্লাগইন

`webman/console` `symfony/console` উপর নির্ভর করে

> প্লাগইনটি webman>=1.2.2 webman-framework>=1.2.1 এর প্রয়োজন

## ইনস্টলেশন

```sh
composer require webman/console
```

## সমর্থিত কমান্ড
**ব্যবহার পদ্ধতি**  
`php webman কমান্ড` বা `php webman কমান্ড`।
উদাহরণঃ `php webman version` বা `php webman version`

## সমর্থিত কমান্ড
### version
**webman ভার্সন নাম্বার মুদ্রণ**

### route:list
**বর্তমান রাউট কনফিগারেশন মুদ্রণ**

### make:controller
**একটি কন্ট্রোলার ফাইল তৈরি করুন** 
উদাহরণ `php webman make:controller admin` যা তৈরি করবে `app/controller/AdminController.php`
উদাহরণ `php webman make:controller api/user` যা তৈরি করবে `app/api/controller/UserController.php`

### make:model
**একটি মডেল ফাইল তৈরি করুন**
উদাহরণ `php webman make:model admin` যা তৈরি করবে `app/model/Admin.php`
উদাহরণ `php webman make:model api/user` যা তৈরি করবে `app/api/model/User.php`

### make:middleware
**একটি মিডলওয়ার ফাইল তৈরি করুন**
উদাহরণ `php webman make:middleware Auth` যা তৈরি করবে `app/middleware/Auth.php`

### make:command
**কাস্টম কমান্ড ফাইল তৈরি করুন**
উদাহরণ `php webman make:command db:config` যা তৈরি করবে `app\command\DbConfigCommand.php`

### plugin:create
**একটি মৌলিক প্লাগইন তৈরি করুন**
উদাহরণ `php webman plugin:create --name=foo/admin` যা তৈরি করবে `config/plugin/foo/admin` এবং `vendor/foo/admin` দুটি নির্দেশক
[মৌলিক প্লাগইন তৈরি করুন](/doc/webman/plugin/create.html) দেখুন

### plugin:export
**মৌলিক প্লাগইন রপ্তানি করুন**
যেমন `php webman plugin:export --name=foo/admin` 
[মৌলিক প্লাগইন তৈরি করুন](/doc/webman/plugin/create.html) দেখুন

### plugin:export
**অ্যাপ্লিকেশন প্লাগইন নির্যাতন করুন**
যেমন `php webman plugin:export shop`
[অ্যাপ্লিকেশন প্লাগইন](/doc/webman/plugin/app.html) দেখুন

### phar:pack
**webman প্রজেক্ট প্যাক করুন ফার ফাইল**
[phar প্যাক](/doc/webman/others/phar.html) দেখুন
> এই বৈশিষ্ট্য webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 প্রয়োজন

## কাস্টম কমান্ড
ব্যবহারকারীরা নিজেরা কমান্ড সংজ্ঞা করতে পারেন, উদাহরণ, তালিকাবদ্ধ ডাটাবেস কনফিগারেশন মুদ্রণ করার কমান্ডটি নিম্নলিখিত রকম

* `php webman make:command config:mysql` এ নির্ধারিত করুন
* ধরুন `app/command/ConfigMySQLCommand.php` খোলুন এবং নিম্নলিখিত অনুসরণ করুন

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
        $output->writeln('MySQL কনফিগারেশন তথ্য নিম্নলিখিতভাবে দেখাবে:');
        $config = config('database');
        $headers = ['নাম', 'ডিফল্ট', 'ড্রাইভার', 'দূরবর্তী', 'পোর্ট', 'ডাটাবেস', 'ব্যবহারকারীনাম', 'পাসওয়ার্ড', 'উনিক্স_সকেট', 'চারসেট', 'কলেকশন', 'প্রিফিক্স', 'স্ট্রিক্ট', 'ইঞ্জিন', 'স্কিমা', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'নাম':
                        $row[] = $name;
                        break;
                    case 'ডিফল্ট':
                        $row[] = $config['ডিফল্ট'] == $name ? 'true' : 'false';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['ডিফল্ট'] == $name) {
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

কমান্ড লাইনে চালিয়ে দিন `php webman config:mysql`

নিম্নলিখিত অনুসারে ফলাফল হবে:
```plaintext
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| নাম   | ডিফল্ট | ড্রাইভার | হোস্ট      | পোর্ট | ডাটাবেস | ব্যবহারকারীনাম | পাসওয়ার্ড | উনিক্স_সকেট | চারসেট | কলেকশন       | প্রিফিক্স | স্ট্রিক্ট | ইঞ্জিন | স্কিমা | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## অধিক তথ্যের জন্য
http://www.symfonychina.com/doc/current/components/console.html
