# Proxy Nginx

Lorsque webman a besoin d'être directement accessible depuis Internet, il est conseillé d'ajouter un proxy Nginx en amont de webman, ce qui présente plusieurs avantages :

- Les ressources statiques sont gérées par Nginx, permettant à webman de se concentrer sur le traitement de la logique métier.
- Plusieurs instances de webman peuvent partager les ports 80 et 443, en différenciant les sites via les noms de domaine, ce qui permet le déploiement de plusieurs sites sur un seul serveur.
- Possibilité de coexistence de php-fpm et de l'architecture webman.
- Le proxy Nginx permet de mettre en place plus facilement et efficacement SSL pour le protocole https.
- Possibilité de filtrer strictement certaines requêtes externes non valides.

## Exemple de proxy Nginx
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name nom_de_domaine_du_site;
  listen 80;
  access_log off;
  root /votre/dossier/webman/public;

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

En général, les développeurs n'ont besoin de configurer que les valeurs réelles pour server_name et root, les autres champs ne nécessitent pas de configuration.
