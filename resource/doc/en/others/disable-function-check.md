# Disable Function Check

Use this script to check for disabled functions. Run the command in the terminal: ```curl -Ss https://www.workerman.net/webman/check | php```

If you see the prompt ```Functions function_name has been disabled. Please check disable_functions in php.ini```, it means that the functions required by webman have been disabled. To use webman properly, you need to remove the disable in php.ini.
You can follow any of the methods below to remove the disable.

## Method 1
Install `webman/console` 
```
composer require webman/console ^v1.2.35
```

Execute the command:
```
php webman fix-disable-functions
```

## Method 2
Run the script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` to remove the disable.

## Method 3
Run `php --ini` to find the location of the php.ini file used by php cli.

Open the php.ini file, find `disable_functions`, and remove the call for the following functions:
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