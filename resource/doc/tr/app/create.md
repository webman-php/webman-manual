# Uygulama Eklentisi Oluşturma

## Benzersiz Kimlik

Her eklentinin benzersiz bir uygulama kimliği vardır. Geliştirici, gelişmeden önce bir kimlik düşünmeli ve kimliğin kullanılmadığını kontrol etmelidir.
Kontrol adresi [Uygulama Kimliği Kontrolü](https://www.workerman.net/app/check)

## Oluşturma

`composer require webman/console` komutunu kullanarak webman komut satırını yükleyin

`php webman app-plugin:create {eklenti kimliği}` komutunu kullanarak yerelde bir uygulama eklentisi oluşturabilirsiniz

Örneğin `php webman app-plugin:create foo`

Webman'i yeniden başlatın

`http://127.0.0.1:8787/app/foo` adresine gidin, eğer bir içerik dönüyorsa, oluşturma işlemi başarılı demektir.
