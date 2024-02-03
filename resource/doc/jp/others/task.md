# 遅い業務処理

時には遅い業務を処理する必要がありますが、遅い業務がwebmanの他のリクエスト処理に影響を与えないようにするために、これらの業務は状況に応じて異なる処理方法を使用できます。

## メッセージキューを使用する
[redisキュー](https://www.workerman.net/plugin/12) [stompキュー](https://www.workerman.net/plugin/13) を参照してください。

### 利点
急な大量の業務処理リクエストに対応できます。

### 欠点
クライアントに直接結果を返すことはできません。結果をプッシュする場合は、[webman/push](https://www.workerman.net/plugin/2) を使用して結果をプッシュする必要があります。

## 追加のHTTPポート

> **注意**
> この機能にはwebman-framework>=1.4が必要です。

遅いリクエストを処理するためにHTTPポートを追加し、これらの遅いリクエストはこのポートにアクセスして特定の一連のプロセスで処理され、処理後に結果が直接クライアントに返されます。

### 利点
データをクライアントに直接返すことができます。

### 欠点
急な大量のリクエストには対応できません。

### 実施手順
`config/process.php` に以下の設定を追加します。
```php
return [
    // ... 他の設定はここで省略 ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // プロセス数
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // リクエストクラスの設定
            'logger' => \support\Log::channel('default'), // ログのインスタンス
            'app_path' => app_path(), // アプリケーションディレクトリの位置
            'public_path' => public_path() // パブリックディレクトリの位置
        ]
    ]
];
```

これにより遅いインターフェイスは、`http://127.0.0.1:8686/` を通じてこのグループのプロセスを実行し、他のプロセスの業務処理に影響を与えません。

フロントエンドがポートの違いに気付かないようにするために、nginxに8686ポートへのプロキシを追加できます。遅いインターフェイスのリクエストパスがすべて`/tast`で始まると仮定すると、nginxの全体的な構成は以下のようになります：
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

  # /tastで始まるリクエストは8686ポートを通ります。実際の状況に応じて/tastを必要なプレフィックスに変更してください
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # その他のリクエストは元の8787ポートを通ります
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

これにより、クライアントは`ドメイン.com/tast/xxx`にアクセスすると、独自の8686ポートを通じて処理され、8787ポートのリクエスト処理に影響を与えません。
