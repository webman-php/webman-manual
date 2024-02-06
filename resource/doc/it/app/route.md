## File di configurazione del percorso
Il file di configurazione del percorso del plugin si trova in `plugin/nome_plugin/config/route.php`

## Percorso predefinito
Il percorso dell'URL dell'applicazione del plugin inizia sempre con `/app`, ad esempio l'URL del controller `UserController` del plugin `plugin\foo\app` è `http://127.0.0.1:8787/app/foo/user`

## Disabilita percorso predefinito
Se si desidera disabilitare il percorso predefinito di un'applicazione del plugin, è possibile farlo configurando il percorso come segue:
```php
Route::disableDefaultRoute('foo');
```

## Gestisci callback 404
Se si desidera impostare un fallback per un'applicazione del plugin, è necessario passare il nome del plugin come secondo parametro, ad esempio:
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
