# Langsamer Geschäftsverkehr

Manchmal müssen wir langsame Geschäftsaktivitäten bewältigen, um zu vermeiden, dass sie die Verarbeitung anderer Anfragen von webman beeinträchtigen. Je nach Bedarf können diese Aktivitäten mit verschiedenen Ansätzen behandelt werden.

## Verwendung von Warteschlangen
Siehe [Redis Warteschlange](../queue/redis.md) und [Stomp-Warteschlange](../queue/stomp.md).

### Vorteile
Es können plötzliche Anfragen zur Verarbeitung großer Geschäftsvolumina bewältigt werden.

### Nachteile
Es ist nicht möglich, die Ergebnisse direkt an den Client zurückzugeben. Für die Ergebnisbereitstellung muss mit anderen Diensten zusammengearbeitet werden, z. B. die Verwendung von [webman/push](https://www.workerman.net/plugin/2) zum Versenden der Verarbeitungsergebnisse.

## Hinzufügen eines HTTP-Ports

> **Hinweis:**
> Diese Funktion erfordert webman-framework>=1.4

Durch das Hinzufügen eines HTTP-Ports können langsame Anfragen über diesen Port von einer bestimmten Gruppe von Prozessen verarbeitet werden, und die Ergebnisse werden direkt an den Client zurückgegeben.

### Vorteile
Die Daten können direkt an den Client zurückgegeben werden.

### Nachteile
Es ist nicht möglich, auf plötzliche Anfragen zur Verarbeitung großer Volumina zu reagieren.

### Implementierungsschritte
Fügen Sie die folgende Konfiguration zur Datei `config/process.php` hinzu.
```php
return [
    // ... Andere Konfigurationen hier ausgelassen ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Anzahl der Prozesse
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Einstellung der Request-Klasse
            'logger' => \support\Log::channel('default'), // Instanz des Protokolls
            'app_path' => app_path(), // Position des app-Verzeichnisses
            'public_path' => public_path() // Position des public-Verzeichnisses
        ]
    ]
];
```

Auf diese Weise können langsame Schnittstellen über die Gruppe von Prozessen unter `http://127.0.0.1:8686/` ausgeführt werden, ohne die Verarbeitung anderer Prozesse zu beeinträchtigen.

Um sicherzustellen, dass der Front-End-Benutzer keine Unterschiede beim Port spürt, kann ein Proxy zum 8686-Port in nginx hinzugefügt werden. Angenommen, die Pfade der langsamen Schnittstellen beginnen alle mit `/task`, dann wäre eine ähnliche nginx-Konfiguration wie folgt:
```conf
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Hinzufügen eines 8686 Upstreams
upstream task {
    server 127.0.0.1:8686;
    keepalive 10240;
}

server {
    server_name webman.com;
    listen 80;
    access_log off;
    root /path/webman/public;

    # Anfragen, die mit /task beginnen, werden zum 8686-Port geroutet. Bitte passen Sie den /task-Pfad entsprechend Ihren Anforderungen an.
    location /task {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header Host $host;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_pass http://task;
    }

    # Andere Anfragen werden über den originalen 8787-Port geroutet
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

Auf diese Weise werden Anfragen, die mit `domain.com/tast/xxx` beginnen, über den separaten 8686-Port verarbeitet, ohne die Verarbeitung der Anfragen über den 8787-Port zu beeinträchtigen.
