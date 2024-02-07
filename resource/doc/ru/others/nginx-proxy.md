# Прокси nginx
Когда webman нужно предоставить прямой доступ из внешней сети, рекомендуется добавить прокси nginx перед webman, это имеет следующие преимущества.

- Статические ресурсы обрабатываются nginx, позволяя webman сосредоточиться на обработке бизнес-логики
- Позволяет нескольким webman использовать порты 80 и 443, разделяя их по доменным именам и реализуя развертывание нескольких сайтов на одном сервере
- Может совмещать архитектуры php-fpm и webman
- Прокси nginx обеспечивает ssl и реализует https, что делает процесс более простым и эффективным
- Может строго фильтровать некорректные запросы из внешней сети

## Пример прокси nginx
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name домен_сайта;
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

Как правило, разработчику нужно просто настроить значения server_name и root, а остальные поля не требуют настройки.
