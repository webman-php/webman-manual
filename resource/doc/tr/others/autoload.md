# Otomatik Yükleme

## PSR-0 Uyumluluğuna Göre Dosyaların Composer ile Yüklenmesi
webman `PSR-4` otomatik yükleme standardını takip eder. İşletmenizin `PSR-0` uyumluluğuna ihtiyaç duyması durumunda aşağıdaki adımları izleyebilirsiniz.

- `PSR-0` uyumlu kod kütüphanesini saklamak için `extend` dizini oluşturun
- `composer.json` dosyasını düzenleyerek, `autoload` kısmına aşağıdaki içeriği ekleyin

```js
"psr-0" : {
    "": "extend/"
}
```
Sonuç olarak şuna benzer bir görüntü elde edilmelidir
![](../../assets/img/psr0.png)

- `composer dumpautoload` komutunu çalıştırın
- webman'i yeniden başlatmak için `php start.php restart` komutunu çalıştırın (Not: Etkili olabilmesi için yeniden başlatılması zorunludur)

## Belirli Dosyaların Composer ile Yüklenmesi

- `composer.json` dosyasını düzenleyerek, `autoload.files` kısmına yüklenmesini istediğiniz dosyaları ekleyin
```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```
- `composer dumpautoload` komutunu çalıştırın
- webman'i yeniden başlatmak için `php start.php restart` komutunu çalıştırın (Not: Etkili olabilmesi için yeniden başlatılması zorunludur)

> **Not**
> `composer.json` dosyasındaki `autoload.files` yapılandırması, webman başlatılmadan önce yüklenir. Diğer yandan, çerçeve tarafından yüklenen `config/autoload.php` dosyaları, webman başlatıldıktan sonra yüklenir.
> `composer.json` dosyasındaki `autoload.files` ile yüklenen dosyaları değiştirdikten sonra, yeniden başlatmanız gerekmektedir; yeniden yükleme ile değişiklikler geçerli olmayacaktır. Diğer yandan, çerçeve üzerinde yüklenen `config/autoload.php` dosyaları, değişiklikler yapıldıktan sonra yeniden yükleme işlemi ile etkinleştirilebilir.

## Çerçeve ile Belirli Dosyaların Yüklenmesi
SPR standardına uygun olmayan bazı dosyaları otomatik olarak yükleyemeyebiliriz, bu tür dosyaların yüklenmesi için `config/autoload.php` dosyasını yapılandırabiliriz. Örneğin:
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
 > `autoload.php` dosyasında, `support/Request.php` ve `support/Response.php` dosyalarının yüklenmesi yapılandırılmıştır. Bunun nedeni, `vendor/workerman/webman-framework/src/support/` klasöründe aynı adı taşıyan iki dosya bulunmasıdır. Bu konfigürasyon ile proje dizinindeki `support/Request.php` ve `support/Response.php` dosyaları öncelikli olarak yüklenir. Bu sayede bu iki dosyanın içeriğini özelleştirebiliriz, `vendor` içerisindeki dosyaları değiştirmemiz gerekmez. Eğer bunları özelleştirmeniz gerekiyorsa, bu yapılandırmaları göz ardı edebilirsiniz.
