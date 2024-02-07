# nginxプロキシ
webmanが外部アクセスを提供する必要がある場合、webmanの前にnginxプロキシを追加することをお勧めします。これには以下の利点があります。

- 静的リソースはnginxで処理され、webmanはビジネスロジックの処理に専念できます
- 複数のwebmanが80、443ポートを共有し、ドメインで異なるサイトを区別して単一のサーバーで複数のサイトを展開できます
- php-fpmとwebmanアーキテクチャを共存させることができます
- nginxプロキシによりSSLを介したhttpsが簡単かつ効率的に実現できます
- 外部からの不正なリクエストを厳密にフィルタリングできます

## nginxプロキシの例
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name サイトのドメイン;
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

通常、開発者は上記の設定でserver_nameとrootを実際の値に設定するだけで十分であり、他のフィールドは設定する必要はありません。
