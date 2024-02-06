# Çoklu Uygulama
Bir proje bazen birden fazla alt projeye ayrılabilir, örneğin bir alışveriş sitesi, alışveriş ana projesi, alışveriş API arayüzü ve alışveriş yönetim arayüzü olmak üzere üç alt projeye ayrılabilir ve hepsi aynı veritabanı yapılandırmasını kullanır.

webman sana app dizinini bu şekilde planlamanı sağlar:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
`http://127.0.0.1:8787/shop/{controller}/{method}` adresine gidildiğinde `app/shop/controller` dizinindeki controller ve methoda ulaşılır.

`http://127.0.0.1:8787/api/{controller}/{method}` adresine gidildiğinde `app/api/controller` dizinindeki controller ve methoda ulaşılır.

`http://127.0.0.1:8787/admin/{controller}/{method}` adresine gidildiğinde `app/admin/controller` dizinindeki controller ve methoda ulaşılır.

webman'de, app dizinini şu şekilde bile planlayabilirsin:
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Bu durumda `http://127.0.0.1:8787/{controller}/{method}` adresine gidildiğinde `app/controller` dizinindeki controller ve methoda ulaşılır. Path'te api veya admin ile başladığında ilgili dizindeki controller ve methoda ulaşılır.

Birden fazla uygulama durumunda sınıf isim alanı `psr4`'e uygun olmalıdır, örneğin `app/api/controller/FooController.php` dosyası aşağıdakine benzer olmalıdır:

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Çoklu Uygulama Arası Ara Middleware Yapılandırması
Bazen farklı uygulamalar için farklı ara katmanlar yapılandırmak isteyebilirsin, örneğin 'api' uygulamasının bir CORS ara katmanına ihtiyacı olabilir, 'admin' ise bir yönetici girişi kontrolü ara katmanına ihtiyaç duyabilir, bu durumda `config/midlleware.php` dosyası aşağıdakine benzer olabilir:
```php
return [
    // Global ara katmanlar
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // API uygulaması ara katmanları
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Admin uygulama ara katmanları
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Yukarıdaki ara katmanlar gerçekte mevcut olmayabilir, burada sadece farklı uygulamalar için ara katmanları nasıl yapılandıracağına dair bir örnek sunulmuştur.

Ara katmanların çalışma sırası `Global ara katmanlar` -> `Uygulama ara katmanları` şeklindedir.

Ara katman geliştirme kılavuzu [Ara Katmanlar Bölümü](middleware.md)'nde bulunmaktadır.

## Çoklu Uygulama İstisna İşleme Yapılandırması
Benzer şekilde, farklı uygulamalar için farklı istisna işleme sınıfları yapılandırmak isteyebilirsin, örneğin 'shop' uygulamasında bir istisna oluştuğunda kullanıcı dostu bir hata sayfası sunmak isteyebilirsin; 'api' uygulamasında bir istisna oluştuğunda bir sayfa değil, bir JSON dizesi döndürmek isteyebilirsin. Farklı uygulamalar için farklı istisna işleme sınıfları yapılandırma dosyası `config/exception.php` aşağıdakine benzer olabilir:

```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Ara katmanlardan farklı olarak, her uygulama sadece bir istisna işleme sınıfı yapılandırabilir.

> Yukarıdaki istisna işleme sınıfları gerçekte mevcut olmayabilir, burada sadece farklı uygulamalar için istisna işleme nasıl yapılandırılacağına dair bir örnek sunulmuştur.

İstisna işleme geliştirme kılavuzu [İstisna İşleme Bölümü](exception.md)'nde bulunmaktadır.
