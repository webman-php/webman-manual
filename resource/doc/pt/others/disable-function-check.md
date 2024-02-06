# Desativar a verificação de funções

Utilize este script para verificar se existem funções desativadas. Execute o comando na linha de comando: `curl -Ss https://www.workerman.net/webman/check | php`

Se receber a mensagem `A função Functions foi desativada. Por favor, verifique disable_functions no php.ini`, significa que as funções necessárias para o webman foram desativadas e é necessário removê-las do php.ini para utilizar o webman normalmente.
Siga um dos métodos abaixo para remover a desativação.

## Método Um
Instale `webman/console` 
```
composer require webman/console ^v1.2.35
```

Execute o comando
```
php webman fix-disable-functions
```

## Método Dois

Execute o script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` para remover a desativação.

## Método Três

Execute `php --ini` para encontrar o local do arquivo php.ini usado pelo php cli.

Abra o php.ini, localize `disable_functions` e remova a chamada das funções a seguir:
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
