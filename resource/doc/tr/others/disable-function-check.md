# Fonksiyon Kontrolünü Devre Dışı Bırakma

Bu betik, yasaklı fonksiyonların olup olmadığını kontrol etmek için kullanılır. Komut satırında şu komutu çalıştırın: ```curl -Ss https://www.workerman.net/webman/check | php```

Eğer ```Functions fonksiyon_adı has be disabled. Please check disable_functions in php.ini``` şeklinde bir uyarı alırsanız, webman'ın bağımlı olduğu fonksiyonların devre dışı bırakıldığını gösterir. Webman'ı normal bir şekilde kullanabilmek için php.ini dosyasında bu fonksiyonların devre dışı bırakılmasını kaldırmanız gerekmektedir. Devre dışı bırakma işlemi için aşağıdaki yöntemleri kullanabilirsiniz.

## Yöntem 1
`webman/console` paketini yükleyin
```
composer require webman/console ^v1.2.35
```

Ardından şu komutu çalıştırın
```
php webman fix-disable-functions
```

## Yöntem 2
Aşağıdaki komutu çalıştırarak devre dışı bırakmayı kaldırabilirsiniz
```
curl -Ss https://www.workerman.net/webman/fix-disable-functions | php
```

## Yöntem 3
`php --ini` komutunu çalıştırarak php cli'nin kullandığı php.ini dosyasının konumunu bulun.

php.ini dosyasını açın ve `disable_functions` kısmını bulun, aşağıdaki fonksiyonların çağrılmasını kaldırın
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
