# Proxy di Nginx

Quando webman deve essere accessibile direttamente dall'esterno, è consigliabile aggiungere un proxy Nginx di fronte a webman, che offre i seguenti vantaggi:

- Le risorse statiche sono gestite da Nginx, consentendo a webman di concentrarsi sulle operazioni logiche del business.
- Consente a più istanze di webman di condividere le porte 80 e 443, distinguendo i diversi siti web attraverso i nomi di dominio, consentendo la distribuzione di più siti web su un singolo server.
- Possibilità di coesistenza dell'architettura php-fpm con webman.
- Implementa SSL tramite proxy Nginx per ottenere un protocollo HTTPS in modo più semplice ed efficiente.
- Consente di filtrare rigorosamente alcune richieste non valide provenienti dall'esterno.

## Esempio di proxy Nginx

```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name nome_del_sito;
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

Di solito, con la configurazione sopra, gli sviluppatori devono soltanto impostare i valori effettivi per server_name e root, senza necessità di configurare gli altri campi.
