# webman/console Komut Satırı Eklentisi

`webman/console`, `symfony/console` temel alınarak oluşturulmuştur

> Eklenti webman>=1.2.2 webman-framework>=1.2.1 gerektirmektedir.

## Kurulum
 
```sh
composer require webman/console
```

## Desteklenen Komutlar
**Kullanımı**  
`php webman komut` ya da `php webman komut`.
Örneğin `php webman version` ya da `php webman version`

## Desteklenen Komutlar
### version
**webman sürüm numarasını yazdırır**

### route:list
**Mevcut rotaların yapılandırmasını yazdırır**

### make:controller
**Bir denetleyici dosyası oluşturur** 
Örneğin `php webman make:controller admin` ile `app/controller/AdminController.php` dosyası oluşturulur
Örneğin `php webman make:controller api/user` ile `app/api/controller/UserController.php` dosyası oluşturulur

### make:model
**Bir model dosyası oluşturur**
Örneğin `php webman make:model admin` ile `app/model/Admin.php` dosyası oluşturulur
Örneğin `php webman make:model api/user` ile `app/api/model/User.php` dosyası oluşturulur

### make:middleware
**Bir ara katman dosyası oluşturur**
Örneğin `php webman make:middleware Auth` ile `app/middleware/Auth.php` dosyası oluşturulur

### make:command
**Özel komut dosyası oluşturur**
Örneğin `php webman make:command db:config` ile `app\command\DbConfigCommand.php` dosyası oluşturulur

### plugin:create
**Temel bir eklenti oluşturur**
Örneğin `php webman plugin:create --name=foo/admin` ile `config/plugin/foo/admin` ve `vendor/foo/admin` olmak üzere iki dizin oluşturulur
Bakınız[Temel bir eklenti oluşturma](/doc/webman/plugin/create.html)

### plugin:export
**Temel eklentiyi dışa aktarır**
Örneğin `php webman plugin:export --name=foo/admin` 
Bakınız[Temel bir eklenti oluşturma](/doc/webman/plugin/create.html)

### plugin:export
**Uygulama eklentisini dışa aktarır**
Örneğin `php webman plugin:export shop`
Bakınız[Uygulama Eklentileri](/doc/webman/plugin/app.html)

### phar:pack
**webman projesini phar dosyasına paketler**
Bkz. [phar paketleme](/doc/webman/others/phar.html)
> Bu özellik webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 gerektirir

## Özel Komutlar
Kullanıcılar kendi komutlarını tanımlayabilir, örneğin aşağıdaki, veritabanı yapılandırmasını yazdıran bir komuttur

* `php webman make:command config:mysql` komutunu çalıştırın
* Aşağıdaki gibi düzenleyin `app/command/ConfigMySQLCommand.php` dosyasını açın

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
    protected static $defaultDescription = 'Şu anki MySQL sunucusu yapılandırmasını gösterir';

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

Komut isteminden `php webman config:mysql` komutunu çalıştırın

Sonuç aşağıdakine benzer olacaktır:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Daha Fazla Bilgi İçin
http://www.symfonychina.com/doc/current/components/console.html
