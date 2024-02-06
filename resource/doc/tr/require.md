# Gerekli Ortam

## Linux Sistemi
Linux sistemi `posix` ve `pcntl` uzantılarına bağımlıdır. Bu uzantılar genellikle PHP içinde yer alır ve genellikle ayrıca yüklenmeleri gerekmez.

Eğer bir Baota kullanıcısıysanız, `pnctl_` ile başlayan fonksiyonların Baota üzerinde devre dışı bırakılması yeterlidir.

`event` uzantısı zorunlu değildir, ancak daha iyi performans için bu uzantının kurulması önerilir.

## Windows Sistemi
Webman Windows sistemlerinde çalışabilir, ancak çoklu işlem, servis olarak çalıştırma gibi özelliklerin ayarlanamadığı için, Windows sadece geliştirme ortamı olarak kullanılmalıdır. Üretim ortamında Linux sistemi tercih edilmelidir.

Not: Windows sistemlerinde `posix` ve `pcntl` uzantılarına ihtiyaç duyulmaz.
