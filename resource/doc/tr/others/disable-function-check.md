```php
# Fonksiyon Kontrolünü Devre Dışı Bırakma

Bu betiği, yasaklanmış fonksiyonların olup olmadığını kontrol etmek için kullanın. Komut satırında şu kodu çalıştırın: `curl -Ss https://www.workerman.net/webman/check | php`

Eğer `Functions fonksiyon_adı has be disabled. Please check disable_functions in php.ini` gibi bir uyarı alırsanız, webman'in bağımlı olduğu fonksiyonlar yasaklanmış demektir. Webman'i normal bir şekilde kullanabilmek için bu fonksiyonların yasağının php.ini'den kaldırılması gerekmektedir.
Yasaklamayı kaldırmak için aşağıdaki yöntemlerden birini seçebilirsiniz.

## Yöntem 1
`webman/console`'ı kurun
```
composer require webman/console ^v1.2.35
```

Aşağıdaki komutu çalıştırın
```
php webman fix-disable-functions
```

## Yöntem 2

Yasaklamayı kaldırmak için aşağıdaki komutu çalıştırın: `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php`

## Yöntem 3

`php --ini` komutunu çalıştırarak php cli'nin kullandığı php.ini dosyasının konumunu bulun.

Php.ini dosyasını açın ve `disable_functions` bölümünü bulun, aşağıdaki fonksiyonların çağrılarını kaldırın
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
```
