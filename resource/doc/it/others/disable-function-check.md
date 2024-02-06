# Disabilità del controllo delle funzioni

Utilizza questo script per verificare se alcune funzioni sono state disabilitate. Esegui il comando da riga di comando ```curl -Ss https://www.workerman.net/webman/check | php```.

Se viene visualizzato il messaggio ```La funzione Functions nome_funzione è stata disabilitata. Controlla le disable_functions nel file php.ini```, significa che le funzioni dipendenti da webman sono state disabilitate e è necessario rimuovere la disabilitazione dal file php.ini per poter utilizzare webman correttamente.
Per rimuovere la disabilitazione, segui uno dei metodi riportati di seguito.

## Metodo Uno
Installa `webman/console` 
```
composer require webman/console ^v1.2.35
```

Esegui il comando
```
php webman fix-disable-functions
```

## Metodo Due
Esegui lo script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` per rimuovere la disabilitazione

## Metodo Tre
Esegui `php --ini` per trovare la posizione del file php.ini utilizzato da PHP CLI

Apri il file php.ini, individua `disable_functions` e rimuovi i seguenti nomi di funzioni
```
stream_socket_server
stream_socket_client
pcntl_signal_dispatch
pcntl_signal
pcntl_alarm
pcntl_fork
posix_getuid
posix_getpwuid
posix_kill
posix_setsid
posix_getpid
posix_getpwnam
posix_getgrnam
posix_getgid
posix_setgid
posix_initgroups
posix_setuid
posix_isatty
proc_open
proc_get_status
proc_close
shell_exec
```
