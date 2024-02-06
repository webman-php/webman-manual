# Uygulama Eklentisi Oluşturma

## Tekil Kimlik

Her eklentinin benzersiz bir uygulama kimliği vardır; geliştirici bir eklenti oluşturmadan önce bir kimlik seçmeli ve seçilen kimliğin kullanılmadığını kontrol etmelidir.
Kontrol adresi: [Uygulama Kimliği Kontrolü](https://www.workerman.net/app/check)

## Oluşturma

`composer require webman/console` komutunu çalıştırarak webman komut satırını yükleyin.

`php webman app-plugin:create {eklenti kimliği}` komutunu kullanarak yerel ortamda bir uygulama eklentisi oluşturabilirsiniz.

Örneğin `php webman app-plugin:create foo`

Webman'ı yeniden başlatın.

`http://127.0.0.1:8787/app/foo` adresine giderek başarılı bir şekilde oluşturulduysa içerik döndürülecektir.
