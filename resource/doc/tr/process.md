# Özel Süreçler

webman'de, workerman'da olduğu gibi özel dinleme veya süreçler oluşturabilirsiniz.

> **Not**
> Windows kullanıcıları, özel bir süreci başlatmak için webman'ı başlatmak için `php windows.php` kullanmalıdır.

## Özel HTTP Sunucusu
Bazı özel gereksinimleriniz olabilir ve webman HTTP sunucusunun çekirdek kodunu değiştirmeniz gerekebilir, bu durumda özel süreçleri kullanabilirsiniz.

Örneğin, yeni bir tane oluşturun app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Bu, Webman\App içindeki yöntemleri yeniden yazmaktadır.
}
```

`config/process.php` içine aşağıdaki yapılandırmayı ekleyin

```php
use Workerman\Worker;

return [
    // ... Diğer yapılandırmalar buraya bırakılır...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Süreç sayısı
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // İstek sınıfı ayarı
            'logger' => \support\Log::channel('default'), // Günlük örneği
            'app_path' => app_path(), // app dizini konumu
            'public_path' => public_path() // public dizini konumu
        ]
    ]
];
```

> **İpucu**
> Webman'ın kendi HTTP sürecini kapatmak istiyorsanız, sadece config/server.php içinde `listen=>''` ayarını yapmanız yeterlidir.

## Özel Websocket Dinleyici Örneği

`app/Pusher.php` dosyasını oluşturun
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> Not: onXXX özellikleri hepsi public olarak tanımlanmalıdır.

`config/process.php` içine aşağıdaki yapılandırmayı ekleyin
```php
return [
    // ... Diğer süreç yapılandırmaları buraya bırakılır...
    
    // websocket_test sürecinin adı
    'websocket_test' => [
        // Burada süreç sınıfını belirtin, yukarıda tanımlanan Pusher sınıfıdır
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Özel Olmayan Dinleme Süreci Örneği
`app/TaskTest.php` dosyasını oluşturun
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Her 10 saniyede bir veritabanını kontrol et, yeni bir kullanıcı kaydı yapılmış mı?
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` içine aşağıdaki yapılandırmayı ekleyin
```php
return [
    // ... Diğer süreç yapılandırmaları buraya bırakılır...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Not: Dinleme ayarı yapılmadığında herhangi bir port dinlenmez, süreç sayısı belirtilmezse varsayılan olarak 1 olur.

## Yapılandırma Dosyası Açıklaması

Bir sürecin tam yapılandırma tanımı aşağıdaki gibidir:
```php
return [
    // ... 
    
    // websocket_test sürecinin adı
    'websocket_test' => [
        // Burada sürece sınıfını belirtin
        'handler' => app\Pusher::class,
        // Dinlenen protokol, ip ve port numarası (isteğe bağlı)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Süreç sayısı (isteğe bağlı, varsayılan 1)
        'count'   => 2,
        // Süreç çalıştırma kullanıcısı (isteğe bağlı, varsayılan mevcut kullanıcı)
        'user'    => '',
        // Süreç çalıştırma kullanıcı grubu (isteğe bağlı, varsayılan mevcut kullanıcı grubu)
        'group'   => '',
        // Mevcut sürecin reload işlemini destekleyip desteklemediği (isteğe bağlı, varsayılan true)
        'reloadable' => true,
        // reusePort seçeneğini etkinleştirmek isteyenler (isteğe bağlı, bu seçenek php> = 7.0 gerektirir, varsayılan true'dur)
        'reusePort'  => true,
        // taşıma (isteğe bağlı, ssl açmak gerektiğinde ssl olarak ayarlayın, varsayılan tcp'dir)
        'transport'  => 'tcp',
        // bağlam (isteğe bağlı, taşıma ssl olarak ayarlandığında sertifika yolunu geçirmeniz gerekir)
        'context'    => [], 
        // Süreç sınıfının yapılandırıcı işlev parametreleri, burada process\Pusher::class sınıfının yapılandırıcı parametreleri (isteğe bağlı)
        'constructor' => [],
    ],
];
```

## Sonuç
Webman'ın özel süreçleri aslında workerman'ın basitleştirilmiş bir sardır, bu, yapılandırmayı işleve ayırır ve workerman'ın `onXXX` geri aramasını sınıf yöntemleri aracılığıyla gerçekleştirir; diğer kullanımlar tamamen workerman ile aynıdır.
