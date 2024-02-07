# Performans Testi

## Performans test sonuçları hangi faktörlerden etkilenir?
* Stres makinesi ile sunucu arasındaki ağ gecikmesi (yerel ağ veya yerel makine testi önerilir)
* Stres makinesi ile sunucu arasındaki bant genişliği (yerel ağ veya yerel makine testi önerilir)
* HTTP keep-alive özelliğinin açılıp-açılmadığı (önerilen açık olması)
* Eş zamanlı sayı yeterli mi (dış ağ testleri için daha yüksek eş zamanlı sayı önerilir)
* Sunucu işlem sayısı uygun mu (helloworld işlemleri için işlem sayısı önerilen CPU sayısıyla aynı olmalıdır, veritabanı işlemleri için işlem sayısı CPU'nun dört katı veya daha fazlası olmalıdır)
* İşletmenin kendi performansı (örneğin, dış ağ veritabanı kullanılıp kullanılmadığı)

## HTTP keep-alive nedir?
HTTP Keep-Alive mekanizması, tek bir TCP bağlantısı üzerinden birden çok HTTP isteği ve yanıt göndermek için kullanılan bir tekniktir. Performans test sonuçları üzerinde büyük bir etkiye sahiptir ve keep-alive kapatıldığında QPS (saniyedeki istek sayısı) katlanarak düşebilir. Şu anda, tarayıcılar genellikle keep-alive'ı varsayılan olarak açık durumda tutarlar, yani tarayıcı bir HTTP adresine eriştiğinde bağlantıyı geçici olarak kapatmaz ve sonraki isteklerde bu bağlantıyı tekrar kullanır, bu da performansı artırır. Performans testleri sırasında keep-alive'ın açık olması önerilir.

## HTTP keep-alive nasıl açılır?
Eğer ab programını kullanarak bir performans testi yapılıyorsa, -k parametresini eklemek gerekir, örnek olarak `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
apipost, keep-alive'ı etkinleştirmek için dönüş başlığında gzip başlığını döndürmelidir (apipost'ta bir hata mevcut, aşağıya bakınız).
Diğer performans test programları genellikle varsayılan olarak açık durumda olacaktır.

## Neden dış ağda test edildiğinde QPS çok düşük?
Dış ağ gecikmesi çok yüksek olduğunda QPS'nin düşük olması normal bir durumdur. Örneğin, baidu sayfasını test etmek, QPS'nin sadece birkaç düzine olmasına neden olabilir. İç ağ veya yerel makine testi yapılması önerilir, ağ gecikmesi etkisini ortadan kaldırmak için. Dış ağda test yapmak zorunda kalınıyorsa, bant genişliğini gerektiği gibi sağlamak için eş zamanlı sayıyı artırarak verimliliği artırabilirsiniz.

## Nginx proxy'den sonra neden performans düşer?
Nginx'in çalışması sistem kaynaklarını tüketir. Ayrıca, Nginx ve webman arasındaki iletişim de belirli kaynaklar tüketir. Ancak, sistem kaynakları sınırlıdır ve webman tüm sistem kaynaklarına erişemeyebilir, bu nedenle bütün sistemin performansının azalması normal bir durumdur. Nginx proxy'nin getirdiği performans etkisini mümkün olduğunca azaltmak için, Nginx günlüğünü kapatmayı düşünebilirsiniz (`access_log off;`), Nginx ile webman arasında keep-alive'ı etkinleştirebilirsiniz, bkz. [nginx proxy](nginx-proxy.md).
Ayrıca, https'nin http'ye göre daha fazla kaynak harcaması normaldir çünkü https, SSL/TLS el sıkışması, veri şifreleme/çözme ve paket boyutunda artış gibi nedenlerle daha fazla kaynak tüketir, bu durum performansın düşmesine neden olabilir. Eğer performans testi kısa bağlantılar kullanıyorsa (HTTP keep-alive açık değilse), her istek için ek SSL/TLS el sıkışması iletişimi gerektirir ve performans önemli ölçüde düşer. Bu nedenle, https testlerinde HTTP keep-alive'ın açık olduğu bir şekilde test yapılması önerilir.

## Sistem performansının sınıra ulaştığını nasıl bilebilirim?
Genellikle, CPU'nun %100 olduğu durumda sistem performansının sınırda olduğunu söyleyebiliriz. Eğer CPU'nun hala boş olduğunu görüyorsanız, sınırda olmadığınızı gösterir, bu durumda eş zamanlı sayıyı artırarak QPS'yi artırabilirsiniz. Eğer eş zamanlı sayıyı artırmak QPS'yi artırmıyorsa, bu webman işlem sayısının yetersiz olabileceği anlamına gelir, bu durumda webman işlem sayısını artırabilirsiniz. Eğer bu durumda da QPS artmıyorsa, bant genişliğinin yeterli olup olmadığını kontrol etmelisiniz.

## Neden webman'in performansı go dilindeki gin çerçevesinden daha düşük olarak çıkıyor?
[techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) testleri, webman'in metin, veritabanı sorgusu, veritabanı güncellemesi gibi tüm ölçümlerde gin'den yaklaşık iki kat daha iyi olduğunu göstermektedir. Eğer sizin sonuçlarınız farklıysa, bu muhtemelen webman'da ORM kullanmanızın getirdiği büyük bir performans kaybından kaynaklanabilir, webman+native PDO ve gin+native SQL karşılaştırabilirsiniz.

## Webman'da ORM kullanmak ne kadar performans kaybına neden olur?
Aşağıdaki bir performans testi veri setidir.

**Ortam**
Alibaba Cloud, 4 çekirdek 4GB, 100.000 kayıttan rastgele bir tane sorgulanarak JSON döndürülüyor.

**Eğer native PDO kullanılıyorsa**
webman'in QPS'i 17,800

**Eğer Laravel Db::table() kullanılıyorsa**
webman'in QPS'i 9,400

**Eğer Laravel Model kullanılıyorsa**
webman'in QPS'i 7,200

ThinkORM sonuçları benzer, küçük farklılıklar gösterebilir.

> **Not**
> ORM kullanmanın bir miktar performans kaybına neden olmasına rağmen, çoğu işletme için bu kullanmaya değer bir durumdur. Geliştirme verimliliği, sürdürülebilirlik, performans gibi birden çok kriter arasında denge bulmalıyız.

## Neden apipost ile yapılan performans testi sonuçları düşük?
Apipost'un performans test modülünde bir hata bulunmaktadır, eğer sunucu gzip başlığını döndürmezse keep-alive sağlanamaz ve performans büyük ölçüde düşer. çözüm olarak, veri döndürülürken verinin sıkıştırılması ve gzip başlığının eklenmesi gerekmektedir, örneğin,
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
Bunun dışında, apipost'un bazı durumlarda istenilen performansı elde edememesi, aynı eş zamanlılıkla apipost'un ab'ye kıyasla yaklaşık %50 daha düşük QPS üretmesi şeklinde ortaya çıkabilir. Performans testleri için ab, wrk veya diğer profesyonel test yazılımlarının kullanılması önerilir.

## Uygun işlem sayısının ayarlanması
webman, varsayılan olarak cpu*4 işlem sayısını açar. Aslında, ağ gecikmeli olmayan helloworld işlemleri için işlem sayısını CPU çekirdek sayısıyla aynı olacak şekilde ayarlamak performans açısından en iyisi olabilir, bu işlem maliyetlerini azaltabileceği için. Veritabanı, redis vb. blokaj durumundaki I/O işlemlerine sahipse, işlem sayısı CPU'nun 3-8 katı olarak ayarlanabilir, çünkü bu durumda daha fazla işlem gereklidir ve I/O bloklama durumunda işlem geçiş maliyeti neredeyse göz ardı edilebilir.

## Performans Testi İçin Referans Aralıkları

**Bulut Sunucusu 4 Çekirdek 4GB 16 İşlem  Yerel Ağ/Yerel Makine Testi**

| - | Keep-alive Açık | Keep-alive Kapalı |
|--|-----|-----|
| hello world | 80-160 bin QPS | 10-30 bin QPS |
| Veritabanı tek sorgu | 10-20 bin QPS | 10 bin QPS |

[**Techempower Üçüncü Taraf Performans Testi Verileri**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

## Performans Testi Komut Örnekleri

**ab**
```bash
# 100,000 istek 200 eş zamanlı istemci Keep-alive açık
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100,000 istek 200 eş zamanlı istemci Keep-alive kapalı
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```bash
# 200 eş zamanlı istemci ile 10 saniye süren test Keep-alive (varsayılan olarak)
wrk -c 200 -d 10s http://example.com
```
