# Gestione dei servizi lenti

A volte è necessario gestire dei servizi lenti al fine di evitare che influenzino il processo di gestione delle richieste di webman. A seconda delle circostanze, questi servizi possono essere gestiti utilizzando diverse soluzioni.

## Utilizzo di code di messaggi
Fare riferimento a [coda Redis](../queue/redis.md) e [coda Stomp](../queue/stomp.md)

### Vantaggi
Può gestire le richieste di elaborazione dei servizi in modo efficiente durante picchi improvvisi di attività.

### Svantaggi
Non può fornire direttamente i risultati al client. Se è necessario inviare i risultati, è necessario utilizzare altri servizi, ad esempio utilizzando [webman/push](https://www.workerman.net/plugin/2) per inviare i risultati dell'elaborazione.

## Aggiunta di una porta HTTP

> **Nota**
> Questa funzionalità richiede webman-framework>=1.4

Aggiungendo una porta HTTP è possibile gestire le richieste lente, le quali, una volta ricevute su questa porta, vengono gestite da un insieme specifico di processi e i risultati vengono restituiti direttamente al client.

### Vantaggi
È possibile restituire direttamente i dati al client.

### Svantaggi
Non è in grado di gestire picchi improvvisi di richieste.

### Procedura di implementazione
Aggiungere la seguente configurazione a `config/process.php`.
```php
return [
    // ... Altre configurazioni sono omesse qui ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Numero di processi
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Impostazioni della classe di richiesta
            'logger' => \support\Log::channel('default'), // Istanza del registro
            'app_path' => app_path(), // Posizione della directory dell'applicazione
            'public_path' => public_path() // Posizione della directory pubblica
        ]
    ]
];
```

In questo modo, le richieste lente possono essere gestite da questo gruppo di processi su `http://127.0.0.1:8686/`, senza influenzare l'elaborazione dei processi.

Per rendere impercettibile la differenza delle porte per il front-end, è possibile configurare un proxy verso la porta 8686 in nginx. Ad esempio, se i percorsi delle richieste dei servizi lenti iniziano con `/tast`, la configurazione completa di nginx sarà simile alla seguente:
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Aggiunta di un nuovo upstream per la porta 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Le richieste che iniziano con /tast vengono instradate alla porta 8686, sostituire /tast con il prefisso desiderato
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Le altre richieste vengono instradate alla porta 8787
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

In questo modo, quando il cliente visita `dominio.com/tast/xxx`, la richiesta verrà instradata alla porta 8686 per essere elaborata, senza influenzare l'elaborazione delle richieste sulla porta 8787.
