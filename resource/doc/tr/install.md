# Ortam Gereksinimleri

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Proje Oluşturma

```php
composer create-project workerman/webman
```

### 2. Çalıştırma

webman dizinine gidin

#### Windows Kullanıcıları
`windows.bat` dosyasını çift tıklayın veya `php windows.php` komutunu çalıştırarak başlatın

> **Not**
> Eğer bir hata alırsanız, muhtemelen bazı fonksiyonlar yasaklanmıştır. [Fonksiyon Yasaklama Kontrolü](others/disable-function-check.md) belgesine başvurarak yasaklamayı kaldırabilirsiniz

#### Linux Kullanıcıları
`debug` modunda çalıştırma (geliştirme ve hata ayıklama için)

```php
php start.php start
```

`daemon` modunda çalıştırma (canlı ortam için)

```php
php start.php start -d
```

> **Not**
> Eğer bir hata alırsanız, muhtemelen bazı fonksiyonlar yasaklanmıştır. [Fonksiyon Yasaklama Kontrolü](others/disable-function-check.md) belgesine başvurarak yasaklamayı kaldırabilirsiniz

### 3. Erişim

Tarayıcı üzerinden `http://ip adresi:8787` adresine gidin
