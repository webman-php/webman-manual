# Güvenlik

## Kullanıcı Yürütme
nginx kullanıcı ile uyumlu olacak şekilde düşük izinli bir kullanıcı olarak yürütme kullanılması önerilir. Çalıştırma kullanıcısı `config/server.php` içinde `user` ve `group` olarak ayarlanmalıdır. Benzer şekilde özel süreç kullanıcısı `config/process.php` içinde `user` ve `group` ile belirlenir. Monitor sürecinin kullanıcı ayarları yapılmamalıdır, çünkü normal çalışabilmesi için yüksek izinlere ihtiyaç duyar.

## Denetleyici Standardı
`controller` dizini veya alt dizinler sadece denetleyici dosyalarını içerebilir, diğer sınıf dosyalarının yerleştirilmesi yasaktır. Aksi takdirde, [Denetleyici Soneki](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) kapalıyken, sınıf dosyaları URL'den yasadışı erişilebilir, öngörülemeyen sonuçlara yol açabilir. Örneğin, `app/controller/model/User.php` aslında Model sınıfıdır ancak yanlışlıkla `controller` dizinine yerleştirildiği durumda, [Denetleyici Soneki](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) kapalıyken, kullanıcıların `/model/user/xxx` gibi bir URL ile `User.php` dosyasının herhangi bir yöntemine erişmesine neden olabilir. Bu tür durumların tamamen önlenmesi için, hangi dosyaların denetleyici dosyası olduğunu belirlemek için [Denetleyici Soneki](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) kullanılması kesinlikle önerilir.

## XSS Filtreleme
Genellikle, webman istekleri XSS kaçışı yapmaz. Webman, adımında XSS kaçışı yapılmasını şiddetle önerir ve veritabanına girmeden önce kaçış işleminin yapılmasını önermez.Bunun yanı sıra twig, blade, think-tmplate gibi şablonlar otomatik olarak XSS kaçışını gerçekleştirir, manuel kaçış gerekmez, bu oldukça kullanışlıdır.

> **İpucu**
> Veritabanına girmeden önce XSS kaçışı yaparsanız, bazı uygulama eklentileriyle uyumsuzluk sorunlarına neden olabilirsiniz

## SQL Enjeksiyonunu Önleme
SQL enjeksiyonunu önlemek için lütfen ORM'i kullanmaya çalışın, [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) ya da [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html) gibi. Kendi SQL sorgularınızı mümkün olduğunca oluşturmamaya özen gösterin.

## nginx Proxy'si
Uygulamanızın dış dünyaya açılması gerektiğinde, webman'ın önüne bir nginx proxy eklemek kesinlikle önerilir, bu şekilde bazı yasadışı HTTP isteklerini filtreleyerek güvenliği artırabilirsiniz. Ayrıntılar için [nginx proxy](nginx-proxy.md) sayfasına bakınız.
