# nginxProxy
When webman needs to provide direct extranet access, it is recommended to add an nginx proxy in front of webman, which has the following benefits。

 - Static resources are handled by nginx, let webman focus on business logic
 - Let multiple webmans share port 80 and 443, distinguish different sites by domain name, and deploy multiple sites on a single server
 - Ability to implement php-fpm to coexist with webman architecture
 - nginxProxy ssl implementation of https, more simple and efficient
 - Ability to strictly filter some illegitimate requests on the extranet

## nginxProxy example
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name Site Domain;
  listen 80;
  access_log off;
  root /your/webman/public;

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
