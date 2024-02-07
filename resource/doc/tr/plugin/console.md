# webman/console Komut Satırı Eklentisi

`webman/console` `symfony/console` üzerine kuruludur.

> Eklenti webman>=1.2.2 webman-framework>=1.2.1 gerektirir

## Kurulum

```sh
composer require webman/console
```

## Desteklenen Komutlar
**Kullanım**  
`php webman komut` or `php webman komut`.
Örnek: `php webman version` veya `php webman version`

## Desteklenen Komutlar
### version
**webman sürüm numarasını yazdırır**

### route:list
**Mevcut rota yapılandırmasını yazdırır**

### make:controller
**Bir denetleyici dosyası oluşturur** 
Örneğin `php webman make:controller admin` `app/controller/AdminController.php` dosyasını oluşturur
Örneğin `php webman make:controller api/user` `app/api/controller/UserController.php` dosyasını oluşturur

### make:model
**Bir model dosyası oluşturur**
Örneğin `php webman make:model admin` `app/model/Admin.php` dosyasını oluşturur
Örneğin `php webman make:model api/user` `app/api/model/User.php` dosyasını oluşturur

### make:middleware
**Bir ara yazılım dosyası oluşturur**
Örneğin `php webman make:middleware Auth` `app/middleware/Auth.php` dosyasını oluşturur

### make:command
**Özel bir komut dosyası oluşturur**
Örneğin `php webman make:command db:config` `app\command\DbConfigCommand.php` dosyasını oluşturur

### plugin:create
**Temel bir eklenti oluşturur**
Örneğin `php webman plugin:create --name=foo/admin` `config/plugin/foo/admin` ve `vendor/foo/admin` iki dizin oluşturur
[Düzeyli Bir Eklenti Oluştur](/doc/webman/plugin/create.html) için bkz.

### plugin:export
**Temel bir eklentiyi dışa aktarır**
Örneğin `php webman plugin:export --name=foo/admin` 
[Düzeyli Bir Eklenti Oluştur](/doc/webman/plugin/create.html) için bkz.

### plugin:export
**Uygulama eklentisini dışa aktarır**
Örneğin `php webman plugin:export shop`
[Uygulama Eklentileri](/doc/webman/plugin/app.html) için bkz.

### phar:pack
**webman projesini phar dosyasına paketler**
[phar Paketleme](/doc/webman/others/phar.html) için bkz.
> Bu özellik webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 gerektirir

## Özel Komutlar
Kullanıcılar kendi komutlarını tanımlayabilir, örneğin, aşağıdaki, veritabanı yapılandırmasını yazdırmak için bir komuttur

* `php webman make:command config:mysql` komutunu çalıştırın.
* `app/command/ConfigMySQLCommand.php` dosyasını aşağıdaki gibi değiştirin

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
    protected static $defaultDescription = 'Mevcut MySQL sunucu yapılandırmasını gösterir';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL yapılandırma bilgileri aşağıdaki gibidir:');
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

## Test

Komut isteminden `php webman config:mysql` komutunu çalıştırın.

Sonuç aşağıdaki gibi olacaktır:
```plaintext
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Daha Fazla Bilgi İçin
http://www.symfonychina.com/doc/current/components/console.html
