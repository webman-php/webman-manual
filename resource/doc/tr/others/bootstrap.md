# İşletme Başlatma

Bazen bir işlem başlatıldıktan sonra işletme başlatma işlemleri yapmamız gerekebilir. Bu başlatma işlemi, işlem ömrü boyunca sadece bir kez çalışacaktır. Örneğin, işlem başlatıldıktan sonra bir zamanlayıcı ayarlamak veya veritabanı bağlantısını başlatmak gibi. Aşağıda bu konuya dair bir açıklama bulunmaktadır.

## Prensip
**[İşlem Akışı](process.md)** kısmında belirtildiği gibi, webman işlem başladıktan sonra `config/bootstrap.php` (dahil olmak üzere `config/plugin/*/*/bootstrap.php`) dosyasında belirtilen sınıfları yükler ve sınıfların start metotlarını çalıştırır. Biz start metodu içerisine işletme kodlarını ekleyerek işlem başladıktan sonraki işletme başlatma işlemini gerçekleştirebiliriz.

## Süreç
Örneğin, işlem başladıktan sonra iç bellek kullanımını düzenli aralıklarla raporlamak için bir zamanlayıcı yapmak istediğimizi düşünelim. Bu sınıfa `MemReport` adını verdik.

#### Komutu Çalıştırın
`php webman make:bootstrap MemReport` komutunu çalıştırarak başlatma dosyası olan `app/bootstrap/MemReport.php` dosyasını oluşturun.

> **Not**
> Eğer webman'da `webman/console` yüklü değilse, `composer require webman/console` komutunu çalıştırarak yükleyin.

#### Başlatma Dosyasını Düzenle
`app/bootstrap/MemReport.php` dosyasını düzenleyerek aşağıdaki gibi bir içerik oluşturun:

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Bu komut satırı işletme ortamında mı çalışıyor?
        $is_console = !$worker;
        if ($is_console) {
            // Eğer işletme ortamında bu başlatma işlemini çalıştırmak istemiyorsanız, burada doğrudan çıkın
            return;
        }
        
        // Her 10 saniyede bir çalıştır
        \Workerman\Timer::add(10, function () {
            // Gösterim kolaylığı için, burada raporlama süreci yerine çıktı kullanıyoruz
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Not**
> Komut satırı kullanırken, çerçeve ayrıca `config/bootstrap.php` dosyasında belirtilen start metotlarını çalıştırır. İşletme başlatma kodlarını çalıştırıp çalıştırmayacağımızı belirlemek için `$worker`'ın null olup olmadığını kontrol edebiliriz.

#### İşlemle Birlikte Başlatma Dosyasını Yapılandırın
`config/bootstrap.php` dosyasını açarak `MemReport` sınıfını başlatma öğesine ekleyin.
```php
return [
    // ...Diğer yapılandırmalar burada kısaltılmıştır...

    app\bootstrap\MemReport::class,
];
```

Bu şekilde işletme başlatma sürecini tamamlamış oluruz.

## Ek Açıklamalar
[Özel İşlemler](../process.md) başlatıldığında da `config/bootstrap.php` dosyasında belirtilen start metotlarını çalıştırır. Hangi işlemin hangi işlem olduğunu belirlemek için `$worker->name` ile mevcut işlemi kontrol edebilir ve bu işlemlere özgü işletme başlatma kodlarını çalıştırıp çalıştırmamaya karar verebiliriz. Örneğin, monitor işlemine gözetim yapmak istemiyoruz, bu durumda `MemReport.php` içeriği aşağıdaki gibi olabilir:

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Bu komut satırı işletme ortamında mı çalışıyor?
        $is_console = !$worker;
        if ($is_console) {
            // Eğer işletme ortamında bu başlatma işlemini çalıştırmak istemiyorsanız, burada doğrudan çıkın
            return;
        }
        
        // monitor işlemi zamanlayıcıyı çalıştırmasın
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Her 10 saniyede bir çalıştır
        \Workerman\Timer::add(10, function () {
            // Gösterim kolaylığı için, burada raporlama süreci yerine çıktı kullanıyoruz
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
