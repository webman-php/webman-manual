# nginx代理
当webman需要直接提供外网访问时，建议在webman前增加一个nginx代理，这样有以下好处。

 - 静态资源由nginx处理，让webman专注业务逻辑处理
 - 让多个webman共用80、443端口，通过域名区分不同站点，实现单台服务器部署多个站点
 - 能够实现php-fpm与webman架构共存
 - nginx代理ssl实现https，更加简单高效
 - 能够严格过滤外网一些不合法请求

## nginx代理示例
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name 站点域名;
  listen 80;
  access_log off;
  root /your/webman/public;

  location / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_set_header X-Forwarded-Proto $scheme;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```
