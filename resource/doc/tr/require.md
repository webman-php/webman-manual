# Gerekli Ortam

## Linux İşletim Sistemi
Linux işletim sistemi `posix` ve `pcntl` uzantılarına bağımlıdır. Bu uzantılar genellikle PHP'de yerleşik olarak bulunur ve genellikle kurulmaları gerekmez.

Eğer Baota kullanıcısıysanız, sadece `pnctl_` ile başlayan fonksiyonları Baota üzerinden devre dışı bırakmanız yeterlidir.

`event` uzantısı zorunlu değildir, ancak daha iyi performans için bu uzantının kurulması önerilir.

## Windows İşletim Sistemi
webman, Windows işletim sisteminde çalışabilir, ancak çoklu işlem, daemon gibi özellikleri ayarlayamama sebebiyle, Windows sadece geliştirme ortamı olarak önerilmektedir. Üretim ortamında Linux işletim sistemi kullanılması tavsiye edilir.

Not: Windows işletim sisteminde `posix` ve `pcntl` uzantılarına bağımlı değildir.
