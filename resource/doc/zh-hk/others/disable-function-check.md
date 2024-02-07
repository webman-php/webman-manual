# 禁用函數檢查

使用這個腳本檢查是否有禁用函數。在命令行運行```curl -Ss https://www.workerman.net/webman/check | php```

如果提示```Functions 函数名 has be disabled. Please check disable_functions in php.ini```
表示 webman 依賴的函數被禁用，需要在 php.ini 中解除禁用才能正常使用 webman。
解除禁用可選以下方法之一。

## 方法一
安裝 `webman/console` 
```composer require webman/console ^v1.2.35```

執行命令
```php webman fix-disable-functions```

## 方法二
執行腳本 `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` 以解除禁用

## 方法三
運行`php --ini` 找到 php cli 所使用的 php.ini 文件位置

打開 php.ini，找到`disable_functions`，解除以下函數的調用
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
