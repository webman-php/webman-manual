# Paketleme

Örneğin, foo uygulama eklentisini paketlemek için:

* Önemli olan şey, `plugin/foo/config/app.php` dosyasında versiyon numarasını ayarlamak
* Paketlemeye gerek olmayan dosyaları (`plugin/foo/public` içinde özellikle yüklemeyi test etmek için geçici dosyaları) `plugin/foo` içerisinden silmek
* Veritabanı ve Redis yapılandırmalarını silmek. Eğer projeniz kendi bağımsız veritabanı ve Redis yapılandırmalarını içeriyorsa, bu yapılandırmaları uygulamanın ilk erişiminde kurulum rehberini tetiklemeli (kendi uygulamanızı uygulamalısınız) ve yöneticinin elle doldurmasını ve oluşturmasını sağlamalısınız.
* Diğer dosyaların orijinal hallerine geri dönmek
* Yukarıdaki adımları tamamladıktan sonra, `{ana proje}/plugin/` dizinine girip `zip -r foo.zip foo` komutunu kullanarak foo.zip dosyasını oluşturmak
