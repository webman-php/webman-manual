## Rota Yapılandırma Dosyası
Eklenti için rota yapılandırma dosyası `plugin/eklentiadı/config/route.php` içinde bulunur.

## Varsayılan Rota
Uygulama eklenti URL adres yolları her zaman `/app` ile başlar, örneğin `plugin\foo\app\controller\UserController` URL adresi `http://127.0.0.1:8787/app/foo/user` şeklindedir.

## Varsayılan Rota Devre Dışı Bırakma
Belirli bir uygulama eklentisinin varsayılan rotasını devre dışı bırakmak istiyorsanız, rota yapılandırmasında aşağıdaki gibi ayarlayabilirsiniz.
```php
Route::disableDefaultRoute('foo');
```

## 404 Hatası İçin Geri Çağrı Oluşturma
Belirli bir uygulama eklentisine fallback belirtmek istiyorsanız, ikinci parametre olarak eklenti adını ileterek aşağıdaki gibi bir geri çağrı oluşturabilirsiniz.
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
