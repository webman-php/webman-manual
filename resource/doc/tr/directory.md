# Dosya Yapısı
```plaintext
.
├── app                           Uygulama dizini
│   ├── controller                Kontrolör dizini
│   ├── model                     Model dizini
│   ├── view                      Görünüm dizini
│   ├── middleware                Ara yazılım dizini
│   │   └── StaticFile.php        Dahili statik dosya ara yazılımı
|   └── functions.php             İşletme özel fonksiyonlar bu dosyaya yazılır
|
├── config                        Yapılandırma dizini
│   ├── app.php                   Uygulama yapılandırması
│   ├── autoload.php              Otomatik yüklenen dosyaların yapılandırması
│   ├── bootstrap.php             İşlem başlatıldığında onWorkerStart fonksiyonunun çalıştırılması yapılandırması
│   ├── container.php             Konteyner yapılandırması
│   ├── dependence.php            Konteyner bağımlılık yapılandırması
│   ├── database.php              Veritabanı yapılandırması
│   ├── exception.php             İstisna yapılandırması
│   ├── log.php                   Günlük yapılandırması
│   ├── middleware.php            Ara yazılım yapılandırması
│   ├── process.php               Özel işlem yapılandırması
│   ├── redis.php                 Redis yapılandırması
│   ├── route.php                 Rota yapılandırması
│   ├── server.php                Sunucu yapılandırması (port, işlem sayısı vb.)
│   ├── view.php                  Görünüm yapılandırması
│   ├── static.php                Statik dosya anahtarı ve statik dosya ara yazılımı yapılandırması
│   ├── translation.php           Çoklu dil yapılandırması
│   └── session.php               Oturum yapılandırması
├── public                        Statik kaynak dizini
├── process                       Özel işlem dizini
├── runtime                       Uygulama çalışma zamanı dizini, yazma iznine ihtiyaç duyar
├── start.php                     Hizmet başlatma dosyası
├── vendor                        Composer ile yüklenen üçüncü taraf kütüphaneler dizini
└── support                       Kütüphane uyumluluğu (üçüncü taraf kütüphaneleri dahil)
    ├── Request.php               İstek sınıfı
    ├── Response.php              Yanıt sınıfı
    ├── Plugin.php                Eklenti yükleme ve kaldırma betiği
    ├── helpers.php               Yardımcı fonksiyonlar (işletme özel fonksiyonlar app/functions.php içine yazılmalıdır)
    └── bootstrap.php             İşlem başlatıldıktan sonra başlatma betiği
```
