# Deshabilitar la comprobación de funciones

Utilice este script para comprobar si hay funciones deshabilitadas. Ejecute el comando en la línea de comandos ```curl -Ss https://www.workerman.net/webman/check | php```

Si recibe el mensaje ```La función Functions ha sido deshabilitada. Por favor, verifique disable_functions en php.ini```, significa que las funciones en las que webman depende han sido deshabilitadas, y es necesario eliminar la restricción en php.ini para poder utilizar webman correctamente.
Siga uno de los métodos a continuación para eliminar la restricción.

## Método uno
Instale `webman/console` 
```
composer require webman/console ^v1.2.35
```

Ejecute el comando
```
php webman fix-disable-functions
```

## Método dos

Ejecute el script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` para eliminar la restricción

## Método tres

Ejecute `php --ini` para encontrar la ubicación del archivo php.ini utilizado por el php cli

Abra php.ini, localice `disable_functions` y quite la restricción a las siguientes funciones
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
