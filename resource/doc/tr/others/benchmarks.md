# Performans Testi

### Performans test sonuçları hangi faktörlerden etkilenir?
- Sunucuya olan ağ gecikmesi (yerel ağ veya yerel test önerilir)
- Sunucuya olan bant genişliği (yerel ağ veya yerel test önerilir)
- HTTP keep-alive'in açık olup olmadığı (açık olması önerilir)
- Yeterli eşzamanlı kullanıcı sayısı (uzak testler için mümkün olduğunca yüksek eşzamanlı kullanıcı sayısı önerilir)
- Sunucu işlem sayısı uygun mu? (helloworld işlemleri için işlem sayısı önerilen CPU sayısıyla aynı olmalıdır, veritabanı işlemleri için ise CPU'nun dört katı veya daha fazlası önerilir)
- İşin kendisi performansı (örneğin, uzak veritabanı kullanılıp kullanılmadığını kontrol etmek)

### HTTP keep-alive nedir?
HTTP Keep-Alive mekanizması, tek bir TCP bağlantısı üzerinden birden fazla HTTP isteği ve yanıt gönderme ve alma tekniğidir. Bu mekanizma performans test sonuçları üzerinde büyük etki yapar, keep-alive kapatıldığında QPS (Saniye başına istek sayısı) katlanarak azalabilir.
Şu anda tüm tarayıcılar varsayılan olarak keep-alive'ı açık tutar, yani tarayıcı belirli bir HTTP adresine eriştiğinde bağlantıyı kapatmaz, bir sonraki istekte bu bağlantıyı yeniden kullanır ve bu performansı artırır.
Performans testleri sırasında keep-alive önerilir.

### Performans testi sırasında HTTP keep-alive nasıl açılır?
Eğer ab programını kullanıyorsanız -k parametresini eklemeniz gerekir, örneğin `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Apipost'ta keep-alive'ı etkinleştirebilmek için sıkıştırma başlığını döndürmek gereklidir (apipost'un hatası, aşağıya bakınız).
Diğer performans test programları genellikle varsayılan olarak açıktır.

### Neden uzak ağ üzerinden yapılan performans testinde QPS (Saniye başına istek sayısı) çok düşük?
Uzak ağ gecikmesi çok büyük olduğu için QPS düşük olması normal bir durumdur. Örneğin Baidu'nun sayfasını test etmek, QPS'nin sadece birkaç düzine olması mümkündür.
Yerel ağ veya yerel testler yapmanızı öneririz, ağ gecikmesi etkisini dışarıda bırakın.
Eğer ısrarla uzak ağ üzerinde test yapmak isterseniz, bant genişliğini artırarak (bant genişliğinin yeterli olduğundan emin olun) eşzamanlı kullanıcı sayısını artırarak verimliliği artırabilirsiniz.

### Neden nginx vekilinde performans düşüyor?
Nginx'in çalışması sistem kaynaklarına ihtiyaç duyar. Aynı zamanda, Nginx ve webman arasındaki iletişim de belli bir kaynağı tüketir.
Ancak, sistem kaynakları sınırlıdır, webman tüm sistem kaynaklarına erişemez, bu nedenle tüm sistemin performansının biraz düşmesi normaldir.
Nginx vekilinin performans etkisini mümkün olduğunca azaltmak için, Nginx günlüğünü kapatmayı düşünebilirsiniz (`access_log off;`), Nginx ile webman arasındaki keep-alive'i açmayı düşünebilirsiniz, bkz. [nginx proxy](nginx-proxy.md).
Ayrıca, https ve http karşılaştırıldığında, https daha fazla kaynak tüketir, çünkü https SSL/TLS el sıkışma gerektirir, veri şifreleme/şifre çözme yapar, paket boyutları daha büyük olur ve daha fazla bant genişliği kaplar, bu da performans düşüşüne neden olabilir.
Eğer kısa süreli bağlantılarla test yapıyorsanız (HTTP keep-alive açık değilse), her istekte ek SSL/TLS el sıkışması yapılması gerekeceğinden, performans önemli ölçüde düşebilir. Bu nedenle, https testi yaparken HTTP keep-alive'ı açmanız önerilir.

### Sistem performansının limitine ulaşıldığını nasıl anlarız?
Genellikle CPU %100 olduğunda sistem performansının sınırına ulaşıldığını gösterir. Eğer CPU boşluk varsa, henüz bir sınırdayızdır, bu durumda eşzamanlı kullanıcı sayısını artırarak QPS'yi artırabiliriz.
Eğer eşzamanlı kullanıcı sayısını artırmak QPS'yi artırmıyorsa, olasıca webman işlem sayısı yetersizdir, webman işlem sayısını artırabilirsiniz. Hala artıramıyorsanız, bant genişliğinin yeterli olup olmadığını kontrol etmelisiniz.

### Neden webman'in göre performansı go'nun gin framework'ünden düşük?
[techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2)'ün performans testi, webman'in tüm metriklerde (metin, veritabanı sorgusu, veritabanı güncelleme vb.) gin'den yaklaşık iki kat daha yüksek olduğunu gösteriyor. Eğer sizin sonuçlarınız farklıysa, büyük ihtimalle webman'de ORM kullanmanız performans kaybına neden olmuştur, webman+native PDO ve gin+native SQL'in performansını karşılaştırabilirsiniz.

### webman'de ORM kullanmanın performansı ne kadar etkileyeceği?
Aşağıdaki bir performans testi veri setidir.

**Çevre**
Alibaba Cloud 4 çekirdekli 4G, 100.000 kayıttan rastgele bir veri sorgulama işlemi ardından JSON yanıtını dönderme.

**Eğer native PDO kullanılıyorsa**
webman QPS 1.78 bin'dir

**Eğer laravel'in Db::table() kullanılıyorsa**
webman QPS 0.94 bin'a düşer

**Eğer laravel'in Model kullanılıyorsa**
webman QPS 0.72 bin'a düşer

thinkORM sonuçları benzerdir, fark çok büyük değildir.

> **Not**
> ORM kullanmak performansı biraz düşürebilir ancak çoğu iş için yeterli olacaktır. Geliştirme hızı, bakım kolaylığı, performans gibi birkaç ölçüt arasında denge bulmalıyız, sadece performansı takip etmemeliyiz.

### Neden apipost ile yapılan performans testinde QPS çok düşük?
Apipost'un performans test modülünde bir hata bulunmaktadır, sunucu gzip başlığını döndürmediği sürece keep-alive'ı koruyamaz, bu durumda performans önemli ölçüde düşer.
Bu sorunu çözmek için veri sıkıştırarak ve gzip başlığını ekleyerek yanıt vermeniz gerekmektedir, örneğin,
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
Bunun dışında, apipost bazı durumlarda tatmin edici bir performans gösteremeyebilir, aynı ve eşzamanlı kullanıcı sayılarıyla, ab'ye göre QPS yaklaşık %50 daha düşük olabilir. Performans testleri için apipost'tan ziyade ab veya wrk gibi profesyonel performans test yazılımlarını kullanmanız önerilir.

### Uygun işlem sayısını ayarlamak
webman varsayılan olarak CPU * 4 işlem sayısını açar. Aslında, ağ giriş/çıkışı olmayan helloworld işlemleri için işlem sayısınızı CPU çekirdeği sayınızla aynı yaparak en iyi performansı elde edebilirsiniz, çünkü işlem değişim maliyetlerini azaltabilirsiniz.
Eğer engelleyici I/O ile ilgili bir işiniz varsa, işlem sayınızı CPU'nun 3-8 katına ayarlayabilirsiniz, çünkü bu durumda daha fazla işlemle daha fazla eşzamanlı kullanıcıyı desteklemek için daha fazla işleme ihtiyaç vardır, ayrıca engelleyici I/O durumunda işlem değişim maliyeti göz ardı edilebilir.

### Performans testinin bazı referans aralıkları

**Bulut sunucusu 4 çekirdekli 4G 16 işlem  yerel ağ/yerel testler**

| - | Keep-alive açık | Keep-alive kapalı |
|--|-----|-----|
| merhaba dünya | 8-16 bin QPS | 1-3 bin QPS |
| tekli veritabanı sorgusu | 1-2 bin QPS | 1 bin QPS |

[**Üçüncü taraf techempower performans testi verileri**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Performans testi komut örnekleri

**ab**
```
# 100.000 istek 200 eşzamanlı kullanıcı keep-alive açık
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100.000 istek 200 eşzamanlı kullanıcı keep-alive kapalı
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# 200 eşzamanlı kullanıcı ile 10 saniye performans testi keep-alive (varsayılan olarak açık)
wrk -c 200 -d 10s http://example.com
```
