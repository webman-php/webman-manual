# Прокси сервер Nginx
Когда webman нуждается в прямом доступе из внешней сети, рекомендуется добавить прокси сервер Nginx перед webman, чтобы получить следующие преимущества:

- Статические ресурсы обрабатываются Nginx, позволяя webman сосредоточиться на обработке бизнес-логики
- Разрешить нескольким webman использовать порты 80 и 443 для различных сайтов, отделенных по доменным именам, что позволяет развертывать несколько сайтов на одном сервере
- Возможность совместного использования архитектуры php-fpm и webman
- Прокси сервер Nginx обеспечивает простую и эффективную настройку SSL для реализации протокола HTTPS
- Строгая фильтрация некорректных запросов из внешней сети

## Пример настройки прокси сервера Nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name domain_name; 
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

Обычно разработчику достаточно указать реальные значения для настройки server_name и root, остальные поля не требуют настройки.
