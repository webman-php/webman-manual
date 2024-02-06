# Yavaş İşlem İşleme

Bazı durumlarda yavaş işlemleri işlememiz gerekebilir. Webman'ın diğer istekleri işlemesini etkilememek için, bu işlemler farklı işleme yöntemleri kullanılarak işlenebilir.

## Mesaj Kuyruğu Kullanımı
[Redis kuyruğu](https://www.workerman.net/plugin/12) [stomp kuyruğu](https://www.workerman.net/plugin/13) konusuna bakın.

### Avantajları
Beklenmedik büyük miktarda işleme isteği ile başa çıkabilir.

### Dezavantajları
Sonuçları doğrudan müşteriye geri döndüremez. Sonuçları iletmek için başka bir servisle, örneğin [webman/push](https://www.workerman.net/plugin/2) kullanarak sonuçları göndermek gereklidir.

## Yeni HTTP Portu Ekleme

> **Not**
> Bu özellik için webman-framework>=1.4 gereklidir.

Yavaş istekleri işlemek için yeni bir HTTP portu ekleyerek, bu yavaş istekler belirli bir grup işlem ardından sonuçları doğrudan müşteriye geri döndürebilir.

### Avantajları
Verileri doğrudan müşteriye geri döndürebilme

### Dezavantajları
Beklenmedik büyük miktarda isteğe cevap verememe

### Uygulama Adımları
`config/process.php` içerisine aşağıdaki yapılandırmayı ekleyin.
```php
return [
    // ... diğer yapılandırmalar burada...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // İşlem sayısı
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // talep sınıfı ayarları
            'logger' => \support\Log::channel('default'), // günlük örneği
            'app_path' => app_path(), // uygulama dizin konumu
            'public_path' => public_path() // genel dizin konumu
        ]
    ]
];
```

Bu şekilde, yavaş arabirimler `http://127.0.0.1:8686/` bu grup işleme ile çalışır ve diğer işlemleri etkilemez.

Ön uçtan port farkındalığını gidermek için, nginx'e 8686 portuna bir proxy ekleyebilirsiniz. Varsayalım ki yavaş arabirim istek yolları `/tast` ile başlıyor, bu durumda tüm nginx yapılandırması aşağıdaki gibi olacaktır:
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Yeni eklenen 8686 portu
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # /tast ile başlayan istekler 8686 portuna yönlendirilir, gerçek duruma göre /tast'ı isteğinizle değiştirmeniz gerekebilir
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Diğer istekler orijinal 8787 portuna yönlendirilir
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

Bu sayede müşteri, `domain.com/tast/xxx` URL'sine eriştiğinde özel olarak oluşturulmuş 8686 portunu kullanarak işlem yapacak ve 8787 portundaki istekleri etkilemeyecektir.
