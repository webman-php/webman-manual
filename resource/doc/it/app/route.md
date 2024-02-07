## File di configurazione del percorso
Il file di configurazione del percorso per il plugin si trova in `plugin/nome_plugin/config/route.php`

## Percorso predefinito
Il percorso degli URL dell'applicazione del plugin inizia sempre con `/app`, ad esempio l'URL del controller `UserController` del plugin `foo` è `http://127.0.0.1:8787/app/foo/user`

## Disabilita percorso predefinito
Se si desidera disabilitare il percorso predefinito di un certo plugin dell'applicazione, è possibile farlo nel file di configurazione del percorso in questo modo
```php
Route::disableDefaultRoute('foo');
```

## Gestisci il callback 404
Se si desidera impostare un fallback per un certo plugin dell'applicazione, è necessario passare il nome del plugin come secondo parametro, ad esempio
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
