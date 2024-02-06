# Proxy di Nginx
Quando webman ha bisogno di essere accessibile direttamente dalla rete esterna, è consigliabile inserire un proxy Nginx di fronte a webman, con i seguenti vantaggi.

- Le risorse statiche vengono gestite da Nginx, consentendo a webman di concentrarsi sulle logiche di business
- Consentire a più istanze di webman di condividere le porte 80 e 443, distinguendo i diversi siti Web tramite i nomi di dominio, consentendo così la distribuzione di più siti su un singolo server
- Possibilità di coesistenza di PHP-FPM e architettura webman
- Utilizzo di Nginx per la gestione di SSL e implementazione di HTTPS in modo semplice ed efficiente
- Capacità di filtrare rigorosamente alcune richieste non valide provenienti dalla rete esterna

## Esempio di proxy Nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name nome_dominio_del_sito;
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

Di solito, i developer devono solamente configurare i valori effettivi per `server_name` e `root`, mentre gli altri campi non necessitano di configurazione.
