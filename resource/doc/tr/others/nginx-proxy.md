# nginx proxy
Webman doğrudan dış ağ erişimine ihtiyaç duyduğunda, webman'ın önüne bir nginx proxy eklemenizi öneririz, böylece aşağıdaki faydaları olur.

- Statik kaynaklar nginx tarafından işlenir, webman iş mantığı işlemlerine odaklanır
- Birden çok webman'ın 80 ve 443 portlarını paylaşmasına izin verir, farklı siteleri alan adıyla ayırarak tek sunucuda çoklu site dağıtımını gerçekleştirir
- php-fpm ve webman mimarisinin birlikte çalışmasını sağlar
- Nginx proxy’nin ssl'yi https olarak gerçekleştirmesi daha basit ve verimlidir
- Dış ağda bazı geçersiz istekleri sıkı bir şekilde filtreleyebilir

## Nginx Proxy Örneği
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name site_domain;
  listen 80;
  access_log off;
  root /your/webman/public;

  location ^~ / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $http_host;
      proxy_set_header X-Forwarded-Proto $scheme;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

Genellikle, geliştiricilerin yukarıdaki yapılandırmada sadece server_name ve root değerlerini gerçek değerlere ayarlamaları gerekir, diğer alanların ayarlanmasına gerek yoktur.
