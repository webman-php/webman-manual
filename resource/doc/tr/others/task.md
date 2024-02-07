# Yavaş İşlem İşleme

Bazen yavaş işlemleri işlememiz gerekebilir. Diğer istekleri etkilememek için, bu işlemler farklı durumlara göre farklı işleme yöntemleri kullanılabilir.

## Mesaj Kuyruğu Kullanımı
Redis kuyruğu ve stomp kuyruğu için [redis kuyruk](../queue/redis.md) ve [stomp kuyruk](../queue/stomp.md) sayfalarına bakınız.

### Avantajlar
Ani büyük işlem isteklerine karşı kullanılabilir.

### Dezavantajlar
Sonuçları doğrudan müşteriye dönmek mümkün değildir. Sonuçları göndermek için başka hizmetlerle birlikte çalışması gerekmektedir. Örneğin [webman/push](https://www.workerman.net/plugin/2) sonuçları göndermek için kullanılabilir.

## Yeni HTTP Portu Ekleme

> **Not**
> Bu özellik webman-framework>=1.4 gerektirir.

Yavaş isteklerin doğrudan belirli bir grup işleme girdiği bir port eklemek için yeni bir HTTP portu eklenebilir. İşlem tamamlandıktan sonra sonuçları doğrudan müşteriye dönebilir.

### Avantajlar
Verileri doğrudan müşteriye dönebilir.

### Dezavantajlar
Ani büyük isteklere karşı kullanılamaz.

### Uygulama Adımları
`config/process.php` içine aşağıdaki yapılandırmayı ekleyin.
```php
return [
    // ... diğer yapılandırmalar burada kısaltıldı ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Süreç sayısı
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Request class ayarı
            'logger' => \support\Log::channel('default'), // Günlük örneği
            'app_path' => app_path(), // Uygulama dizini konumu
            'public_path' => public_path() // Genel dizin konumu
        ]
    ]
];
```

Bu şekilde yavaş arayüzlere, diğer işlemleri etkilemeden, bu süreç seti üzerinden `http://127.0.0.1:8686/` üzerinden gidebilir.

Ön uç tarafının port farkını fark etmemesi için, nginx üzerine 8686 portuna bir proxy ekleyebilirsiniz. Varsayalım, yavaş arayüz istek yolları genellikle `/tast` ile başlar, bu durumda nginx yapılandırması şu şekilde olacaktır:
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Yeni bir 8686 yukarıya doğru
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # /tast ile başlayan istekler 8686 portuna gider, lütfen /tast'ı ihtiyacınıza göre değiştirin
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Diğer istekler eski 8787 portuna gider
  location / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

Bu şekilde, müşteriler `domain.com/tast/xxx`'i ziyaret ettiğinde, özel olarak 8686 portunu kullanarak işlem yapacaktır ve 8787 portunun işlemesini etkilemeyecektir.
