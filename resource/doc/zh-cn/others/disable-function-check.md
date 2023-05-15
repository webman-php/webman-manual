# 禁用函数检查

使用这个脚本检查是否有禁用函数。命令行运行```curl -Ss https://www.workerman.net/webman/check | php```

如果有提示```Functions 函数名 has be disabled. Please check disable_functions in php.ini```说明workerman依赖的函数被禁用，需要在php.ini中解除禁用才能正常使用workerman。
解除禁用参考如下两种方法任选其一即可。

## 方法一：脚本解除

执行脚本 `curl -Ss https://www.workerman.net/webman/fix | php` 以解除禁用

## 方法二：手动解除

**步骤如下：**

1、运行`php --ini` 找到php cli所使用的php.ini文件位置

2、打开php.ini，找到`disable_functions`一项解除对应函数的禁用

**依赖的函数**
使用webman需要解除以下函数的禁用
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


