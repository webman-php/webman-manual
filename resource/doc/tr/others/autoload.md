# Otomatik Yükleme

## PSR-0 standartlarına göre dosyaların composer ile yüklenmesi
webman, `PSR-4` otomatik yükleme standartlarını takip eder. İşinizde `PSR-0` standartlarına uygun kod kütüphanesi yüklemeniz gerekiyorsa, aşağıdaki adımlara göz atın.

- `extend` dizini oluşturun ve buraya `PSR-0` standartlarına uygun kod kütüphanesini yerleştirin.
- `composer.json` dosyasını düzenleyin ve `autoload` seçeneği altına aşağıdaki içeriği ekleyin.

```js
"psr-0" : {
    "": "extend/"
}
```
Sonuç olarak şöyle bir yapı oluşur
![](../../assets/img/psr0.png)

- `composer dumpautoload` komutunu çalıştırın.
- webman'i yeniden başlatmak için `php start.php restart` komutunu çalıştırın (Dikkat, bu işlemi yapmadan önce yeniden başlatmalısınız) 

## Belirli dosyaların composer ile yüklenmesi

- `composer.json` dosyasını düzenleyin ve `autoload.files` altına yüklemek istediğiniz dosyaları ekleyin.
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- `composer dumpautoload` komutunu çalıştırın.
- webman'i yeniden başlatmak için `php start.php restart` komutunu çalıştırın (Dikkat, bu işlemi yapmadan önce yeniden başlatmalısınız) 

> **Not**
> `composer.json` dosyasındaki `autoload.files` yapılandırması, webman başlatılmadan önce dosyaları yükler. Ancak, çerçeve tarafından `config/autoload.php` ile yüklenen dosyalar, webman başlatıldıktan sonra yüklenir.
> `composer.json` dosyasındaki `autoload.files` ile yüklenen dosyaların değiştirilmesi için restart işlemi gereklidir. Reload işlemi ise etkisizdir. `config/autoload.php` ile yüklenen dosyaların değiştirilmesi durumunda ise sadece reload işlemi yeterlidir.

## Çerçeve tarafından belirli dosyaların yüklenmesi
Bazı dosyalar, SPR standartlarına uymadığından otomatik olarak yüklenemez. Bu tür dosyaları yüklemek için `config/autoload.php` dosyasını yapılandırabiliriz. Örneğin:
```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **Not**
 > `autoload.php` dosyasında, `support/Request.php` ve `support/Response.php` dosyalarının yüklenmesi yapılmıştır, çünkü `vendor/workerman/webman-framework/src/support/` dizini altında aynı adı taşıyan dosyalar bulunmaktadır. Bu şekilde, projenin kök dizini altındaki `support/Request.php` ve `support/Response.php` dosyalarını öncelikli olarak yüklememizi sağlar ve bu dosyaları değiştirebiliriz, `vendor` içindeki dosyalara müdahale etmemize gerek kalmaz. Eğer bunları özelleştirmeniz gerekmiyorsa, bu yapılandırmaları yok sayabilirsiniz.

