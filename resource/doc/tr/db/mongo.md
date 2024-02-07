# MongoDB

webman varsayılan olarak [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) 'u MongoDB bileşeni olarak kullanır. Bu, laravel projesinden çıkarılmıştır ve laravel ile aynı kullanıma sahiptir.

`jenssegers/mongodb`'yi kullanmadan önce, `php-cli`'da mongodb eklentisini yüklemelisiniz.

> `php -m | grep mongodb` komutunu kullanarak `php-cli`'ın mongodb eklentisine sahip olup olmadığını kontrol edin. Not: `php-cli`'da mongodb eklentisini yüklemiş olsanız bile, bu `php-cli`'da kullanabileceğiniz anlamına gelmez, çünkü `php-cli` ve `php-fpm` farklı uygulamalardır ve farklı `php.ini` yapılandırmalarını kullanabilirler. Kullandığınız `php-cli`'ın hangi `php.ini` yapılandırma dosyasını kullandığını görmek için `php --ini` komutunu kullanın.

## Kurulum

PHP>7.2 için
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 için
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Kurulumdan sonra restart(reload değil) yapılması gerekmektedir.

## Yapılandırma
`config/database.php` dosyasına `mongodb` bağlantısını ekleyin, aşağıdaki gibi:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...diğer yapılandırmalar burada bulunur...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // burada Mongo Driver Manager'a daha fazla ayar gönderebilirsiniz
                // kullanabileceğiniz tam parametre listesi için https://www.php.net/manual/en/mongodb-driver-manager.construct.php adresine bakın

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

## Daha Fazlası İçin Ziyaret Edin

https://github.com/jenssegers/laravel-mongodb
