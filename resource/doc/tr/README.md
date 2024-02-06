# webman nedir

webman, yüksek performanslı bir HTTP hizmet çerçevesi olan [workerman](https://www.workerman.net) üzerine geliştirilmiştir. Webman, geleneksel php-fpm mimarisinin yerine geçerek, son derece yüksek performanslı ve ölçeklenebilir HTTP hizmetleri sağlar. Webman ile web sitesi geliştirebilir, HTTP arayüzleri veya mikro servisler oluşturabilirsiniz.

Bunun yanı sıra, webman, özelleştirilebilir süreçleri destekleyerek workerman'ın yapabildiği her şeyi yapabilir, örneğin websocket hizmetleri, nesnelerin interneti, oyunlar, TCP hizmetleri, UDP hizmetleri, unix soket hizmetleri vb.

# webman felsefesi
**En küçük çekirdek ile maksimum genişleme yeteneği ve en güçlü performans.**

Webman sadece temel işlevleri (yönlendirme, ara yazılım, oturum, özelleştirilmiş süreç arayüzü) sağlar. Diğer tüm işlevler composer ekosistemi ile yeniden kullanılabilir, bu da webman'de en tanıdık bileşenleri kullanabileceğiniz anlamına gelir. Örneğin, veritabanı tarafında geliştirici Laravel'ın `illuminate/database`'ini seçebilir, aynı zamanda ThinkPHP'nin `ThinkORM`'unu veya diğer bileşenleri like `Medoo`. Onları webman'e entegre etmek çok kolaydır.

# webman'ın özellikleri

1. Yüksek istikrar. Webman, workerman'a dayalı olduğundan, endüstride neredeyse hiç hata olmayan yüksek istikrarlı bir soket çerçevesidir.

2. Süper yüksek performans. Webman'ın performansı geleneksel php-fpm çerçevelerinden yaklaşık 10-100 kat daha yüksektir ve go dilinin gin echo gibi çerçevelerinden yaklaşık olarak iki kat daha yüksektir.

3. Yüksek yeniden kullanım. Değişiklik yapmadan, çoğu composer bileşenini ve kütüphaneyi yeniden kullanabilirsiniz.

4. Yüksek genişletilebilirlik. Özelleştirilmiş süreçleri destekler, workerman'ın yapabildiği her şeyi yapabilir.

5. Son derece kolay ve kullanımı kolaydır, öğrenme maliyeti çok düşüktür, kod yazma konusunda geleneksel çerçevelerle farkı yoktur.

6. MIT açık kaynak lisansıyla en esnek ve dostça kullanıcı lisansını kullanır.

# Proje adresi
GitHub: https://github.com/walkor/webman **Küçük yıldızlarınızı esirgemeyin**

Gitee: https://gitee.com/walkor/webman **Küçük yıldızlarınızı esirgemeyin**

# Üçüncü taraf güvenilir performans test verileri

![](../assets/img/benchmark1.png)

Veritabanı sorgusu ile, webman tek başına 390 bin QPS'ye ulaşarak, geleneksel php-fpm mimarisindeki Laravel çerçevesinden neredeyse 80 kat daha yüksek bir performans sergiler.

![](../assets/img/benchmarks-go.png)

Veritabanı sorgusu ile, webman, benzer türdeki go dilinin web çerçevesinden yaklaşık olarak iki kat daha yüksek performans sergiler.

Yukarıdaki veriler [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf) adresinden alınmıştır.
