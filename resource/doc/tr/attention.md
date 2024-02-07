# Programlama Bilgisi

## İşletim Sistemi
webman hem linux hem de windows sistemlerinde çalışabilir. Ancak workerman'ın windows'ta çoklu işlemi desteklemediği ve daemon işlemini desteklemediği için, windows sisteminin yalnızca geliştirme ortamında kullanılması önerilir. Canlı ortamda linux sistemi kullanılmalıdır.

## Başlatma Yöntemi
**linux sistemlerinde**, `php start.php start` komutu (debug hata ayıklama modunda) veya `php start.php start -d` komutu (daemon modunda) ile başlatılır.
**windows sistemlerinde** ise `windows.bat` dosyasını çalıştırabilir veya `php windows.php` komutunu kullanarak başlatabilirsiniz. Durdurmak için ctrl+c tuşları kullanılır. Windows sistemleri stop, reload, status, reload connections gibi komutları desteklemez.

## Sürekli Bellek Kullanımı
webman sürekli bellek kullanımına sahip bir framework'tür. Genellikle, bir PHP dosyası belleğe yüklendikten sonra tekrar diskten okunmaz (şablon dosyaları hariç). Bu nedenle, canlı ortamda işlem kodu veya yapılandırma değişiklikleri yapıldıktan sonra, etkin olabilmesi için `php start.php reload` komutunu çalıştırmak gereklidir. Eğer işlemle ilgili yapılandırmalar değiştirildiyse veya yeni bir composer paketi yüklendiyse, `php start.php restart` ile yeniden başlatılması gerekir.

> Geliştirme sürecini kolaylaştırmak için, webman otomatik olarak dosya güncellemelerini izlemek için özel bir izleme süreci içerir. İşlem dosyaları güncellendiğinde otomatik olarak reload işlemi gerçekleştirilir. Bu özellik yalnızca workerman debug modunda çalıştığında (başlatılırken `-d` eklenmez) kullanılabilir. Windows kullanıcıları için ise `windows.bat` dosyasını çalıştırmaları veya `php windows.php` komutunu çalıştırmaları gerekir.

## Çıktı İfadeleri Hakkında
Geleneksel php-fpm projelerinde `echo`, `var_dump` gibi fonksiyonlar verileri doğrudan sayfada gösterirken, webman'de bu çıktılar genellikle terminalde görüntülenir ve sayfada görüntülenmez (şablon dosyalarındaki çıktılar hariç).

## `exit` veya `die` İfadelerini Kullanmayın
Die veya exit komutları işlemi sonlandıracağı ve yeniden başlatacağı için, mevcut isteğin doğru şekilde yanıtlanamamasına neden olur.

## `pcntl_fork` Fonksiyonunu Kullanmayın
`pcntl_fork` fonksiyonu bir işlem oluşturur, ancak webman içinde kullanılmamalıdır.
