# İşyeri Başlatma

Bazen bir süreç başlatıldıktan sonra iş yerinde başlatma yapmamız gerekebilir. Bu başlatma, süreç yaşam döngüsünde yalnızca bir kez gerçekleşir, örneğin bir süreç başlatıldıktan sonra bir zamanlayıcı ayarlama veya veritabanı bağlantısını başlatma gibi. Aşağıda bunu açıklayacağız.

## İlke
**[Execute Process](process.md)** bölümünde belirtildiği gibi, webman süreç başlatıldıktan sonra `config/bootstrap.php` (bu, `config/plugin/*/*/bootstrap.php` içinde ayarlanmış sınıfları da içerir) içindeki sınıfları yükler ve sınıfın başlatma metodunu çalıştırır. Start metoduna iş yerinde kod ekleyerek işyeri başlatma işlemini tamamlayabiliriz.

## Akış
Örneğin, bir zamanlayıcı oluşturmamız ve belirli aralıklarla mevcut işlem belleğini raporlamak için, `MemReport` adında bir sınıf oluşturmak istediğimizi düşünelim.

#### Komutu çalıştırın

`php webman make:bootstrap MemReport` komutunu çalıştırarak başlatma dosyası `app/bootstrap/MemReport.php` dosyasını oluşturun.

> **İpucu**
> Eğer webman'ınızda `webman/console` yüklü değilse `composer require webman/console` komutunu çalıştırarak yükleyin.

#### Başlatma dosyasını düzenleyin
`app/bootstrap/MemReport.php` dosyasını düzenleyerek aşağıdaki gibi içeriğini düzenleyin:

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Komut satırı ortamı mı ?
        $is_console = !$worker;
        if ($is_console) {
            // Eğer komut satırı ortamında başlatma yapmak istemiyorsanız, burada doğrudan dönüş yapabilirsiniz
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

> **İpucu**
> Komut satırı kullanırken, çerçeve ayrıca `config/bootstrap.php` de konfigüre edilmiş başlatma metodunu çalıştıracak, iş yerini belirli bir koşulda çalıştırıp çalıştırmamanız gerektiğini belirlemek için `$worker` değişkeninin boş olup olmadığını kontrol edebilirsiniz.

#### Süreç başlatma ile yapılandırma
`config/bootstrap.php` dosyasını açarak `MemReport` sınıfını başlatma öğelerine ekleyin.
```php
return [
    // ...diğer yapılandırmalar burada kısaltıldı...
    
    app\bootstrap\MemReport::class,
];
```

Bu şekilde bir işyeri başlatma akışını tamamlamış oluruz.

## Ek Bilgi
[Custom Processes](../process.md) başlatıldıktan sonra `config/bootstrap.php` de yapılandırılmış başlatma metodunu da çalıştırır. Hangi sürecin şu anda hangi süreç olduğunu `$worker->name` üzerinden kontrol edebilir ve bu süreçte işyeri başlatma kodunuzu çalıştırıp çalıştırmamanız gerektiğine karar verebilirsiniz. Örneğin, monitor sürecini izlemek istemiyorsak, `MemReport.php` dosyası aşağıdakine benzer olabilir:

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Komut satırı ortamı mı ?
        $is_console = !$worker;
        if ($is_console) {
            // Eğer komut satırı ortamında başlatma yapmak istemiyorsanız, burada doğrudan dönüş yapabilirsiniz
            return;
        }
        
        // monitor süreci zamanlayıcıyı çalıştırmasın
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
