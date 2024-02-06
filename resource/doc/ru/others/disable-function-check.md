# Проверка отключенных функций

Используйте этот скрипт для проверки отключенных функций. Запустите его из командной строки с помощью ```curl -Ss https://www.workerman.net/webman/check | php```

Если появляется сообщение ```Functions функция_name has be disabled. Please check disable_functions in php.ini```, это означает, что функции, от которых зависит webman, были отключены. Чтобы webman работал правильно, необходимо разрешить эти функции в файле php.ini.
Для разрешения функций следуйте любому из следующих методов.

## Метод 1
Установите `webman/console` 
```
composer require webman/console ^v1.2.35
```

Выполните команду
```
php webman fix-disable-functions
```

## Метод 2
Запустите скрипт `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` для разрешения запрета.

## Метод 3
Запустите `php --ini` для определения расположения файла php.ini, используемого в cli-режиме.

Откройте php.ini и найдите `disable_functions`, разрешите вызов следующих функций:
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
