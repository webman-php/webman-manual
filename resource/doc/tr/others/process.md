# İşlem Akışı

## İşlem Başlatma Akışı

`php start.php start` komutu çalıştırıldıktan sonra işlem akışı aşağıdaki gibidir:

1. config/ klasöründeki yapılandırmaları yükle
2. Worker ile ilgili yapılandırmaları ayarla, örneğin: `pid_file`, `stdout_file`, `log_file`, `max_package_size`
3. webman işlemini oluştur ve (varsayılan olarak 8787 portunu) dinle
4. Yapılandırmalara göre özel işlem oluştur
5. webman işlemi ve özel işlem oluşturulduktan sonra aşağıdaki mantığı çalıştır (hepsi onWorkerStart içinde çalıştırılır):
  ① `config/autoload.php` dosyasında belirtilen dosyaları yükle, örneğin: `app/functions.php`
  ② `config/middleware.php` dosyasını yükle (bu, `config/plugin/*/*/middleware.php` içini de içerir) ve belirtilen ara yazılımı çalıştır
  ③ `config/bootstrap.php` dosyasını çalıştır (bu, `config/plugin/*/*/bootstrap.php` içini de içerir) ve modülleri başlatmak için belirtilen sınıfın başlatma yöntemini çalıştır, örneğin: Laravel veritabanı bağlantısını başlatmak
  ④ `config/route.php` dosyasını yükle (bu, `config/plugin/*/*/route.php` içini de içerir) ve belirtilen rotaları tanımla

## İstek İşleme Akışı
1. İstek URL'sinin public klasöründeki statik dosyalarla eşleşip eşleşmediğini kontrol et, eğer eşleşiyorsa dosyayı geri döndür (istek sonlandır), eşleşmiyorsa 2'ye geç
2. URL'ye göre herhangi bir rota eşleşip eşleşmediğini kontrol et, eşleşmezse 3'e geç, eşleşirse 4'e geç
3. Varsayılan rotaların kapatılıp kapatılmadığını kontrol et, eğer kapatılmışsa 404 döndür (istek sonlandır), kapatılmamışsa 4'e geç
4. İstek için kontrolcünün ara yazılımlarını bul, sırayla ara yazılım ön işlemlerini çalıştır (soğan modeli istek aşaması), kontrolcü iş mantığını çalıştır, ara yazılım son işlemlerini çalıştır (soğan modeli yanıt aşaması), isteği sonlandır (Middleware Soğan Modeli'ne bakın: [Middleware Soğan Modeli](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
