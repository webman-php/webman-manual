# Özel İşlemler

webman'da workerman gibi özel dinleyici veya işlemler oluşturabilirsiniz.

> **Not**
> Windows kullanıcıları ancak `php windows.php` kullanarak webman'ı başlatarak özel işlemleri çalıştırabilir.

## Özel HTTP Sunucusu
Webman HTTP hizmetinin çekirdek kodunu değiştirmeniz gereken özel gereksinimleriniz olabilir, bu durumda özel işlemlerle bu görevi gerçekleştirebilirsiniz.

Örneğin, app\Server.php adında yeni bir dosya oluşturun

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Webman\App içindeki yöntemleri burada yeniden yazın
}
```

`config/process.php` dosyasına aşağıdaki yapıyı ekleyin

```php
use Workerman\Worker;

return [
    // ... Diğer yapılandırmalar burada atlandı...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // İşlem sayısı
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // istek sınıfı ayarı
            'logger' => \support\Log::channel('default'), // günlük örneği
            'app_path' => app_path(), // app dizini konumu
            'public_path' => public_path() // public dizini konumu
        ]
    ]
];
```

> **İpucu**
> Webman'ın kendi HTTP işlemini kapatmak istiyorsanız, config/server.php dosyasında `listen=>''` olarak ayarlayın.

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
> Not: Tüm onXXX özellikleri public'dir

`config/process.php` dosyasına aşağıdaki yapıyı ekleyin
```php
return [
    // ... Diğer işlem yapılandırmaları atlandı ...
    
    // websocket_test işlem adıdır
    'websocket_test' => [
        // Burada işlem sınıfını belirtin, yukarıda tanımlanan Pusher sınıfıdır
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Özel Olmayan Dinleme İşlemi Örneği
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
        // Her 10 saniyede bir veritabanını kontrol et, yeni bir kullanıcı kaydı var mı
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` dosyasına aşağıdaki yapıyı ekleyin
```php
return [
    // ... Diğer işlem yapılandırmaları atlandı...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Not: listen atlandığında herhangi bir bağlantı dinlenmez, count atlandığında işlem sayısı varsayılan olarak 1'dir.

## Yapılandırma Dosyası Açıklaması

Bir işlemin tam yapılandırma tanımı aşağıdaki gibidir:
```php
return [
    // ... 
    
    // websocket_test işlem adıdır
    'websocket_test' => [
        // Burada işlem sınıfını belirtin
        'handler' => app\Pusher::class,
        // Dinleyici protokolü, IP ve bağlantı noktası (isteğe bağlı)
        'listen'  => 'websocket://0.0.0.0:8888',
        // İşlem sayısı (isteğe bağlı, varsayılan 1)
        'count'   => 2,
        // İşlemi çalıştırmak için kullanıcı (isteğe bağlı, varsayılan mevcut kullanıcı)
        'user'    => '',
        // İşlemi çalıştırmak için kullanıcı grubu (isteğe bağlı, varsayılan mevcut kullanıcı grubu)
        'group'   => '',
        // Geçerli işlem yeniden yüklenebilir mi? (isteğe bağlı, varsayılan true)
        'reloadable' => true,
        // reusePort açık mı? (isteğe bağlı, bu seçenek php>=7.0 gerektirir, varsayılan true)
        'reusePort'  => true,
        // taşıma (isteğe bağlı, ssl etkinleştirilmesi gerektiğinde ssl olarak ayarlayın, varsayılan tcp)
        'transport'  => 'tcp',
        // bağlam (isteğe bağlı, taşıma ssl ise sertifika yolunu aktarmak gereklidir)
        'context'    => [], 
        // İşlem sınıfı yapıcı işlem parametreleri, burada process\Pusher::class sınıfının yapıcı işlem parametreleri (isteğe bağlı)
        'constructor' => [],
    ],
];
```

## Özet
Webman'ın özel işlemleri aslında workerman'ın basit bir kaplamasıdır, yapılandırmayı ve iş mantığını ayırır ve workerman'ın `onXXX` geri çağırma yöntemlerini sınıf yöntemiyle gerçekleştirir, diğer kullanımları tamamen workerman ile aynıdır.
