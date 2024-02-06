# Gestione delle attività lente

A volte è necessario gestire attività lente in modo da evitare che esse influenzino l'elaborazione delle altre richieste da parte di webman, e tali attività possono essere gestite con diversi metodi a seconda delle circostanze.

## Utilizzo di code di messaggi
Fare riferimento a [coda Redis](https://www.workerman.net/plugin/12) e [coda STOMP](https://www.workerman.net/plugin/13)

### Vantaggi
È in grado di gestire le richieste di elaborazione di enormi quantità di attività improvvisamente.

### Svantaggi
Non può restituire direttamente i risultati al client. Se è necessario inviare i risultati, è necessario coordinare con altri servizi, ad esempio utilizzando [webman/push](https://www.workerman.net/plugin/2) per inviare i risultati del processo.

## Aggiunta di una porta HTTP

> **Nota**
> Questa funzionalità richiede webman-framework>=1.4

Aggiungere una porta HTTP per gestire le richieste lente, consentendo a queste ultime di essere elaborate tramite l'accesso a questa porta da parte di un insieme specifico di processi, che restituiranno direttamente i risultati al client una volta elaborati.

### Vantaggi
È possibile restituire direttamente i dati al client.

### Svantaggi
Non in grado di gestire richieste di enormi quantità improvvisamente.

### Procedura di implementazione
Nel file `config/process.php`, aggiungere la seguente configurazione.
```php
return [
    // ... Altre configurazioni omesse ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Numero di processi
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Impostazioni classe della richiesta
            'logger' => \support\Log::channel('default'), // Istanza di log
            'app_path' => app_path(), // Posizione della directory app
            'public_path' => public_path() // Posizione della directory pubblica
        ]
    ]
];
```

In questo modo, le richieste lente possono essere gestite tramite questo gruppo di processi tramite `http://127.0.0.1:8686/`, senza influenzare l'elaborazione delle altre attività da parte degli altri processi.

Per rendere trasparente al frontend la differenza tra le porte, è possibile aggiungere un proxy per la porta 8686 in nginx. Supponendo che i percorsi delle richieste lente inizino con `/tast`, la configurazione completa di nginx sarà simile a quanto segue:
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Aggiungere un nuovo upstream per la porta 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Le richieste che iniziano con /tast passano alla porta 8686, si prega di modificare /tast in base alle esigenze effettive
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Le altre richieste passano alla porta originale 8787
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

In questo modo, quando il client accede a `dominio.com/tast/xxx`, verrà gestita tramite la porta 8686 in modo separato e non influenzerà l'elaborazione delle richieste sulla porta 8787.
