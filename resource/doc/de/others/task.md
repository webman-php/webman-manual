# Langsamer Geschäftsprozess

Manchmal müssen wir langsame Geschäftsprozesse behandeln, um zu vermeiden, dass diese die Verarbeitung anderer Anfragen in Webman beeinträchtigen. Je nach Situation können diese Prozesse unterschiedliche Behandlungsmethoden erfordern.

## Verwendung von Warteschlangen
Siehe [Redis-Warteschlange](https://www.workerman.net/plugin/12) und [STOMP-Warteschlange](https://www.workerman.net/plugin/13)

### Vorteile
Flexible Reaktion auf plötzlich auftretende hohe Arbeitslastanfragen

### Nachteile
Kann Ergebnisse nicht direkt an den Client zurücksenden. Um Ergebnisse zu pushen, müssen Sie mit anderen Diensten zusammenarbeiten, z. B. mit [webman/push](https://www.workerman.net/plugin/2), um die Verarbeitungsergebnisse zu pushen.

## Hinzufügen eines HTTP-Ports

> **Achtung**
> Diese Funktion erfordert webman-framework>=1.4

Ein neuer HTTP-Port wird hinzugefügt, um langsame Anfragen zu verarbeiten. Diese Anfragen werden über den Zugriff auf diesen Port von einer spezifischen Gruppe von Prozessen verarbeitet, und die Ergebnisse werden direkt an den Client zurückgesendet.

### Vorteile
Die Daten können direkt an den Client zurückgegeben werden.

### Nachteile
Kann nicht auf plötzlich auftretende hohe Anfragenlast reagieren.

### Implementierungsschritte
Fügen Sie in `config/process.php` die folgende Konfiguration hinzu.
```php
return [
    // ... andere Konfiguration hier ausgelassen ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Anzahl der Prozesse
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Einstellung der Anforderungsklasse
            'logger' => \support\Log::channel('default'), // Logger-Instanz
            'app_path' => app_path(), // Position des app-Verzeichnisses
            'public_path' => public_path() // Position des public-Verzeichnisses
        ]
    ]
];
```

Auf diese Weise können langsame Schnittstellen diesen Satz Prozesse über `http://127.0.0.1:8686/` durchlaufen, ohne die Verarbeitung anderer Prozesse zu beeinträchtigen.

Um sicherzustellen, dass der Frontend-Client keinen Unterschied zwischen den Ports bemerkt, können Sie einen Proxy zum Port 8686 in nginx hinzufügen. Angenommen, die Pfade der langsamen Schnittstellenanfragen beginnen alle mit `/tast`. Eine ähnliche gesamte nginx-Konfiguration könnte wie folgt aussehen:
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Hinzufügen eines neuen 8686-Upstreams
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Anfragen, die mit /tast beginnen, werden an den Port 8686 weitergeleitet. Passen Sie /tast entsprechend Ihren Anforderungen an.
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Andere Anfragen werden an den ursprünglichen Port 8787 weitergeleitet
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

Auf diese Weise werden Anfragen, die mit `domain.com/tast/xxx` beginnen, über den separaten Port 8686 verarbeitet, ohne die Verarbeitung von Anfragen über Port 8787 zu beeinträchtigen.
