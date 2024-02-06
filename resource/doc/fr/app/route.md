## Fichier de configuration des routes
Le fichier de configuration des routes du plugin se trouve dans `plugin/nom_du_plugin/config/route.php`

## Route par défaut
Les adresses URL des applications de plugin commencent toutes par `/app`, par exemple, l'adresse URL du contrôleur `UserController` de `plugin\foo\app` sera `http://127.0.0.1:8787/app/foo/user`

## Désactiver la route par défaut
Si vous souhaitez désactiver la route par défaut d'une application de plugin, vous devez définir dans la configuration de route quelque chose de similaire à :
```php
Route::disableDefaultRoute('foo');
```

## Gérer le callback 404
Si vous souhaitez définir une redirection pour une application de plugin en cas de 404, vous devez passer le nom du plugin comme deuxième paramètre, par exemple :
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
