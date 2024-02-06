# Traitement des requêtes lentes

Parfois, nous devons traiter des requêtes lentes afin d'éviter que celles-ci n'affectent le traitement des autres requêtes par webman. Selon les circonstances, ces requêtes peuvent être traitées de différentes manières.

## Utilisation de files d'attente

Consultez [la file d'attente Redis](https://www.workerman.net/plugin/12) et [la file d'attente Stomp](https://www.workerman.net/plugin/13).

### Avantages
Permet de faire face à des demandes de traitement massives soudaines.

### Inconvénients
Impossible de retourner directement les résultats au client. Pour pousser les résultats, il est nécessaire de collaborer avec d'autres services, tels que l'utilisation de [webman/push](https://www.workerman.net/plugin/2).

## Ajout de port HTTP

> **Remarque**
> Cette fonctionnalité requiert webman-framework>=1.4

Un nouveau port HTTP est ajouté pour le traitement des requêtes lentes. Ces requêtes lentes sont gérées par un groupe spécifique de processus en accédant à ce port, et les résultats sont renvoyés directement au client après traitement.

### Avantages
Possibilité de retourner directement les données au client.

### Inconvénients
Impossible de faire face à des demandes massives soudaines.

### Étapes de mise en œuvre
Ajoutez la configuration suivante dans `config/process.php`.
```php
return [
    // ... D'autres configurations ici ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Nombre de processus
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Configuration de la classe de requête
            'logger' => \support\Log::channel('default'), // Instance de journal
            'app_path' => app_path(), // Emplacement du répertoire app
            'public_path' => public_path() // Emplacement du répertoire public
        ]
    ]
];
```

Ainsi, les interfaces lentes peuvent utiliser ce groupe de processus via `http://127.0.0.1:8686/` sans affecter le traitement des autres processus.

Pour rendre transparente la différence de port pour le frontend, vous pouvez ajouter un proxy vers le port 8686 dans nginx. Supposons que les chemins des requêtes d'interfaces lentes commencent tous par `/tast`, la configuration complète de nginx ressemblerait à ceci :
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Ajouter un upstream 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Les requêtes commençant par /tast vont à 8686, veuillez adapter /tast selon vos besoins réels
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Les autres requêtes vont à l'origine du port 8787
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

Ainsi, lorsque le client accède à `domaine.com/tast/xxx`, cela passera par le port 8686 dédié au traitement sans affecter le traitement des requêtes du port 8787.
