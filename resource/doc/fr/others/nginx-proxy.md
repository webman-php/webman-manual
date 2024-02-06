# Proxy nginx

Lorsque webman doit être directement accessible depuis l'Internet, il est recommandé d'ajouter un proxy nginx devant webman pour les avantages suivants :

- Les ressources statiques sont gérées par nginx, permettant à webman de se concentrer sur le traitement de la logique métier.
- Permet à plusieurs webman de partager les ports 80 et 443, en différenciant les sites par nom de domaine pour déployer plusieurs sites sur un seul serveur.
- Peut permettre la coexistence de php-fpm et de l'architecture webman.
- La mise en place du SSL via le proxy nginx est simple et efficace pour la sécurisation en HTTPS.
- Permet de filtrer strictement certaines requêtes non valides provenant de l'Internet.

## Exemple de proxy nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name Nom_de_domaine_du_site;
  listen 80;
  access_log off;
  root /votre/repertoire_webman/public;

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

En général, les développeurs n'ont besoin que de configurer le server_name et root avec les valeurs réelles, les autres champs ne nécessitent pas de configuration.
