# phar paketi

phar, PHP'de JAR'a benzer bir paketleme dosyasıdır. Webman projesinizi tek bir phar dosyasına paketlemek için phar'ı kullanabilirsiniz, bu da dağıtımı kolaylaştırır.

**Burada [fuzqing](https://github.com/fuzqing) 'e PR için çok teşekkür ederiz.**

> **Dikkat**
> `php.ini` dosyasındaki phar yapılandırma seçeneklerinin kapatılması gerekmektedir, yani `phar.readonly = 0` olarak ayarlanmalıdır.

## Komut Satırı Aracı Kurulumu
`composer require webman/console`

## Yapılandırma Ayarları
`config/plugin/webman/console/app.php` dosyasını açın, `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#` ayarını yaparak, kullanıcıların gereksiz dizinleri ve dosyaları paketlemesini önlemek için bazı dosyaları ve dizinleri paketlemesi gerekmektedir, bu şekilde paket boyutunun fazla olmasını engelleyecektir.

## Paketleme
webman projesinin kök dizininde `php webman phar:pack` komutunu çalıştırarak bir `webman.phar` dosyası oluşturulacaktır.

> Paketleme ile ilgili yapılandırma `config/plugin/webman/console/app.php` dosyasında bulunmaktadır.

## Başlatma ve Durdurma İlgili Komutlar
**Başlatma**
`php webman.phar start` veya `php webman.phar start -d`

**Durdurma**
`php webman.phar stop`

**Durumu Görüntüleme**
`php webman.phar status`

**Bağlantı Durumunu Görüntüleme**
`php webman.phar connections`

**Yeniden Başlatma**
`php webman.phar restart` veya `php webman.phar restart -d`

## Açıklama
* webman.phar'ı çalıştırdıktan sonra webman.phar dosyasının bulunduğu dizinde geçici dosyalar gibi günlükleri depolamak için runtime dizini oluşturulacaktır.

* .env dosyasını projenizde kullanıyorsanız, .env dosyasını webman.phar'ın bulunduğu dizine koymalısınız.

* İşiniz public dizinine dosya yüklemeyi gerektiriyorsa, public dizinini webman.phar'ın bulunduğu dizinden ayrı olarak yerleştirmelisiniz, bu durumda `config/app.php` dosyasını yapılandırmanız gerekmektedir.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
İşlevler yardımıyla `public_path()` gerçek public dizin konumunu bulabilir.

* webman.phar, özel süreçleri Windows'ta çalıştırmayı desteklemez.
