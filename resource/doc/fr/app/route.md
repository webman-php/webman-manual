## Fichier de configuration des routes
Le fichier de configuration des routes du plugin se trouve dans `plugin/nom_du_plugin/config/route.php`.

## Route par défaut
Les chemins d'URL des plugins d'application commencent tous par `/app`, par exemple, le chemin d'URL `plugin\foo\app\controller\UserController` est `http://127.0.0.1:8787/app/foo/user`.

## Désactiver la route par défaut
Si vous souhaitez désactiver la route par défaut d'un plugin d'application, définissez-la dans la configuration des routes de la manière suivante :
```php
Route::disableDefaultRoute('foo');
```

## Gérer le rappel 404
Si vous souhaitez définir une option de secours pour un plugin d'application spécifique, vous devez passer le nom du plugin comme deuxième paramètre, par exemple :
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
