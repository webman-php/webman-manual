# 関数の無効化チェック

このスクリプトを使用して、無効化された関数があるかどうかをチェックします。コマンドラインで```curl -Ss https://www.workerman.net/webman/check | php```を実行してください。

もし```Functions 関数名 has be disabled. Please check disable_functions in php.ini```というメッセージが表示された場合、webmanが依存している関数が無効化されていることを意味し、webmanを正常に使用するためにはphp.iniで無効化を解除する必要があります。無効化の解除には、以下のいずれかの方法を選択してください。

## 方法一
`webman/console`をインストールする
```composer require webman/console ^v1.2.35```

次のコマンドを実行します
```php webman fix-disable-functions```

## 方法二
以下のスクリプトを実行し、無効化を解除します
```curl -Ss https://www.workerman.net/webman/fix-disable-functions | php```

## 方法三
`php --ini`を実行し、php cliが使用しているphp.iniファイルの場所を見つけます。

php.iniを開き、次のように`disable_functions`を見つけて、以下の関数の呼び出しを解除してください。
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
