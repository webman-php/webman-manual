# webman Nedir

webman, [workerman](https://www.workerman.net) üzerine geliştirilen yüksek performanslı bir HTTP hizmet çerçevesidir. webman, geleneksel php-fpm mimarisini değiştirerek yüksek performanslı ve ölçeklenebilir HTTP hizmetleri sunar. webman ile web sitesi geliştirebilir, HTTP arayüzleri veya mikro hizmetler oluşturabilirsiniz.

Ayrıca, webman özel işlemleri destekler ve workerman'ın yapabildiği her şeyi yapabilir, örneğin websocket hizmetleri, nesnelerin interneti, oyunlar, TCP hizmetleri, UDP hizmetleri, unix soket hizmetleri gibi.

# webman Felsefesi
**En küçük çekirdek ile maksimum genişletilebilirlik ve en yüksek performansı sunar.**

webman sadece temel işlevleri (yönlendirme, ara yazılım, oturum, özel işlem arabirimi) sunar. Diğer tüm işlevleri composer ekosistemini kullanarak tekrar kullanılabilir, bu da webman'da en tanıdık işlev bileşenlerini kullanabileceğiniz anlamına gelir. Örneğin, veritabanı tarafında geliştirici, Laravel'ın `illuminate/database`'ini, ThinkPHP'nin `ThinkORM`'unu veya diğer bileşenleri like `Medoo` kullanabilir. Bu bileşenleri webman'e entegre etmek oldukça kolaydır.

# webman'ın Sunduğu Özellikler

1. Yüksek İstikrar: webman, workerman üzerine kurulmuştur ve endüstride hatasız ve yüksek stabiliteli soket çerçevesidir.
2. Süper Yüksek Performans: webman'ın performansı geleneksel php-fpm çerçevelerine göre 10-100 kat daha yüksektir ve go dilinin gin echo gibi çerçevelerinden yaklaşık olarak iki kat daha yüksektir.
3. Yeniden Kullanılabilir: Çoğu composer bileşen ve kütüphanesini değiştirmeden tekrar kullanabilirsiniz.
4. Yüksek Genişletilebilirlik: Özelleştirilmiş işlemleri destekler, workerman'ın yapabildiği her şeyi yapabilirsiniz.
5. Son Derece Basit ve Kullanışlı: Öğrenme maliyeti çok düşük, kod yazma konusunda geleneksel çerçevelerle farkı yoktur.
6. MIT açık kaynak lisansı ile kullanımı çok serbesttir.

# Proje Adresi
GitHub: https://github.com/walkor/webman **Lütfen küçük yıldızlarınızı esirgemeyin**

Gitee: https://gitee.com/walkor/webman **Lütfen küçük yıldızlarınızı esirgemeyin**

# Üçüncü Taraf Yetkili Performans Test Verileri

![](../assets/img/benchmark1.png)

Veritabanı sorgulama işlemleri ile, webman tek başına 390.000 QPS'lik bir işlem kapasitesine ulaşır, geleneksel php-fpm mimarisindeki Laravel çerçevesine göre neredeyse 80 kat daha yüksektir.

![](../assets/img/benchmarks-go.png)

Veritabanı sorgulama işlemleri ile, webman, aynı türdeki go dilinin web çerçevelerinden yaklaşık olarak iki kat daha yüksek performans gösterir.

Yukarıdaki veriler [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf) sitesinden alınmıştır.
