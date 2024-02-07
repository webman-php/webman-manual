# Yapılandırma Dosyası

## Konum
webman'ın yapılandırma dosyaları `config/` dizininde bulunur ve projede ilgili yapılandırmayı almak için `config()` işlevini kullanabilirsiniz.

## Yapılandırmayı Almak

Tüm yapılandırmayı almak için
```php
config();
```

Tüm `config/app.php` dosyasındaki yapılandırmayı almak için
```php
config('app');
```

`config/app.php` dosyasındaki `debug` yapılandırmasını almak için
```php
config('app.debug');
```

Eğer yapılandırma bir dizi ise, `.` kullanarak dizi içindeki öğelerin değerlerini alabilirsiniz, örneğin
```php
config('file.key1.key2');
```

## Varsayılan Değer
```php
config($key, $default);
```
config ikinci parametre aracılığıyla varsayılan değeri iletebilir, yapılandırma mevcut değilse varsayılan değeri döndürür.
Yapılandırma mevcut değilse ve varsayılan değer belirtilmemişse null döndürür.

## Özel Yapılandırma
Geliştiriciler `config/` dizini altına kendi yapılandırma dosyalarını ekleyebilirler, örneğin

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Yapılandırmayı Alırken Kullanılabilir**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Yapılandırmayı Değiştirme
webman dinamik yapılandırmayı desteklemez, tüm yapılandırmalar ilgili yapılandırma dosyasını el ile değiştirmeniz gerekmektedir ve ardından yeniden yüklemek veya yeniden başlatmak gerekmektedir.

> **Dikkat**
> Sunucu yapılandırması `config/server.php` ve işlem yapılandırması `config/process.php` yeniden yükleme işlemini desteklemez, etkin olması için yeniden başlatılması gerekir
