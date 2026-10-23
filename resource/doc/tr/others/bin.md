# İkili Paketleme

webman, projeyi tek bir ikili dosyaya paketlemeyi destekler; bu sayede webman, PHP ortamı olmadan Linux üzerinde çalışabilir.

> **Not**
> Paketlenen dosya şu anda yalnızca x86_64 mimarili Linux sistemlerde çalışır. Windows ve macOS desteklenmez.
> `php.ini` dosyasında phar seçeneğini devre dışı bırakmanız gerekir: `phar.readonly = 0` ayarlayın.

## Komut satırı aracını kurma
`composer require webman/console`

## Paketleme
Şu komutu çalıştırın
```
php webman build:bin
```
Belirli bir PHP sürümüyle paketleme de yapılabilir, örneğin
```
php webman build:bin 8.3
```

Paketlemeden sonra build dizininde `webman.bin` dosyası oluşturulur.

## Başlatma
webman.bin dosyasını Linux sunucusuna yükleyin, ardından `./webman.bin start` veya `./webman.bin start -d` ile başlatın.

## İlke
* Önce yerel webman projesi bir phar dosyasına paketlenir
* Ardından php8.x.micro.sfx uzaktan indirilir
* php8.x.micro.sfx ile phar dosyası tek bir ikili dosya halinde birleştirilir

## Önemli notlar
* Uyumluluk sorunlarını önlemek için yerel ve paketlemede aynı PHP sürümünü (örn. ikisinde de PHP 8.3) kullanmanız önerilir
* Paketleme sırasında PHP 8 kaynak kodu indirilir ancak yerelde kurulmaz, yerel PHP ortamını etkilemez
* webman.bin şu anda yalnızca x86_64 Linux’ta çalışır, macOS desteklenmez
* Paketlenen projeler reload desteklemez; kod güncellemelerinde yeniden başlatma gerekir
* Varsayılan olarak env dosyası paketlenmez (`config/plugin/webman/console/app.php` içindeki exclude_files ile yönetilir). Başlatırken env dosyası webman.bin ile aynı dizinde olmalıdır
* Çalışma sırasında webman.bin’in bulunduğu dizinde log dosyaları için bir runtime dizini oluşturulur
* webman.bin şu anda harici php.ini dosyası okumaz. php.ini özelleştirmek için `config/plugin/webman/console/app.php` içindeki custom_ini’de ayarlayın
* Paketlenmesi gerekmeyen dosyalar `config/plugin/webman/console/app.php` içinde hariç tutulabilir; böylece paket gereksiz büyümez
* İkili paketleme Swoole korutinlerini desteklemez
* Kullanıcı tarafından yüklenen dosyaları ikili paket içinde saklamayın. `phar://` protokolüyle bu dosyalar üzerinde işlem yapmak risklidir (phar deserializasyon açığı). Yüklenen dosyalar paket dışında, diskte ayrı tutulmalıdır
* Uygulamanız public dizinine dosya yüklemesi gerekiyorsa, public dizinini webman.bin ile aynı konuma taşıyın, `config/app.php` dosyasını şöyle ayarlayıp yeniden paketleyin:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Statik PHP’yi ayrı indirme
Bazen yalnızca PHP çalıştırılabilir dosyası gerekiyorsa, tüm PHP ortamını kurmanıza gerek yok. [Statik PHP’yi buradan indirin](https://www.workerman.net/download).

> **İpucu**
> Statik PHP için php.ini belirtmek için: `php -c /your/path/php.ini start.php start -d`

## Desteklenen eklentiler
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Proje kaynağı

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
