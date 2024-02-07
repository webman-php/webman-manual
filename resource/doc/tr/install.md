# Ortam Gereksinimleri

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Proje Oluşturma

```php
composer create-project workerman/webman
```

### 2. Çalıştırma

Webman dizinine girin

#### Windows Kullanıcıları
Çift tıklayarak `windows.bat` dosyasını veya `php windows.php` komutunu çalıştırın

> **Not**
> Eğer bir hata alırsanız, muhtemelen işlevler devre dışı bırakılmıştır, [disable-function-check.md](others/disable-function-check.md) belgesine bakarak devre dışı bırakma işlemini gerçekleştirin

#### Linux Kullanıcıları
`debug` modunda çalıştırma (geliştirme ve hata ayıklama için)

```php
php start.php start
```

`daemon` modunda çalıştırma (prodüksiyon ortamı için)

```php
php start.php start -d
```

> **Not**
> Eğer bir hata alırsanız, muhtemelen işlevler devre dışı bırakılmıştır, [disable-function-check.md](others/disable-function-check.md) belgesine bakarak devre dışı bırakma işlemini gerçekleştirin

### 3. Erişim

Tarayıcıdan `http://ip adresi:8787` adresine erişin
