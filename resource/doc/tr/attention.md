# Programlama Bilgisi

## İşletim Sistemi
webman, hem linux hem de windows işletim sistemlerinde çalışabilir. Ancak, workerman'ın windows'ta çoklu işlem ve daemon işlemi desteği olmaması nedeniyle, windows sistemi sadece geliştirme ve hata ayıklama için önerilmektedir. Canlı ortamda linux sistemini kullanmanızı öneririz.

## Başlatma Yöntemi
**linux sistemleri** için, `php start.php start` (hata ayıklama modu) veya `php start.php start -d` (daemon modu) komutunu kullanarak başlatılır.
**windows sistemleri** için, `windows.bat` dosyasını çalıştırabilir veya `php windows.php` komutunu kullanarak başlatılır. Durdurmak için ctrl c tuşlarına basılır. Windows sistemleri, stop reload status reload connections gibi komutları desteklemez.

## Kalıcı Bellekte Tutma
webman, kalıcı bellekte tutulan bir framework'tür. Genellikle, php dosyaları belleğe yüklendikten sonra tekrar diskten okunmaz (şablon dosyaları hariç). Bu nedenle canlı ortamda iş kodları veya yapılandırma değişiklikleri etkili olabilmesi için `php start.php reload` komutunu çalıştırmak gereklidir. Eğer işlemle ilgili yapılandırmayı değiştiriyorsanız veya yeni bir composer paketi kurduysanız, `php start.php restart` komutunu çalıştırmalısınız.

> Geliştirme sürecini kolaylaştırmak için, webman modunda çalışan işlemi izlemek için özel bir monitor süreci içerir. İş dosyaları güncellendiğinde otomatik olarak yeniden yüklenir. Bu özellik sadece workerman debug modunda çalışırken (başlatma sırasında `-d` eklenmeden) etkindir. Windows kullanıcıları için bu özelliği etkinleştirmek için `windows.bat` veya `php windows.php` komutlarını çalıştırmalısınız.

## Çıkış İfadeleri Hakkında
Geleneksel php-fpm projelerinde, `echo` `var_dump` gibi fonksiyonlarla veri çıktısı alınır ve bu çıktılar genellikle sayfada görüntülenir. Ancak webman'de, bu tür çıktılar genellikle terminalde görünür ve sayfada gösterilmez (şablon dosyaları hariç).

## `exit` ve `die` İfadeleri Kullanmayın
`die` veya `exit` komutlarını çalıştırmak, işlemi sonlandırır ve yeniden başlatır, bu da mevcut isteğin doğru şekilde yanıtlanmasını engeller.

## `pcntl_fork` Fonksiyonunu Kullanmayın
`pcntl_fork` fonksiyonu, yeni bir işlem oluşturur ve webman'da kullanılmasına izin verilmez.
