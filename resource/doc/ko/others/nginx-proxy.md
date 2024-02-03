# nginx 프록시
웹맨이 외부 액세스를 직접 제공해야 할 때는, 웹맨 앞에 nginx 프록시를 추가하는 것이 좋습니다. 이렇게하면 다음과 같은 이점이 있습니다.

- 정적 자원은 nginx가 처리하여 웹맨이 비즈니스 로직 처리에 집중할 수 있습니다.
- 여러 개의 웹맨이 80, 443 포트를 공유하고, 도메인을 통해 서로 다른 사이트를 구분하여 단일 서버에 여러 사이트를 배포할 수 있습니다.
- php-fpm과 웹맨 아키텍처를 함께 사용할 수 있습니다.
- nginx 프록시를 사용하여 SSL을 통한 https를 간편하고 효율적으로 구현할 수 있습니다.
- 외부에서 일부 불법적인 요청을 엄격하게 필터링할 수 있습니다.

## nginx 프록시 예시
```
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

일반적으로 위의 구성에서 개발자는 server_name 및 root를 실제 값으로 구성하면 됩니다. 다른 필드는 구성할 필요가 없습니다.
