## Rota Yapılandırma Dosyası
Eklenti rotası, `plugin/eklentiAdı/config/route.php` yolunda bulunur.

## Varsayılan Rota
Uygulama eklenti URL adresleri her zaman `/app` ile başlar, örneğin `plugin\foo\app\controller\UserController` URL adresi `http://127.0.0.1:8787/app/foo/user` şeklindedir.

## Varsayılan Rotayı Devre Dışı Bırakma
Belirli bir uygulama eklentisinin varsayılan rotasını devre dışı bırakmak istiyorsanız, rotayı yapılandırma dosyasında aşağıdaki gibi ayarlayabilirsiniz:
```php
Route::disableDefaultRoute('foo');
```

## 404 Hatasını İşleme Geri Çağırma
Bir uygulama eklentisine geri dönüşüm ayarı yapmak istiyorsanız, ikinci parametre aracılığıyla eklenti adını ileterek aşağıdaki gibi bir geri dönüş yapabilirsiniz:
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
