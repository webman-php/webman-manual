```php
# Veritabanı
Eklenti kendi veritabanını yapılandırabilir, örneğin `plugin/foo/config/database.php` içeriği aşağıdaki gibi olabilir
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
Referans yöntemi `Db::connection('plugin.{eklenti}.{bağlantı_adı}');` şeklindedir, örneğin
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Ana projenin veritabanını kullanmak istiyorsanız, doğrudan kullanabilirsiniz, örneğin
```php
use support\Db;
Db::table('user')->first();
// Varsayalım ana projede admin bağlantısı da yapılandırıldı
Db::connection('admin')->table('admin')->first();
```

## Model için Veritabanı Yapılandırması

Model için bir Base sınıfı oluşturabiliriz, Base sınıfında `$connection` ile eklentin kendi veritabanı bağlantısını belirleyebiliriz, örneğin

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

Bu şekilde eklentin içindeki tüm Modeller Base'den miras alarak otomatik olarak eklentinin kendi veritabanını kullanacaktır.

## Veritabanı Yapılandırmasını Tekrar Kullanma
Tabii ki ana projenin veritabanı yapılandırmasını tekrar kullanabiliriz, [webman-admin](https://www.workerman.net/plugin/82) ile entegrasyon yapmışsak, [webman-admin](https://www.workerman.net/plugin/82) veritabanı yapılandırmasını da tekrar kullanabiliriz, örneğin
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
