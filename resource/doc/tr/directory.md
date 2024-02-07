# Dosya Yapısı
```
.
├── app                           Uygulama dizini
│   ├── controller                Kontrolcü dizini
│   ├── model                     Model dizini
│   ├── view                      Görünüm dizini
│   ├── middleware                Ara yazılım dizini
│   │   └── StaticFile.php        Kendi statik dosya ara yazılımı
|   └── functions.php             İşlev özelleştirme fonksiyonları bu dosyada yazılır
|
├── config                        Yapılandırma dizini
│   ├── app.php                   Uygulama yapılandırması
│   ├── autoload.php              Otomatik yüklenecek dosyaların yapılandırılması
│   ├── bootstrap.php             İşlem başlatıldığında onWorkerStart işlemine göre yapılandırma gerçekleştirme
│   ├── container.php             Konteyner yapılandırması
│   ├── dependence.php            Konteyner bağımlılığı yapılandırması
│   ├── database.php              Veritabanı yapılandırması
│   ├── exception.php             İstisna yapılandırması
│   ├── log.php                   Günlükleme yapılandırması
│   ├── middleware.php            Ara yazılım yapılandırması
│   ├── process.php               Özel işlem yapılandırması
│   ├── redis.php                 redis yapılandırması
│   ├── route.php                 Rota yapılandırması
│   ├── server.php                Port, işlem sayısı vb. sunucu yapılandırması
│   ├── view.php                  Görünüm yapılandırması
│   ├── static.php                Statik dosya açma/kapama ve statik dosya ara yazılımı yapılandırması
│   ├── translation.php           Çoklu dil yapılandırması
│   └── session.php               Oturum yapılandırması
├── public                        Statik kaynak dizini
├── process                       Özel işlem dizini
├── runtime                       Uygulama çalışma zamanı dizini, yazma iznine ihtiyaç duyar
├── start.php                     Hizmet başlatma dosyası
├── vendor                        Composer tarafından kurulan üçüncü taraf kitaplık dizini
└── support                       Kitaplık uyumluluğu (üçüncü taraf kitaplıkları içerir)
    ├── Request.php               İstek sınıfı
    ├── Response.php              Yanıt sınıfı
    ├── Plugin.php                Eklenti yükleme/kaldırma betiği
    ├── helpers.php               Yardımcı fonksiyonlar (işlev özelleştirme fonksiyonları app/functions.php içine yazılmalıdır)
    └── bootstrap.php             İşlem başladıktan sonra başlatılma betiği
```
