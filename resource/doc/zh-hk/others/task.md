# 慢業務處理

有時候我們需要處理慢業務，為了避免慢業務影響webman的其他請求處理，這些業務根據情況不同可以使用不同的處理方案。

## 使用消息隊列
參考[redis隊列](../queue/redis.md) [stomp隊列](../queue/stomp.md)

### 優點
可以應對突發海量業務處理請求

### 缺點
無法直接返回結果給客戶端。如需推送結果需要配合其他服務，例如使用 [webman/push](https://www.workerman.net/plugin/2) 推送處理結果。

## 新增HTTP端口

> **注意**
> 此特性需要webman-framework>=1.4

新增HTTP端口處理慢請求，這些慢請求通過訪問這個端口進入特定的一組進程處理，處理後將結果直接返回給客戶端。

### 優點
可以直接將數據返回給客戶端

### 缺點
無法應對突發的海量請求

### 實施步驟
在 `config/process.php` 裡增加如下配置。
```php
return [
    // ... 這裡省略了其他配置 ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // 進程數
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // request類設置
            'logger' => \support\Log::channel('default'), // 日誌實例
            'app_path' => app_path(), // app目錄位置
            'public_path' => public_path() // public目錄位置
        ]
    ]
];
```

這樣慢接口可以走 `http://127.0.0.1:8686/` 這組進程，不影響其他進程的業務處理。

為了讓前端無感知端口的區別，可以在nginx加一個到8686端口的代理。假設慢接口請求路徑都是以`/tast`開頭，整個nginx配置類似如下：
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# 新增一個8686 upstream
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # 以/tast開頭的請求走8686端口，請按實際情況將/tast更改為你需要的前綴
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # 其他請求走原8787端口
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

這樣客戶端訪問`域名.com/tast/xxx`時將會走單獨的8686端口處理，不影響8787端口的請求處理。
