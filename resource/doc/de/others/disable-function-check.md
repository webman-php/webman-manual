# Deaktivierung der Funktionsüberprüfung

Verwenden Sie dieses Skript, um zu überprüfen, ob Funktionen deaktiviert sind. Führen Sie den Befehl ```curl -Ss https://www.workerman.net/webman/check | php``` in der Befehlszeile aus.

Wenn die Meldung ```Functions Funktionname wurde deaktiviert. Bitte überprüfen Sie disable_functions in der php.ini``` angezeigt wird, bedeutet dies, dass die von webman abhängigen Funktionen deaktiviert sind und im php.ini aktiviert werden müssen, um webman ordnungsgemäß verwenden zu können. Heben Sie die Deaktivierung durch eine der folgenden Methoden auf.

## Methode eins
Installieren Sie `webman/console`
```composer require webman/console ^v1.2.35```

Führen Sie den Befehl aus
```php webman fix-disable-functions```

## Methode zwei
Führen Sie das Skript aus ```curl -Ss https://www.workerman.net/webman/fix-disable-functions | php```, um die Deaktivierung aufzuheben.

## Methode drei
Führen Sie `php --ini` aus, um den Speicherort der php.ini-Datei für die PHP-CLI zu finden.

Öffnen Sie die php.ini-Datei, suchen Sie nach `disable_functions` und entfernen Sie den Aufruf der folgenden Funktionen:
```stream_socket_server
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
shell_exec```
