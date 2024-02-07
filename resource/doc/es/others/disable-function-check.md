# Deshabilitar la verificación de funciones

Utiliza este script para comprobar si hay funciones deshabilitadas. Ejecuta en la línea de comandos: ```curl -Ss https://www.workerman.net/webman/check | php```

Si aparece el mensaje ```La función Functions ha sido deshabilitada. Por favor revisa disable_functions en php.ini```, significa que las funciones en las que webman depende están deshabilitadas. Debes habilitarlas en php.ini para poder utilizar webman normalmente. A continuación se presentan algunos métodos para habilitarlas.

## Método 1
Instala `webman/console`
```composer require webman/console ^v1.2.35```

Ejecuta el comando
```php webman fix-disable-functions```

## Método 2
Ejecuta el script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` para habilitarlas.

## Método 3
Ejecuta `php --ini` para encontrar la ubicación del archivo php.ini utilizado por php cli.

Abre php.ini y encuentra `disable_functions`. Habilita las siguientes funciones:
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
shell_exec
```
