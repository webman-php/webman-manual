# 禁用函式檢查

使用此腳本檢查是否有禁用的函式。在命令列執行```curl -Ss https://www.workerman.net/webman/check | php```

若提示```Functions 函式名 has been disabled. Please check disable_functions in php.ini```表示webman所依賴的函式已被禁用，需要在php.ini中解除禁用才能正常使用webman。解除禁用參考以下方法選用其一即可。

## 方法一
安裝`webman/console` 
```composer require webman/console ^v1.2.35```

執行指令
```php webman fix-disable-functions```

## 方法二
執行腳本 `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` 以解除禁用

## 方法三
執行`php --ini` 找到php cli所使用的php.ini檔案位置

開啟php.ini，找到`disable_functions`，解除以下函式的呼叫
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
shell_exec```
