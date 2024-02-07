# Disabilita il controllo delle funzioni

Utilizza questo script per verificare se ci sono funzioni disabilitate. Esegui il comando da riga di comando ```curl -Ss https://www.workerman.net/webman/check | php```

Se ricevi il messaggio ```La funzione Functions nome_funzione è stata disabilitata. Si prega di controllare disable_functions in php.ini```, significa che le funzioni dipendenti da webman sono state disabilitate e devono essere abilitate nel file php.ini per poter utilizzare webman in modo corretto.
Segui una delle seguenti modalità per abilitarle.

## Modalità 1
Installa `webman/console`
```
composer require webman/console ^v1.2.35
```

Esegui il comando
```
php webman fix-disable-functions
```

## Modalità 2

Esegui lo script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` per rimuovere la disabilitazione

## Modalità 3

Esegui `php --ini` per trovare la posizione del file php.ini utilizzato da php cli

Apri php.ini, trova `disable_functions` e rimuovi la chiamata alle seguenti funzioni
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
