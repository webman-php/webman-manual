# Casbin Erişim Kontrol Kütüphanesi webman-permission

## Açıklama

Bu kütüphane, [PHP-Casbin](https://github.com/php-casbin/php-casbin) üzerine kurulmuştur. Bu güçlü ve verimli açık kaynak erişim kontrol çerçevesi, `ACL`, `RBAC`, `ABAC` gibi erişim kontrol modellerini destekler.

## Proje Adresi

https://github.com/Tinywan/webman-permission

## Kurulum
 
```php
composer require tinywan/webman-permission
```
> Bu eklenti PHP 7.1+ ve [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) gerektirir. Resmi belgeler: https://www.workerman.net/doc/webman#/db/others

## Yapılandırma

### Servis Kaydı
Aşağıdaki gibi içeriği olan `config/bootstrap.php` adında bir yapılandırma dosyası oluşturun:

```php
    // ...
    webman\permission\Permission::class,
```

### Model Yapılandırma Dosyası

Aşağıdaki gibi içeriği olan `config/casbin-basic-model.conf` adında bir yapılandırma dosyası oluşturun:

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

### İzin Yapılandırma Dosyası

Aşağıdaki gibi içeriği olan `config/permission.php` adında bir yapılandırma dosyası oluşturun:

```php
<?php

return [
    /*
     *Varsayılan İzin
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Model Ayarları
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adaptör .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Veritabanı Ayarları.
            */
            'database' => [
                // Veritabanı bağlantı adı, boş bırakılması durumunda varsayılan yapılandırma kullanılır.
                'connection' => '',
                // Kural tablo adı (önek olmadan)
                'rules_name' => 'rule',
                // Kural tablosunun tam adı.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Hızlı Başlangıç

```php
use webman\permission\Permission;

// Bir kullanıcıya izin ekler
Permission::addPermissionForUser('eve', 'articles', 'read');
// Bir kullanıcıya rol ekler.
Permission::addRoleForUser('eve', 'writer');
// Bir kurala izin ekler
Permission::addPolicy('writer', 'articles','edit');
```

Kullanıcının bu tür bir izne sahip olup olmadığını kontrol edebilirsiniz

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // eve'e makaleleri düzenleme izni ver
} else {
    // isteği reddet, bir hata göster
}
```

## Yetkilendirme Ara Katmanı

Aşağıdaki gibi bir `app/middleware/AuthorizationMiddleware.php` dosyası oluşturun (dizin mevcut değilse kendiniz oluşturun):

```php
<?php

/**
 * Yetkilendirme Ara Katmanı
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

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $uri = $request->path();
        try {
            $userId = 10086;
            $action = $request->method();
            if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
                throw new \Exception('Üzgünüz, bu API\'ye erişim izniniz yok');
            }
        } catch (CasbinException $exception) {
            throw new \Exception('Yetkilendirme istisnası' . $exception->getMessage());
        }
        return $next($request);
    }
}
```

`config/middleware.php` dosyasına aşağıdaki gibi genel ara katmanı ekleyin:

```php
return [
    // Genel ara katmanı
    '' => [
        // ... Diğer ara katmanları burada kısaltılmıştır
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Teşekkürler

[Casbin](https://github.com/php-casbin/php-casbin), tüm belgelere [resmi web sitesi](https://casbin.org/) üzerinden erişebilirsiniz.

## Lisans

Bu proje [Apache 2.0 lisansı](LICENSE) ile lisanslanmıştır.
