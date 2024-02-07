# 遅延処理

時には、遅延処理を行う必要があります。Webmanの他のリクエスト処理に影響を与えないようにするために、これらのビジネスは異なるケースに応じて異なる処理方法を使用することができます。

## メッセージキューの使用
[Redisキュー](../queue/redis.md) [STOMPキュー](../queue/stomp.md)を参照してください

### 利点
突発的な大量のビジネス処理リクエストに対応できます

### 欠点
クライアントに直接結果を返すことはできません。結果をプッシュする必要がある場合は、[webman/push](https://www.workerman.net/plugin/2)を使用して結果をプッシュするなど、他のサービスと連携する必要があります。

## 新しいHTTPポートの追加

> **注意**
> この機能は、webman-framework>=1.4が必要です

新しいHTTPポートを追加して、遅いリクエストを処理します。これらの遅いリクエストは、このポートにアクセスすることで特定の一連のプロセスによって処理され、処理された後に結果がクライアントに直接返されます。

### 利点
データを直接クライアントに返すことができます

### 欠点
突発的な大量のリクエストに対応することができません

### 実施手順
`config/process.php`に以下の設定を追加します。
```php
return [
    // ... 他の設定は省略 ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // プロセス数
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // リクエストクラスの設定
            'logger' => \support\Log::channel('default'), // ログインスタンス
            'app_path' => app_path(), // appディレクトリの場所
            'public_path' => public_path() // publicディレクトリの場所
        ]
    ]
];
```

これにより、遅いインターフェースは`http://127.0.0.1:8686/`のプロセス群を通り、他のプロセスのビジネス処理に影響を与えません。

フロントエンドがポートの違いに気づかないようにするために、8686ポートへのリクエストを処理するためにnginxにプロキシを追加することができます。遅いインターフェースのリクエストパスが大体`/tast`で始まると仮定し、nginxの設定全体は次のようになります：

```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# 新しい8686 upstreamを追加
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # /tastで始まるリクエストは8686ポートに流す。必要に応じて/tastを必要なプレフィックスに変更してください
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # 他のリクエストは元の8787ポートに流す
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

これにより、クライアントは`域名.com/tast/xxx`にアクセスする際に、個別の8686ポートで処理されるようになり、8787ポートのリクエスト処理に影響を与えません。
