# Contrôleur

Créez un nouveau fichier de contrôleur `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Lorsque vous accédez à `http://127.0.0.1:8787/foo`, la page renvoie `hello index`.

Lorsque vous accédez à `http://127.0.0.1:8787/foo/hello`, la page renvoie `hello webman`.

Bien sûr, vous pouvez modifier les règles de routage via la configuration des routes, voir [Routes](route.md).

> **Conseil**
> Si une erreur 404 se produit, veuillez ouvrir `config/app.php` et définir `controller_suffix` sur `Controller`, puis redémarrer.

## Suffixe du contrôleur
Depuis la version 1.3 de webman, il est possible de définir un suffixe pour les contrôleurs dans `config/app.php`. Si `controller_suffix` est réglé à une chaîne vide `''`, le contrôleur ressemblera à ceci

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Il est fortement recommandé de définir le suffixe du contrôleur à `Controller`. Cela permet d'éviter les conflits de noms entre les contrôleurs et les modèles de données, tout en renforçant la sécurité.

## Remarque
 - Le framework transmet automatiquement un objet `support\Request` au contrôleur. Grâce à celui-ci, vous pouvez récupérer les données d'entrée de l'utilisateur (données GET, POST, en-tête, cookie, etc.), voir [Requête](request.md).
 - Le contrôleur peut renvoyer un entier, une chaîne de caractères ou un objet `support\Response`, mais pas d'autres types de données.
 - L'objet `support\Response` peut être créé à l'aide des fonctions d'aide `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, etc.

## Cycle de vie du contrôleur
Lorsque `controller_reuse` est défini sur `false` dans `config/app.php`, une nouvelle instance du contrôleur correspondant est initialisée à chaque requête, puis détruite à la fin de la requête. Cela fonctionne de la même manière que dans les frameworks traditionnels.

Lorsque `controller_reuse` est défini sur `true` dans `config/app.php`, toutes les requêtes utiliseront la même instance du contrôleur. Autrement dit, une fois l'instance du contrôleur créée, elle restera en mémoire et sera réutilisée pour toutes les requêtes.

> **Remarque**
> La désactivation de la réutilisation du contrôleur nécessite webman>=1.4.0. Avant la version 1.4.0, la réutilisation du contrôleur était activée par défaut et ne pouvait pas être modifiée.

> **Remarque**
> Lorsque la réutilisation du contrôleur est activée, les requêtes ne doivent pas modifier les propriétés du contrôleur, car ces modifications auront un impact sur les requêtes ultérieures. Par exemple

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // Cette méthode conservera le modèle après la première requête update?id=1
        // Si vous envoyez une autre requête delete?id=2, les données de l'id 1 seront supprimées.
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Conseil**
> Le fait de retourner des données dans le constructeur `__construct()` du contrôleur n'aura aucun effet, par exemple

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Le fait de retourner des données dans le constructeur n'aura aucun effet, et le navigateur ne recevra pas cette réponse
        return response('hello'); 
    }
}
```

## Différences entre la réutilisation et non-réutilisation du contrôleur
Les différences sont les suivantes

#### Contrôleur non réutilisable
À chaque requête, une nouvelle instance du contrôleur est créée et détruite à la fin de la requête. Bien que cela soit semblable à un framework traditionnel, les performances sont légèrement inférieures à celles de la réutilisation du contrôleur (environ 10% de moins en termes de performances helloworld, mais cette différence est négligeable avec des opérations métier plus lourdes).

#### Contrôleur réutilisable
Lorsque cette option est activée, une seule instance du contrôleur est créée pour un processus. À la fin de la requête, cette instance du contrôleur n'est pas détruite et est réutilisée pour les requêtes ultérieures dans le même processus. Bien que la réutilisation du contrôleur améliore les performances, elle n'est pas adaptée à la plupart des développeurs.

#### Cas où la réutilisation du contrôleur n'est pas possible
Lorsque la requête modifie les propriétés du contrôleur, la réutilisation du contrôleur est impossible, car ces modifications affecteront les requêtes ultérieures.

Certains développeurs aiment effectuer des initialisations pour chaque requête dans le constructeur du contrôleur `__construct()`, dans ce cas, la réutilisation du contrôleur n'est pas possible, car le constructeur du processus courant est appelé une seule fois, et non à chaque requête.

