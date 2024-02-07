# Traitement des opérations lentes

Parfois, nous avons besoin de traiter des opérations lentes afin d'éviter qu'elles n'impactent le traitement d'autres requêtes dans webman. Selon les circonstances, ces opérations peuvent être traitées de différentes manières.

## Utilisation de files d'attente de messages
Voir [file d'attente Redis](../queue/redis.md) et [file d'attente Stomp](../queue/stomp.md)

### Avantages
Capacité à gérer des demandes de traitement soudaines et massives

### Inconvénients
Impossible de retourner directement les résultats au client. Si la diffusion des résultats est nécessaire, elle doit être associée à d'autres services, tels que l'utilisation de [webman/push](https://www.workerman.net/plugin/2) pour diffuser les résultats du traitement.

## Ajout d'un port HTTP

> **Remarque**
> Cette fonctionnalité nécessite webman-framework>=1.4

Ajoute un port HTTP pour le traitement des requêtes lentes. Ces requêtes lentes sont dirigées vers un ensemble spécifique de processus via l'accès à ce port, et les résultats du traitement sont renvoyés directement au client.

### Avantages
Capacité à renvoyer directement les données au client

### Inconvénients
Impossible de gérer des demandes soudaines et massives

### Étapes de mise en œuvre
Ajoutez la configuration suivante dans `config/process.php`.
```php
return [
    // ... autres configurations omises ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // nombre de processus
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // configuration de la classe de demande
            'logger' => \support\Log::channel('default'), // instance de journal
            'app_path' => app_path(), // emplacement du répertoire d'application
            'public_path' => public_path() // emplacement du répertoire public
        ]
    ]
];
```

De cette manière, les requêtes lentes peuvent être routées vers ce groupe de processus via `http://127.0.0.1:8686/`, sans affecter le traitement des autres processus.

Pour que le client ne perçoive pas la différence de port, vous pouvez ajouter une directive de proxy vers le port 8686 dans nginx. Supposons que les chemins des requêtes lentes commencent tous par `/tast`, la configuration nginx complète serait similaire à ce qui suit :
```nginx
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

  # Les requêtes commençant par /tast sont dirigées vers le port 8686, veuillez remplacer /tast par le préfixe nécessaire selon la situation
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Les autres requêtes passent par le port 8787 d'origine
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

Ainsi, lorsque le client accède à `domaine.com/tast/xxx`, la requête est dirigée vers le port 8686 distinct, sans affecter le traitement des requêtes sur le port 8787.
