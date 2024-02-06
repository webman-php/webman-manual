# MongoDB

webman, [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) bileşenini mongodb bileşeni olarak varsayar ve bu, laravel projesinden çıkarılan ve laravel ile aynı şekilde kullanılan bir bileşendir.

`jenssegers/mongodb` kullanmadan önce, `php-cli`'ye mongodb uzantısını yüklemeniz gerekir.

> `php -m | grep mongodb` komutunu kullanarak `php-cli`'ye mongodb uzantısının yüklenip yüklenmediğini kontrol edebilirsiniz. Not: `php-fpm`'de mongodb uzantısını yüklediyseniz bile, `php-cli`'nin bunu kullanabildiği anlamına gelmez çünkü `php-cli` ve `php-fpm` farklı uygulamalardır ve farklı `php.ini` yapılandırmalarını kullanabilir. Kullandığınız `php-cli` için hangi `php.ini` yapılandırma dosyasını kullandığınızı görmek için `php --ini` komutunu kullanın.

## Kurulum

PHP>7.2 için
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 için
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Kurulumdan sonra yeniden başlatmanız gerekmektedir (reload işlemi geçerli değildir)

## Yapılandırma
`config/database.php` dosyasına `mongodb` bağlantısını ekleyin, aşağıdaki gibi:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...diğer yapılandırmalar burada kısaltıldı...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // burada daha fazla ayar geçebilirsiniz
                // tüm parametreleri görmek için https://www.php.net/manual/en/mongodb-driver-manager.construct.php adresindeki "Uri Options" altında bulunan tam parametre listesine bakabilirsiniz

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Örnek
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## Daha fazla bilgi için
https://github.com/jenssegers/laravel-mongodb
