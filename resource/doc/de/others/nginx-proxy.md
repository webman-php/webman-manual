# Nginx-Proxy
Wenn Webman direkten externen Zugriff bieten muss, wird empfohlen, einen Nginx-Proxy vor Webman zu platzieren, um folgende Vorteile zu erzielen.

- Statische Ressourcen werden von Nginx verarbeitet, damit sich Webman auf die Verarbeitung von Geschäftslogik konzentrieren kann.
- Mehrere Webman-Instanzen können den Port 80 und 443 gemeinsam nutzen und durch die Verwendung von Domainnamen verschiedene Websites auf einem einzigen Server bereitstellen.
- PHP-FPM und Webman können gemeinsam betrieben werden.
- Der Nginx-Proxy ermöglicht eine einfache und effiziente Implementierung von SSL für HTTPS.
- Es können unerwünschte Anfragen aus dem Internet streng gefiltert werden.

## Beispiel für Nginx-Proxy
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name site_domain;
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

Normalerweise muss der Entwickler in der obigen Konfiguration nur die Werte für `server_name` und `root` konfigurieren, die anderen Felder müssen nicht angepasst werden.
