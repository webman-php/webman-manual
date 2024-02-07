# Nginx-Proxy
Wenn webman direkten Zugriff auf das Internet benötigt, wird empfohlen, einen Nginx-Proxy vor webman zu setzen, um folgende Vorteile zu erzielen.
- Statische Ressourcen werden von Nginx verarbeitet, damit sich webman auf die Geschäftslogik konzentrieren kann.
- Mehrere webman-Instanzen können den Port 80 und 443 gemeinsam nutzen und durch verschiedene Domains unterschieden werden, um mehrere Websites auf einem Server zu betreiben.
- PHP-FPM und webman-Architektur können zusammenarbeiten.
- Der Nginx-Proxy ermöglicht SSL über HTTPS, was einfacher und effizienter ist.
- Es ermöglicht die strikte Filterung einiger unerwünschter externer Anfragen.

## Nginx-Proxy-Beispiel
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name domain_der_website;
  listen 80;
  access_log off;
  root /dein/webman/öffentlich;

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
Im Allgemeinen muss der Entwickler in der obigen Konfiguration nur den server_name und root-Werte auf die tatsächlichen Werte setzen. Andere Felder müssen nicht konfiguriert werden.
