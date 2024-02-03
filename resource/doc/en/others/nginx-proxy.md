# Nginx Proxy
When webman needs to provide direct external access, it is recommended to add an nginx proxy in front of webman for the following benefits:

- Static resources are handled by nginx, allowing webman to focus on business logic processing.
- Multiple webman instances can share ports 80 and 443, and different sites can be distinguished by domain names to achieve multiple site deployment on a single server.
- Enables coexistence of php-fpm and webman architecture.
- Nginx proxy can implement SSL for https, making it simpler and more efficient.
- Can strictly filter out some illegal external requests.

## Nginx Proxy Example
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

In general, developers only need to configure the server_name and root with the actual values, and the other fields do not need to be configured.