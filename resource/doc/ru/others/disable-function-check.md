# Проверка отключенных функций

Используйте этот скрипт, чтобы проверить, есть ли отключенные функции. Запустите в командной строке ```curl -Ss https://www.workerman.net/webman/check | php```.

Если вы видите сообщение ```Functions функция_имя has been disabled. Please check disable_functions in php.ini```, это означает, что функции, от которых зависит webman, были отключены, и их нужно разрешить в php.ini, чтобы webman можно было использовать нормально.
Чтобы разрешить отключение, воспользуйтесь одним из следующих методов.

## Метод один
Установите `webman/console`
```php
composer require webman/console ^v1.2.35
```

Выполните команду
```php
php webman fix-disable-functions
```

## Метод два

Запустите скрипт `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php`, чтобы разрешить отключение.

## Метод три
Запустите `php --ini`, чтобы найти местоположение файла php.ini, используемого php cli.

Откройте php.ini, найдите `disable_functions` и разрешите вызов следующих функций
```php
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
