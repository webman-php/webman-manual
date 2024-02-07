# Verificação de Funções Desativadas

Utilize este script para verificar se há funções desativadas. Execute o comando na linha de comando: ```curl -Ss https://www.workerman.net/webman/check | php```

Se você receber o aviso "A função Functions foi desativada. Por favor, verifique disable_functions no php.ini", isso significa que as funções dependentes do webman foram desativadas e é necessário remover a desativação no php.ini para usar o webman normalmente.
Para remover a desativação, escolha um dos métodos abaixo.

## Método 1
Instale `webman/console`
```
composer require webman/console ^v1.2.35
```

Execute o comando
```
php webman fix-disable-functions
```

## Método 2
Execute o script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` para remover a desativação.

## Método 3
Execute `php --ini` para encontrar a localização do arquivo php.ini usado pelo php cli.

Abra o php.ini e encontre `disable_functions`, remova a chamada das seguintes funções:
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
