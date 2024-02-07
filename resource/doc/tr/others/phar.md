# phar Paketi

phar, PHP'de JAR'a benzeyen bir paketleme dosyasıdır ve webman proje dosyalarını tek bir phar dosyasına paketlemek için phar'ı kullanabilirsiniz, bu da dağıtımı kolaylaştırır.

**Burada [fuzqing](https://github.com/fuzqing) 'e çok teşekkür ederiz.**

> **Not**
> `php.ini`'de phar yapılandırma seçeneklerini kapatmanız gerekmektedir, yani `phar.readonly = 0` olarak ayarlanmalıdır.

## Komut Satırı Aracı Kurulumu
`composer require webman/console`

## Yapılandırma Ayarları
`config/plugin/webman/console/app.php` dosyasını açın ve ` 'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` olarak ayarlayın, bu, kullanıcıların paketleme sırasında gereksiz dizinleri ve dosyaları hariç tutmalarına ve paket boyutunun aşırı büyümesini önlemelerine yardımcı olur.

## Paketleme
webman proje kök dizininde `php webman phar:pack` komutunu çalıştırarak bir `webman.phar` dosyası oluşturacaktır.

> Paketleme ilişkili yapılandırmalar `config/plugin/webman/console/app.php` içindedir.

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
* webman.phar çalıştırıldıktan sonra, webman.phar dosyasının bulunduğu dizinde geçici dosyalar gibi günlük dosyasını depolamak için runtime dizini oluşturulur.

* Eğer projenizde .env dosyasını kullanıyorsanız, .env dosyasını webman.phar dosyasının bulunduğu dizine koymalısınız.

* İşletmenizin public dizinine dosya yüklemesi yapmanız gerekiyorsa, public dizinini webman.phar dosyasının bulunduğu dizine çıkarmalısınız, bu durumda `config/app.php` dosyasını yapılandırmanız gerekmektedir.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
İşletme, gerçek public dizini konumunu bulmak için `public_path()` yardımcı fonksiyonunu kullanabilir.

* webman.phar, özel işlemi Windows'ta etkinleştirmez.
