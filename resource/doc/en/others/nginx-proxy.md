# Nginx Proxy

When webman requires direct access from the external network, it is recommended to add an nginx proxy in front of webman for the following benefits:

- Nginx handles static resources, allowing webman to focus on business logic processing.
- Enables multiple webmen to share ports 80 and 443, distinguishing different sites through domain names, and achieving the deployment of multiple sites on a single server.
- Enables coexistence of php-fpm and webman architecture.
- Nginx proxy facilitates SSL implementation for https, making it simpler and more efficient.
- Strictly filters out some illegal requests from the external network.

## Nginx Proxy Example
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

Generally, developers only need to configure the server_name and root with actual values, and the other fields do not need to be configured.
