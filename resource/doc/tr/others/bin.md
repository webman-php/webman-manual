# İkili Paketleme

Webman, projeyi bir ikili dosyaya paketlemeyi destekler, bu da webman'ın PHP ortamına ihtiyaç duymadan Linux sistemlerde çalışmasını sağlar.

> **Not**
> Paketlenmiş dosya şu anda yalnızca x86_64 mimarili Linux sistemlerde çalışmaktadır ve macOS'i desteklememektedir.
> `php.ini` dosyasındaki phar yapılandırma seçeneğini kapatmanız gerekmektedir, yani `phar.readonly = 0` olarak ayarlanmalıdır.

## Komut Satırı Aracı Kurulumu
`composer require webman/console ^1.2.24`

## Yapılandırma Ayarları
`config/plugin/webman/console/app.php` dosyasını açın ve aşağıdaki ayarı yapın:
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Bu ayar, paketlerken bazı gereksiz dizinleri ve dosyaları hariç tutmak için kullanılır, bu sayede paketin boyutunu küçültmek amaçlanmaktadır.

## Paketleme
Aşağıdaki komutu çalıştırın:
```
php webman build:bin
```
Ayrıca hangi PHP sürümüyle paketlemek istediğinizi de belirtebilirsiniz, örneğin:
```
php webman build:bin 8.1
```

Paketleme işleminden sonra `build` dizini içinde bir `webman.bin` dosyası oluşturulur.

## Başlatma
Webman.bin dosyasını Linux sunucusuna yükleyin, ardından `./webman.bin start` veya `./webman.bin start -d` komutunu kullanarak başlatabilirsiniz.

## Prensip
* Öncelikle yerel webman projesi bir phar dosyasına paketlenir.
* Ardından uzak sunucudan php8.x.micro.sfx dosyası indirilir.
* Php8.x.micro.sfx ve phar dosyası birleştirilerek bir ikili dosya oluşturulur.

## Dikkat Edilmesi Gerekenler
* Yereldeki PHP sürümü 7.2'den büyük olmalıdır.
* Ancak sadece PHP 8 için ikili dosya oluşturulabilmektedir.
* Yerel PHP sürümü ile paketleme sürümünün aynı olması önerilmektedir, yani eğer yerel olarak PHP 8.0 kullanıyorsanız, paketleme sırasında da PHP 8.0 kullanmanız önerilir, uyumluluk sorunlarını önlemek için.
* Paketleme sırasında PHP 8'in kaynak kodları indirilir, ancak yerelde kurulmaz, yerel PHP ortamını etkilemez.
* Webman.bin şu anda yalnızca x86_64 mimarili Linux sistemlerinde çalışmaktadır ve macOS'i desteklememektedir.
* Varsayılan olarak env dosyası paketlenmez (`config/plugin/webman/console/app.php` de exclude_files kontrol eder), bu nedenle başlatma esnasında env dosyasının webman.bin ile aynı dizinde bulunması gerekmektedir.
* Çalışma sırasında webman.bin dizininde bir runtime dizini oluşturulur, bu dizin log dosyalarını içerir.
* Şu anda webman.bin dışındaki php.ini dosyalarını okumaz, özel php.ini gerekiyorsa `/config/plugin/webman/console/app.php` dosyasında custom_ini olarak ayarlanmalıdır.

## Tek Başına Statik PHP İndirme
Bazı durumlarda sadece PHP ortamı dağıtmanız gerekiyor, bu durumda sadece bir PHP çalıştırılabilir dosyaya ihtiyacınız var demektir, işte buradan [statik php indirme](https://www.workerman.net/download) bağlantısını kullanarak indirebilirsiniz.

> **İpucu**
> Statik php için özel php.ini dosyası belirtmek istiyorsanız aşağıdaki komutu kullanabilirsiniz `php -c /your/path/php.ini start.php start -d`

## Desteklenen Uzantılar
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## Proje Kaynakları
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
