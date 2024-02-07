# Disable Function Check

Use this script to check for disabled functions. Run the command ```curl -Ss https://www.workerman.net/webman/check | php``` in the command line.

If you see the prompt ```Functions function_name has been disabled. Please check disable_functions in php.ini```, it means that the functions webman relies on have been disabled. You need to enable them in php.ini in order to use webman properly. Please refer to one of the following methods to enable them.

## Method One
Install `webman/console` 
```
composer require webman/console ^v1.2.35
```

Execute the command
```
php webman fix-disable-functions
```

## Method Two

Execute the script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` to enable the functions.

## Method Three

Run `php --ini` to find the location of the php.ini file used by php-cli.

Open php.ini, find `disable_functions`, and enable the following functions:
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
