# nginx proxy
webman dış ağ erişimini sağlamanız gerektiğinde, webman önüne bir nginx proxy eklemenizi öneririz, bunun birkaç avantajı vardır.

- Statik kaynakları nginx'e bırakarak, webman'ın iş mantığına odaklanmasını sağlamak
- Birden çok webman'ın 80, 443 portunu paylaşmasına izin vermek, alan adı ile farklı siteleri tek sunucuda dağıtmak
- php-fpm ve webman mimarisinin birlikte var olmasını sağlamak
- nginx proxy ile ssl sertifikası alarak https'i basit ve verimli hale getirmek
- Dış ağdaki bazı geçersiz istekleri sıkı bir şekilde filtreleyebilmek

## nginx proxy örneği
```nginx
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

Genellikle, geliştiriciler yukarıdaki yapılandırmada yalnızca server_name ve root'u gerçek değerlere ayarlamaları gerekir, diğer alanlar yapılandırılmayabilir.
