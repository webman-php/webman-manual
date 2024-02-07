# Hızlı Başlangıç

webman veritabanı varsayılan olarak [illuminate/database](https://github.com/illuminate/database) kullanmaktadır, yani [laravel veritabanı](https://learnku.com/docs/laravel/8.x/database/9400) ile aynı şekilde kullanılır.

Tabii ki, ThinkPHP veya diğer veritabanlarını kullanmak için [Diğer Veritabanı Bileşenlerini Kullanma](others.md) bölümüne bakabilirsiniz.

## Kurulum

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Kurulumdan sonra, yeniden başlatma gereklidir (yeniden yükleme geçersizdir)

> **İpucu**
> Eğer sayfalama, veritabanı etkinliği, SQL yazdırma gibi şeylere ihtiyacınız yoksa, sadece şunu çalıştırmanız yeterlidir
> `composer require -W illuminate/database`

## Veritabanı Yapılandırması
`config/database.php`
```php
return [
    // Varsayılan veritabanı
    'default' => 'mysql',

    // Çeşitli veritabanı yapılandırmaları
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```


## Kullanma
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("merhaba $name");
    }
}
```
