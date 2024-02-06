# Veritabanı
Eklentiler kendi veritabanlarını yapılandırabilir, örneğin `plugin/foo/config/database.php` dosyasının içeriği aşağıdaki gibidir
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql bağlantı adı
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'veritabanı',
            'username'    => 'kullanıcı adı',
            'password'    => 'şifre',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin bağlantı adı
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'veritabanı',
            'username'    => 'kullanıcı adı',
            'password'    => 'şifre',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Referans şekli `Db::connection('plugin.{eklenti}.{bağlantı adı}');`, örneğin
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Ana projenin veritabanını kullanmak istiyorsanız, doğrudan kullanabilirsiniz, örneğin
```php
use support\Db;
Db::table('user')->first();
// Varsayalım ana projenin bir admin bağlantısı da yapılandırıldı
Db::connection('admin')->table('admin')->first();
```

## Model'e Veritabanı Yapılandırma

Model için bir Base sınıfı oluşturabiliriz, Base sınıfı `$connection` ile eklentinin kendi veritabanını belirtir, örneğin

```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

Bu şekilde, eklenti içindeki tüm Modeller, Base sınıfından miras aldığı için otomatik olarak eklentinin kendi veritabanını kullanır.

## Veritabanı Yapılandırmasını Tekrar Kullanma
Tabii ki ana projenin veritabanı yapılandırmasını da tekrar kullanabiliriz, [webman-admin](https://www.workerman.net/plugin/82) entegrasyonu varsa, [webman-admin](https://www.workerman.net/plugin/82) veritabanı yapılandırmasını da tekrar kullanabiliriz, örneğin
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
