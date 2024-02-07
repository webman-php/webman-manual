# Güvenlik

## Çalışan Kullanıcı
Önerilen, çalışan kullanıcıyı daha düşük bir izinle belirlemektir, örneğin nginx ile aynı kullanıcı. Çalışan kullanıcı, `config/server.php` dosyasında `user` ve `group` alanlarında ayarlanır. Benzer şekilde, özel süreçlerin kullanıcıları `config/process.php` dosyasındaki `user` ve `group` ile belirlenir. Dikkat edilmesi gereken bir nokta, monitor sürecinin çalışan kullanıcı olarak ayarlanmamasıdır, çünkü normal çalışabilmesi için yüksek izinlere ihtiyaç duyar.

## Denetleyici Standartları
`controller` dizini veya alt dizinler sadece denetleyici dosyalarını içerir, diğer sınıf dosyalarının konulması yasaktır. Aksi takdirde [denetleyici eki](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) açılmadığı durumda, sınıf dosyaları URL'den izinsiz erişilebilir ve öngörülemeyen sonuçlara yol açabilir. Örneğin, `app/controller/model/User.php` gerçekte bir Model sınıfıdır, ancak yanlışlıkla `controller` dizinine konulmuştur. [Denetleyici eki](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) açılmadığı durumda, kullanıcıların `/model/user/xxx` gibi bir URL ile `User.php` dosyasındaki herhangi bir yönteme erişmesine neden olabilir. Bu durumu tamamen ortadan kaldırmak için, hangi dosyaların denetleyici dosyası olduğunu açıkça belirten [denetleyici eki](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) kullanmanızı kesinlikle tavsiye ederiz.

## XSS Filtresi
Genel kullanıma uygun olması nedeniyle, webman istekleri XSS dönüştürmez. webman, XSS dönüşümünün veri sunulurken yapılmasını şiddetle önerirken, veri tabanına girmeden önce kaçınmanın daha iyi olduğunu belirtmektedir. Ayrıca twig, blade, think-template gibi şablonlar otomatik olarak XSS dönüşümü yapar, bu nedenle manuel dönüşüme gerek kalmaz, oldukça kullanışlıdır.

> **İpucu** 
> Veri tabanına girmeden önce XSS dönüşümü yaparsanız, bazı uygulama eklentileriyle uyumsuzluk sorunlarına neden olabilirsiniz.

## SQL Enjeksiyonunu Engelleme
SQL enjeksiyonunu engellemek için mümkün olduğunca ORM kullanmanızı öneririz, örneğin [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html), [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html) gibi. Mümkünse kendiniz SQL sorgularını birleştirmemeye çalışın.

## nginx Proxy
Uygulamanızın dış dünyaya açılması gerektiğinde, webman'in önüne bir nginx proxy eklemek güvenliği artırabilir ve bazı yasadışı HTTP isteklerini filtreleyebilir. Detaylar için [nginx proxy](nginx-proxy.md) sayfasına bakabilirsiniz.
