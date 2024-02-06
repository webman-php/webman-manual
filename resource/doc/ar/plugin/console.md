# إضافة سطر الأوامر webman/console

`webman/console` معتمد على `symfony/console`

> تحتاج الإضافة webman>=1.2.2 و webman-framework>=1.2.1

## التثبيت
 
```sh
composer require webman/console
```

## الأوامر المدعومة
**طريقة الاستخدام**  
`php webman الأمر` أو `php webman الأمر`.
مثال `php webman version` أو `php webman version`

## الأوامر المدعومة
### version
**طباعة رقم إصدار webman**

### route:list
**طباعة تكوين الطريق الحالي**

### make:controller
**إنشاء ملف تحكم**
مثال `php webman make:controller admin` سينشئ `app/controller/AdminController.php`
مثال `php webman make:controller api/user` سينشئ `app/api/controller/UserController.php`

### make:model
**إنشاء ملف نموذج**
مثال `php webman make:model admin` سينشئ `app/model/Admin.php`
مثال `php webman make:model api/user` سينشئ `app/api/model/User.php`

### make:middleware
**إنشاء ملف وسيط**
مثال `php webman make:middleware Auth` سينشئ `app/middleware/Auth.php`

### make:command
**إنشاء ملف أمر مخصص**
مثال `php webman make:command db:config` سينشئ `app\command\DbConfigCommand.php`

### plugin:create
**إنشاء إضافة أساسية**
مثال `php webman plugin:create --name=foo/admin` سينشئ دليكتورين `config/plugin/foo/admin` و `vendor/foo/admin`
انظر[إنشاء إضافة أساسية](/doc/webman/plugin/create.html)

### plugin:export
**تصدير إضافة أساسية**
مثال `php webman plugin:export --name=foo/admin` 
انظر[إنشاء إضافة أساسية](/doc/webman/plugin/create.html)

### plugin:export
**تصدير إضافة تطبيق**
مثال `php webman plugin:export shop`
انظر[إضافة تطبيق](/doc/webman/plugin/app.html)

### phar:pack
**تعبئة مشروع webman كملف phar**
انظر[تعبئة phar](/doc/webman/others/phar.html)
> هذه الميزة تحتاج webman>=1.2.4 و webman-framework>=1.2.4 و webman\console>=1.0.5

## الأوامر المخصصة
يمكن للمستخدمين تحديد أوامر مخصصة، مثل الأمر التالي لطباعة تكوين قاعدة البيانات

* تنفيذ `php webman make:command config:mysql`
* فتح `app/command/ConfigMySQLCommand.php` وتعديله كما يلي

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
    protected static $defaultDescription = 'عرض تكوين خادم MySQL الحالي';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('معلومات تكوين MySQL كالتالي:');
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
  
## الاختبار

تشغيل الأمر التالي `php webman config:mysql`

النتيجة تشبه ما يلي:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## للمزيد من المعلومات يرجى الرجوع إلى
http://www.symfonychina.com/doc/current/components/console.html
