# nginx代理
當webman需要直接提供外網訪問時，建議在webman前增加一個nginx代理，這樣有以下好處。

- 靜態資源由nginx處理，讓webman專注業務邏輯處理
- 讓多個webman共用80、443端口，通過域名區分不同站點，實現單台伺服器部署多個站點
- 能夠實現php-fpm與webman架構共存
- nginx代理ssl實現https，更加簡單高效
- 能夠嚴格過濾外網一些不合法請求

## nginx代理示例
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name 站點域名;
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

一般來說以上配置開發者只需要將server_name和root配置成實際值即可，其它字段不需要配置。
