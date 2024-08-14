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
  # 注意，这里一定是webman下的public目录，不能是webman根目录
  root /your/webman/public;

  location ^~ / {
      proxy_set_header Host $http_host;
      proxy_set_header X-Forwarded-For $remote_addr;
      proxy_set_header X-Forwarded-Proto $scheme;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }

  # 拒绝访问所有以 .php 结尾的文件
  location ~ \.php$ {
      return 404;
  }

  # 允许访问 .well-known 目录
  location ~ ^/\.well-known/ {
    allow all;
  }

  # 拒绝访问所有以 . 开头的文件或目录
  location ~ /\. {
      return 404;
  }

}
```

一般来说以上配置开发者只需要将server_name和root配置成实际值即可，其它字段不需要配置。

> **注意**
> 特别注意的是，root选项一定要配置成webman下的public目录，千万不要直接设置成webman目录，否则你的所有文件可能会被外网下载访问，包括数据库配置等敏感文件。
