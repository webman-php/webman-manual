# webman Performansı


### Geleneksel çerçeve istek işleme süreci

1. Nginx/apache isteği alır
2. Nginx/apache isteği php-fpm'ye iletilir
3. Php-fpm çevreyi başlatır, örneğin değişken listesi oluşturur
4. Php-fpm her bir uzantı/modülün RINIT'ini çağırır
5. Php-fpm, php dosyasını okur (opcache kullanarak bu adım atlanabilir)
6. Php-fpm leksik analiz, sözdizimi analiz eder, opcode'a derler (opcache kullanarak bu adım atlanabilir)
7. Php-fpm, opcode'u çalıştırır, 8, 9, 10, 11 adımları içerir
8. Çerçeve başlatılır, örneğin konteyner, denetleyici, yönlendirme, ara katman gibi çeşitli sınıflar örneklendirilir.
9. Çerçeve veritabanına bağlanır ve yetkilendirme yapar, redis'e bağlanır
10. Çerçeve iş mantığını yürütür
11. Çerçeve veritabanı ve redis bağlantısını kapatır
12. Php-fpm kaynağı serbest bırakır, tüm sınıf tanımlarını yok eder, örnekleri yok eder, sembol tablosunu yok eder
13. Php-fpm her bir uzantı/modülün RSHUTDOWN metodunu sırayla çağırır
14. Php-fpm sonucu nginx/apache'e iletilir
15. Nginx/apache sonucu istemciye geri gönderir


### webman'in istek işleme süreci
1. Çerçeve isteği alır
2. Çerçeve iş mantığını yürütür
3. Çerçeve sonucu istemciye geri gönderir

Evet, nginx'in ters proxy olmadığı durumlarda, çerçeve sadece bu 3 adıma sahiptir. Bu, webman'in performansının geleneksel çerçevelerin birkaç katı hatta onlarca katı olduğu anlamına gelebilir.

Daha fazla bilgi için [Basınç Testi](benchmarks.md) sayfasına bakabilirsiniz.
