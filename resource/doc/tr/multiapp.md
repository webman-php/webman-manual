# Çoklu Uygulama
Bir proje bazen birden çok alt projeye ayrılabilir, örneğin bir alışveriş merkezi ana proje, alışveriş merkezi API arayüzü ve alışveriş merkezi yönetim paneli olmak üzere 3 alt projeye ayrılabilir ve hepsi aynı veritabanı yapılandırmasını kullanabilir.

webman size app dizinini şu şekilde planlamanıza izin verir:
```plaintext
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
`http://127.0.0.1:8787/shop/{controller}/{method}` adresine erişildiğinde, `app/shop/controller` dizinindeki denetleyici ve metoda erişilir.

`http://127.0.0.1:8787/api/{controller}/{method}` adresine erişildiğinde, `app/api/controller` dizinindeki denetleyici ve metoda erişilir.

`http://127.0.0.1:8787/admin/{controller}/{method}` adresine erişildiğinde, `app/admin/controller` dizinindeki denetleyici ve metoda erişilir.

webman'de app dizinini şu şekilde bile planlayabilirsiniz.
```plaintext
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
Bu durumda, `http://127.0.0.1:8787/{controller}/{method}` adresine erişildiğinde, `app/controller` dizinindeki denetleyici ve metoda erişilir. api veya admin ile başlayan yolda, ilgili dizindeki denetleyici ve metoda erişilir.

Çoklu uygulamalarda sınıf ad alanları `psr4`'e uygun olmalıdır, örneğin `app/api/controller/FooController.php` dosyası aşağıdaki gibi olabilir:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Çoklu Uygulama Orta Katmanı Yapılandırması
Bazen farklı uygulamalar için farklı orta katmanları yapılandırmak isteyebilirsiniz, örneğin `api` uygulaması belki bir CORS orta katmanına ihtiyaç duyarken, `admin` bir yönetici girişi kontrolü orta katmanına ihtiyaç duyar, bu durumda `config/midlleware.php` dosyası aşağıda olduğu gibi olabilir:
```php
return [
    // Genel orta katmanlar
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // api uygulaması orta katmanları
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // admin uygulaması orta katmanları
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Yukarıdaki orta katmanlar muhtemelen mevcut değildir, burada sadece uygulamalara göre nasıl orta katman yapılandırılacağını anlatan bir örnektir.

Orta katmanların yürütme sırası şu şekildedir: `Genel orta katmanlar` -> `Uygulama orta katmanları`.

Orta katman geliştirme için [Orta Katman Bölümü](middleware.md)'ne bakın.

## Çoklu Uygulama İstisna İşleme Yapılandırması
Aynı şekilde, farklı uygulamalar için farklı istisna işleme sınıfları yapılandırmak isteyebilirsiniz, örneğin `shop` uygulamasında bir istisna oluştuğunda kullanıcı dostu bir sayfa göstermek isteyebilirsiniz; `api` uygulamasında bir istisna oluştuğunda bir sayfa yerine bir JSON dizesi dönmek isteyebilirsiniz. Farklı uygulamalar için farklı istisna işleme sınıflarını yapılandırmak için `config/exception.php` dosyası aşağıda olduğu gibi olabilir:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Orta katmanlardan farklı olarak, her uygulama yalnızca bir istisna işleme sınıfı yapılandırabilir.

> Yukarıdaki istisna işleme sınıfları muhtemelen mevcut değildir, burada sadece uygulamalara göre nasıl istisna işleme yapılacağını gösteren bir örnektir.

İstisna işleme geliştirme için [İstisna İşleme Bölümü](exception.md)'ne bakın.
