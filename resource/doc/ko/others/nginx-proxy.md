# Nginx 프록시
webman이 직접 외부 액세스를 제공해야 하는 경우, webman 전에 Nginx 프록시를 추가하는 것이 좋습니다. 이렇게 하면 다음과 같은 이점이 있습니다.

- 정적 자원은 Nginx가 처리하여 webman이 비즈니스 로직 처리에 집중할 수 있습니다.
- 여러 개의 webman이 80번, 443번 포트를 공유하고, 도메인을 통해 서로 다른 사이트를 구분하여 단일 서버에 여러 사이트를 배포할 수 있습니다.
- php-fpm 및 webman 아키텍처의 공존을 실현할 수 있습니다.
- Nginx 프록시 SSL을 통해 https를 쉽고 효율적으로 구현할 수 있습니다.
- 외부에서 일부 불법 요청을 엄격히 걸러낼 수 있습니다.

## Nginx 프록시 예시
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name 사이트도메인;
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

일반적으로 개발자는 위의 구성에서 server_name 및 root를 실제 값으로 구성하기만 하면 되며, 다른 필드를 구성할 필요는 없습니다.
