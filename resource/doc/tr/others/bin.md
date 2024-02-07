# İkili Paketleme

webman, projeyi bir ikili dosyaya paketlemeyi destekler, bu sayede webman'ın linux sistemlerde php ortamına ihtiyaç duymadan da çalıştırılabilir.

> **Not**
> Paketlendikten sonra dosyalar sadece x86_64 mimarisi linux sistemlerinde çalışır, macOS sistemlerini desteklemez
> `php.ini` dosyasındaki, yani `phar.readonly = 0` geçerli olacak şekilde phar yapılandırma seçeneklerini kapatmanız gerekmektedir.

## Komut Satırı Aracı Kurulumu
`composer require webman/console ^1.2.24`

## Yapılandırma Ayarları
`config/plugin/webman/console/app.php` dosyasını açarak aşağıdaki ayarı yapın
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Bu ayar, paketleme sırasında gereksiz dizin ve dosyaları hariç tutmak için kullanılır, paket boyutunu büyümemesi için.

## Paketleme
Aşağıdaki komutu çalıştırın
```bash
php webman build:bin
```
Ayrıca, belirli bir php sürümü ile de paketleme yapabilirsiniz, örneğin
```bash
php webman build:bin 8.1
```

Paketleme işlemi sonrasında `webman.bin` adında bir dosya `build` klasöründe oluşturulacaktır.

## Başlatma
webman.bin dosyasını linux sunucusuna yükleyerek `./webman.bin start` veya `./webman.bin start -d` komutunu çalıştırarak başlatabilirsiniz.

## Prensip
* İlk olarak, yerel webman projesi bir phar dosyasına paketlenir
* Daha sonra php8.x.micro.sfx dosyası uzaktan indirilir
* Son olarak, php8.x.micro.sfx ve phar dosyası birleştirilerek bir ikili dosya oluşturulur

## Notlar
* Yereldeki php sürümü 7.2 ve daha yüksekse paketleme komutunu çalıştırabilirsiniz
* Ancak, sadece php8 ikili dosyası oluşturulabilir
* Yereldeki php sürümü ve paketleme sürümünün aynı olmasını şiddetle tavsiye ederiz, yani eğer yerelde php8.0 kullanıyorsanız, paketleme için de php8.0 kullanmalısınız, uyumluluk sorunlarını önlemek adına
* Paketleme işleminde php8 kaynak kodları indirilir, ancak yerelde kurulmaz, yerel php ortamını etkilemez
* webman.bin şu anda yalnızca x86_64 mimarili linux sistemlerde çalışır, macOS sistemlerini desteklemez
* Varsayılan olarak env dosyası paketlenmez (`config/plugin/webman/console/app.php` içinde exclude_files ayarını kontrol eder), bu yüzden başlangıçta env dosyasının webman.bin ile aynı dizinde olması gerekmektedir
* Çalışma sırasında, webman.bin dosyasının bulunduğu dizinde runtime klasörü oluşturulur, bu klasör log dosyalarını saklamak için kullanılır
* Şu anda webman.bin dışarıdan php.ini dosyasını okumaz, özelleştirilmiş php.ini dosyası gerekiyorsa, lütfen `/config/plugin/webman/console/app.php` dosyasında custom_ini ayarını yapın

## Tek Başıma Statik PHP İndirme
Bazen sadece bir PHP ortamının dağıtılmasını istersiniz, bu durumda [statik php indirme](https://www.workerman.net/download) bağlantısına tıklayabilirsiniz.

> **Not**
> Statik php'ye özel bir php.ini dosyası belirtmek isterseniz, aşağıdaki komutu kullanabilirsiniz: `php -c /your/path/php.ini start.php start -d`

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

## Proje Kaynağı
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
