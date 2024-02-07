# İşlem Akışı

## Process Başlatma İşlem Akışı

php start.php start komutu çalıştırıldıktan sonra işlem akışı aşağıdaki gibidir:

1. Konfigürasyon altındaki config/ klasöründen yapılandırmalar yüklenir.
2. Worker ile ilgili yapılandırmalar, `pid_file`, `stdout_file`, `log_file`, `max_package_size` vb. ayarlanır.
3. webman işlemi oluşturulur ve (varsayılan olarak 8787 numaralı) bir bağlantı noktasında dinlenmeye başlar.
4. Yapılandırmalara göre özel işlemler oluşturulur.
5. webman işlemi ve özel işlemler başlatıldıktan sonra aşağıdaki mantık çalıştırılır (hepsi onWorkerStart içinde çalıştırılır):
   ① `config/autoload.php` içinde belirtilen dosyalar yüklenir, örneğin `app/functions.php`.
   ② `config/middleware.php` (içerisinde `config/plugin/*/*/middleware.php` gibi) içinde belirtilen orta yazılımlar yüklenir.
   ③ `config/bootstrap.php` (içerisinde `config/plugin/*/*/bootstrap.php` gibi) içinde belirtilen sınıfların başlat metodu çalıştırılır; bu, modülleri başlatmak için kullanılır, örneğin Laravel veritabanı bağlantısının başlatılması.
   ④ `config/route.php` (içerisinde `config/plugin/*/*/route.php` gibi) içinde tanımlanan yönlendirmeler yüklenir.

## İstek İşleme Akışı

1. İstek URL'sinin public klasöründeki statik dosyalara karşılık gelip gelmediği kontrol edilir; eğer öyleyse dosya döndürülür (isteği sonlandırılır), değilse adıma 2'ye geçilir.
2. URL'ye göre belirli bir yönlendirmenin eşleşip eşleşmediği kontrol edilir; eğer eşleşme olmazsa adıma 3'e geçilir, eşleşme olursa adıma 4'e geçilir.
3. Varsayılan yönlendirmenin kapatılıp kapatılmadığı kontrol edilir; eğer kapatılmışsa 404 hatası döndürülür (isteği sonlandırılır), kapatılmamışsa adıma 4'e geçilir.
4. İstek için belirli bir denetleyiciye karşılık gelen orta yazılım bulunur, sırayla orta yazılımın ön işlemleri çalıştırılır (soğan modeli istek aşaması), denetleyici iş mantığı çalıştırılır, orta yazılımın son işlemleri çalıştırılır (soğan modeli yanıt aşaması), istek sona erdirilir. (Bkz. [Middleware Onion Model](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
