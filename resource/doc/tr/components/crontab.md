# crontab zamanlanmış görev bileşeni

## workerman/crontab

### Açıklama

`workerman/crontab`, linux'un crontab'ına benzer, ancak `workerman/crontab` saniyelik zamanlamayı destekler.

Zaman açıklaması:

```plaintext
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ haftanın günü (0 - 6) (Pazar=0)
|   |   |   |   +------ ay (1 - 12)
|   |   |   +-------- ayın günü (1 - 31)
|   |   +---------- saat (0 - 23)
|   +------------ dakika (0 - 59)
+-------------- saniye (0-59)[isteğe bağlı, 0 pozisyonu olmadığında, minimum zaman bir dakikadır]
```

### Proje bağlantısı

https://github.com/walkor/crontab

### Kurulum

```php
composer require workerman/crontab
```

### Kullanım

**Adım 1: `process/Task.php` adlı yeni bir işlem dosyası oluşturun**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Her saniyede bir çalıştır
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
      
        // Günde bir kez saat 7:50'de çalıştır, burada saniye pozisyonu atlandı
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Adım 2: İşlem dosyasını webman'in başlatılmasıyla yapılandırın**
  
`config/process.php` dosyasını açın ve aşağıdaki yapılandırmayı ekleyin

```php
return [
    ....diğer yapılandırmalar burada atlanmıştır....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Adım 3: Webman'i yeniden başlatın**

> Not: Zamanlanmış görevler hemen çalıştırılmaz, tüm zamanlanmış görevler bir sonraki dakikada başlayacak şekilde zamanlanır

### Açıklama
crontab asenkron değildir, örneğin, bir işlem dosyasında A ve B olmak üzere iki zamanlayıcı ayarlanmış olsun, her ikisi de her saniye bir kez görevi çalıştırır, ancak A görevi 10 saniye sürerse, B'nin çalıştırılması için A'nın tamamlanmasını beklemesi gerekir, bu da B'nin gecikmeli olarak çalışmasına neden olur.
Zaman aralığı iş için hassas ise, hassas zamanlanmış görevi ayrı bir işlemde çalıştırmak için diğer zamanlanmış görevlerden etkilenmesini önlemek önemlidir. Örneğin, aşağıdaki gibi `config/process.php` dosyasına yapılandırma yapın

```php
return [
    ....diğer yapılandırmalar burada atlanmıştır....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Zaman hassasiyeti gerektiren zamanlanmış görevleri `process/Task1.php` dosyasına, diğer zamanlanmış görevleri `process/Task2.php` dosyasına yerleştirin.

### Daha fazlası
Daha fazla `config/process.php` yapılandırma açıklaması için, [Özel İşlem](../process.md) bağlantısına bakın
