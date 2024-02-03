# nginxプロキシ
webmanが直接インターネットアクセスを提供する必要がある場合、webmanの前にnginxプロキシを追加することをお勧めします。これにより、以下の利点があります。

- 静的リソースはnginxによって処理され、webmanはビジネスロジックの処理に専念できます
- 複数のwebmanが80、443ポートを共有し、ドメイン名で異なるサイトを区別し、単一のサーバーで複数のサイトを展開できます
- php-fpmとwebmanアーキテクチャの共存が可能
- nginxプロキシによるssl実装により、httpsがより簡単で効率的に実現できます
- 外部からの不正なリクエストを厳密にフィルタリングできます

## nginxプロキシの例
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
    server_name サイトのドメイン名;
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

通常、開発者は上記の設定でserver_nameとrootを実際の値に構成するだけでよく、他のフィールドを構成する必要はありません。
