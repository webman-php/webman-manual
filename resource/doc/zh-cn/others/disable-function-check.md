# 禁用函数检查

使用这个脚本检查是否有禁用函数。命令行运行```curl -Ss https://www.workerman.net/webman/check | php```

如果有提示```Functions 函数名 has be disabled. Please check disable_functions in php.ini```说明webman依赖的函数被禁用，需要在php.ini中解除禁用才能正常使用webman。
解除禁用参考以下方法任选其一即可。

## 方法一
安装`webman/console` 
```
composer require webman/console ^v1.2.35
```

执行命令
```
php webman fix-disable-functions
```

## 方法二

执行脚本 `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` 以解除禁用

## 方法三

运行`php --ini` 找到php cli所使用的php.ini文件位置

打开php.ini，找到`disable_functions`，解除以下函数的调用
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
exec
putenv
getenv
```


