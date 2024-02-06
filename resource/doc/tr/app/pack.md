# Paketleme

Örneğin, foo uygulama eklentisini paketlemek için:

- `plugin/foo/config/app.php` dosyasında sürüm numarasını ayarlayın (**önemli**)
- Paketlenmesine gerek olmayan dosyaları `plugin/foo` içerisinden silin, özellikle `plugin/foo/public` altında geçici dosyaları yükleyerek test ettiyseniz
- Veritabanı ve Redis yapılandırmalarını silin; eğer projeniz kendi bağımsız veritabanı ve Redis yapılandırmalarına sahipse, bu yapılandırmalar ilk uygulamayı ziyaret ettiğinde kurulum sihirbazını tetiklemeli ve yöneticinin manuel olarak doldurmasını ve oluşturmasını sağlamalıdır (bu kısmı kendiniz uygulamanız gerekebilir).
- Diğer orijinal haline geri dönmek istediğiniz dosyaları geri yükleyin
- Yukarıdaki adımları tamamladıktan sonra `{ana proje}/plugin/` dizinine gidin ve `zip -r foo.zip foo` komutunu kullanarak foo.zip dosyasını oluşturun
