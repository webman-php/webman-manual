# Casbin Erişim Kontrol Kütüphanesi webman-permisyon

## Açıklama

Bu, [PHP-Casbin](https://github.com/php-casbin/php-casbin) üzerine kurulmuş güçlü, verimli bir açık kaynak erişim kontrol çerçevesi olan `ACL`, `RBAC`, `ABAC` gibi erişim kontrol modellerini destekler.

## Proje Linki

https://github.com/Tinywan/webman-permission

## Kurulum

```php
composer require tinywan/webman-permission
```
> Bu uzantı PHP 7.1+ ve [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) gerektirir, resmi belge: https://www.workerman.net/doc/webman#/db/others

## Yapılandırma

### Servisi Kaydet

Yeni bir yapılandırma dosyası oluşturun `config/bootstrap.php` ve içeriği aşağıdaki gibi olmalıdır:

```php
    // ...
    webman\permission\Izin::class,
```
### Model Yapılandırma Dosyası

Yeni bir yapılandırma dosyası oluşturun `config/casbin-basic-model.conf` ve içeriği aşağıdaki gibi olmalıdır:

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```
### Politika Yapılandırma Dosyası

Yeni bir yapılandırma dosyası oluşturun `config/permission.php` ve içeriği aşağıdaki gibi olmalıdır:

```php
<?php

return [
    /*
     *Varsayılan İzin
     */
    'default' => 'temel',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'temel' => [
            /*
            * Model Ayarı
            */
            'model' => [
                'config_type' => 'dosya',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adaptör .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Veritabanı Ayarları.
            */
            'database' => [
                // Veritabanı bağlantı adı, boş bırakıldığında varsayılan yapılandırma.
                'connection' => '',
                // Politika tablo adı (önek tablo adını içermez)
                'rules_name' => 'kural',
                // Politika tablo tam adı.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Hızlı Başlangıç

```php
use webman\permission\Permission;

// bir kullanıcıya izinleri ekler
Permission::addPermissionForUser('eve', 'makaleler', 'okuma');
// bir kullanıcıya bir rol ekler
Permission::addRoleForUser('eve', 'yazar');
// bir kural için izinleri ekler
Permission::addPolicy('yazar', 'makaleler','düzenleme');
```

Kullanıcının bu tür bir yetkiye sahip olup olmadığını kontrol edebilirsiniz

```php
if (Permission::enforce("eve", "makaleler", "düzenleme")) {
    // eve'nin makaleleri düzenlemesine izin ver
} else {
    // isteği reddet, bir hata göster
}
````

## Yetkilendirme Ara Yazılım

Dosya oluşturun `app/middleware/YetkilendirmeMiddleware.php` (dizin yoksa kendiniz oluşturun) aşağıdaki gibi:

```php
<?php

/**
 * Yetkilendirme Ara Yazılımı
 * Yazar: ShaoBo Wan (Tinywan)
 * Tarih: 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class YetkilendirmeMiddleware implements MiddlewareInterface
{
	public function process(Request $request, callable $next): Response
	{
		$uri = $request->path();
		try {
			$userId = 10086;
			$action = $request->method();
			if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
				throw new \Exception('Üzgünüm, bu API'ye erişim izniniz yok');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('Yetkilendirme istisnası' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

`config/middleware.php` içine aşağıdaki gibi bir genel ara yazılım ekleyin:

```php
return [
    // Genel ara yazılımlar
    '' => [
        // ... diğer ara yazılımlar burada kısaltılmıştır
        app\middleware\YetkilendirmeMiddleware::class,
    ]
];
```

## Teşekkür

[Casbin](https://github.com/php-casbin/php-casbin), [resmi web sitesinde](https://casbin.org/) tüm belgelere bakabilirsiniz.

## Lisans

Bu proje [Apache 2.0 lisansı](LICENSE) ile lisanslanmıştır.
