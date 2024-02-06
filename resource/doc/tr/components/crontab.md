# crontab zamanlanmış görev bileşeni

## workerman/crontab

### Açıklama

`workerman/crontab`, linux'un crontab'ına benzer, fakat `workerman/crontab` saniye seviyesinde zamanlanmış işlemleri destekler.

Zaman açıklaması:

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ haftanın günü (0 - 6) (Pazar=0)
|   |   |   |   +------ ay (1 - 12)
|   |   |   +-------- ayın günü (1 - 31)
|   |   +---------- saat (0 - 23)
|   +------------ dakika (0 - 59)
+-------------- saniye (0-59)[istedeğinizde atlanabilir, eğer 0 değilse, minimum zaman bir dakikadır]
```

### Proje bağlantısı

https://github.com/walkor/crontab

### Kurulum

```php
composer require workerman/crontab
```

### Kullanım

**Adım 1: İşlem dosyası oluşturun `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Her saniye çalıştır
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Her 5 saniyede bir çalıştır
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Her dakikada bir çalıştır
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Her 5 dakikada bir çalıştır
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Her dakikanın ilk saniyesinde çalıştır
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Günde bir defa 7:50'de çalıştır, burada saniye kısmı atlanmıştır
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
``` 

**Adım 2: İşlem dosyasını webman başlatma ile yapılandırın**

`config/process.php` dosyasını açın ve aşağıdaki yapılandırmayı ekleyin

```php
return [
    ....Diğer yapılandırmalar, burası atlandı....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
  
**Adım 3: webman'i yeniden başlatın**

> Not: Zamanlanmış görevler hemen çalışmaz, tüm zamanlanmış görevler bir sonraki dakikaya girerken çalışmaya başlar.

### Açıklama
crontab asenkron değildir, örneğin bir görev işlemi A ve B iki zamanlanmış zamanlayıcı ayarlamış olsun, her ikisi de her saniye bir kez görevi çalıştırır, ancak A görevi 10 saniye sürerse, B'nin çalışması için A'nın tamamlanmasını beklemesi gerekir, bu da B'nin gecikmeli çalışmasına neden olur.
Eğer bir işlem zaman aralığına karşı hassas ise, hassas zamanlanmış görevleri diğerlerinden etkilenmemesi için ayrı bir işleme koymak gereklidir. Örneğin, `config/process.php` dosyasında aşağıdaki yapılandırmayı yapabilirsiniz

```php
return [
    ....Diğer yapılandırmalar, burası atlandı....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Zaman açısından hassas olan zamanlanmış görevleri `process/Task1.php` dosyasına, diğer zamanlanmış görevleri `process/Task2.php` dosyasına koyun

### Daha fazlası
Daha fazla `config/process.php` yapılandırma açıklaması için [Özel İşlem](../process.md) bölümüne bakınız.
