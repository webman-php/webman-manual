# 慢业务处理

有时候我们需要处理慢业务，为了避免慢业务影响webman的其它请求处理，这些业务根据情况不同可以使用不同的处理方案。

## 使用消息队列
参考[redis队列](../queue/redis.md) [stomp队列](../queue/stomp.md)

### 优点
可以应对突发海量业务处理请求

### 缺点
无法直接返回结果给客户端。如需推送结果需要配合其它服务，例如使用 [webman/push](https://www.workerman.net/plugin/2) 推送处理结果。

## 新增HTTP端口

> **注意**
> 此特性需要webman-framework>=1.4

新增HTTP端口处理慢请求，这些慢请求通过访问这个端口进入特定的一组进程处理，处理后将结果直接返回给客户端。

### 优点
可以直接将数据返回给客户端

### 缺点
无法应对突发的海量请求

### 实施步骤
在 `config/process.php` 里增加如下配置。
```php
return [
    // ... 这里省略了其它配置 ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // 进程数
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'requestClass' => \support\Request::class, // request类设置
            'logger' => \support\Log::channel('default'), // 日志实例
            'appPath' => app_path(), // app目录位置
            'publicPath' => public_path() // public目录位置
        ]
    ]
];
```

这样慢接口可以走 `http://127.0.0.1:8686/` 这组进程，不影响其它进程的业务处理。

为了让前端无感知端口的区别，可以在nginx加一个到8686端口的代理。假设慢接口请求路径都是以`/tast`开头，整个nginx配置类似如下：
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# 新增一个8686 upstream
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # 以/tast开头的请求走8686端口，请按实际情况将/tast更改为你需要的前缀
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # 其它请求走原8787端口
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

这样客户端访问`域名.com/tast/xxx`时将会走单独的8686端口处理，不影响8787端口的请求处理。


