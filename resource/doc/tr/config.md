# Yapılandırma Dosyası

## Konum
webman'in yapılandırma dosyaları `config/` klasöründe bulunur, projede ilgili yapılandırmayı almak için `config()` fonksiyonu kullanılabilir.

## Yapılandırmayı Almak

Tüm yapılandırmayı almak için
```php
config();
```

`config/app.php` içindeki tüm yapılandırmayı almak için
```php
config('app');
```

`config/app.php` içindeki `debug` yapılandırmasını almak için
```php
config('app.debug');
```

Eğer yapılandırma bir dizi ise, nokta kullanarak içindeki öğelerin değerlerini alabilirsiniz, örneğin
```php
config('file.key1.key2');
```

## Varsayılan Değer
```php
config($key, $default);
```
config, ikinci argüman olarak varsayılan değeri ileterek yapılandırmayı alır, yapılandırma mevcut değilse varsayılan değeri döndürür. Yapılandırma yoksa ve varsayılan değer belirtilmemişse null döner.

## Özel Yapılandırma
Geliştiriciler, kendi yapılandırma dosyalarını `config/` klasörüne ekleyebilir, örneğin

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Alırken kullanımı**
```php
config('payment');
config('payment.key');
config('payment.secret');
```

## Yapılandırmayı Değiştirmek
webman, yapılandırmayı dinamik olarak değiştirmeyi desteklemez, tüm yapılandırmaların ilgili yapılandırma dosyalarını el ile değiştirmek ve yeniden yüklemek veya yeniden başlatmak gereklidir.

> **Not**
> Sunucu yapılandırması `config/server.php` ve işlem yapılandırması `config/process.php` yeniden yükleme işlemini desteklemez, etkili hale getirmek için yeniden başlatma gereklidir.
