# webman Performans


### Geleneksel Çerçeve İstek İşleme Süreci

1. nginx/apache isteği alır
2. nginx/apache isteği php-fpm'ye iletişim kurar
3. php-fpm ortamı başlatır, değişken listesi oluşturur
4. php-fpm, her bir uzantının/modülün RINIT'ini çağırır
5. php-fpm, php dosyasını diskten okur (opcache kullanarak bu adım atlanabilir)
6. php-fpm, leksik analiz, sözdizimsel analiz yapar ve opcode'ye derler (opcache kullanarak bu adım atlanabilir)
7. php-fpm, opcode'yi çıkarır, içerisinde 8,9,10,11 bulunur
8. Çerçeve başlatılır, bir sürü sınıf örneği alır, içerisinde konteyner, denetleyici, yönlendirici, ara birim vb. bulunur.
9. Çerçeve veritabanına bağlanır ve izin doğrular, redis bağlantısı kurar
10. Çerçeve iş mantığını yürütür
11. Çerçeve veritabanı ve redis bağlantısını kapatır
12. php-fpm kaynakları serbest bırakır, tüm sınıf tanımlarını, örnekleri imha eder, sembol tablosunu yok eder vb.
13. php-fpm, her bir uzantının/modülün RSHUTDOWN yöntemlerini sırayla çağırır
14. php-fpm sonucu nginx/apache'e yönlendirir
15. nginx/apache sonucu istemciye döndürür


### webman İstek İşleme Süreci
1. Çerçeve isteği alır
2. Çerçeve iş mantığını yürütür
3. Çerçeve sonucu istemciye döndürür

Evet, nginx proxy olmadan, çerçevenin sadece bu 3 adımı vardır. Bu, php çerçevesinin son noktasıdır ve webman'ın performansının geleneksel çerçeve performansının birkaç katı hatta onlarca katı olmasını sağlar.

Daha fazla bilgi için [Baskı Testi](benchmarks.md) sayfasına bakabilirsiniz.
