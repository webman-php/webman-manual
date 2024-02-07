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

Lorsque vous accédez à `http://127.0.0.1:8787/foo`, la page affiche `hello index`.

Lorsque vous accédez à `http://127.0.0.1:8787/foo/hello`, la page affiche `hello webman`.

Bien sûr, vous pouvez modifier les règles de routage via la configuration des routes, voir [Routes](route.md).

> **Remarque**
> Si une erreur 404 se produit et que l'accès est impossible, veuillez ouvrir `config/app.php`, définir `controller_suffix` sur `Controller`, puis redémarrer.

## Suffixe du contrôleur
À partir de la version 1.3, webman permet de définir un suffixe de contrôleur dans `config/app.php`. Si `controller_suffix`dans `config/app.php` est défini comme une chaîne vide `''`, le contrôleur ressemblera à ce qui suit :

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

Il est fortement recommandé de définir le suffixe du contrôleur comme `Controller`, afin d'éviter les conflits de noms entre les contrôleurs et les modèles, tout en renforçant la sécurité.

## Remarques
 - Le framework transmet automatiquement un objet `support\Request` au contrôleur, à l'aide duquel vous pouvez récupérer les données d'entrée de l'utilisateur (données get, post, en-tête, cookie, etc.), voir [Requête](request.md)
 - Le contrôleur peut renvoyer un nombre, une chaîne ou un objet `support\Response`, mais pas d'autres types de données.
 - L'objet `support\Response` peut être créé à l'aide des fonctions d'assistance `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, etc.

## Cycle de vie du contrôleur
Lorsque `controller_reuse` dans `config/app.php` est défini sur `false`, une nouvelle instance du contrôleur correspondant est initialisée à chaque requête, et cette instance est détruite à la fin de la requête, suivant le même mécanisme de fonctionnement que les frameworks traditionnels.

Lorsque `controller_reuse` dans `config/app.php` est défini sur `true`, toutes les requêtes réutiliseront la même instance du contrôleur, c'est-à-dire que l'instance du contrôleur restera en mémoire une fois créée, et sera réutilisée pour toutes les requêtes.

> **Remarque**
> La désactivation de la réutilisation du contrôleur nécessite webman>=1.4.0, autrement dit, avant la version 1.4.0, tous les contrôleurs étaient par défaut réutilisés pour toutes les requêtes, et cette configuration ne pouvait pas être modifiée.

> **Remarque**
> Lorsque la réutilisation du contrôleur est activée, les requêtes ne doivent pas modifier les propriétés du contrôleur, car ces modifications auront un impact sur les requêtes ultérieures, par exemple

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
        // Si une autre requête delete?id=2 est effectuée, les données pour l'id 1 seront supprimées
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Remarque**
> Une instruction `return` dans la méthode `__construct()` du contrôleur n'aura aucun effet, par exemple

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Un retour de données dans le constructeur n'aura aucun effet, le navigateur ne recevra pas cette réponse
        return response('hello'); 
    }
}
```

## Différence entre la non-réutilisation et la réutilisation du contrôleur
Les différences sont les suivantes

#### Non-réutilisation du contrôleur
Une nouvelle instance du contrôleur est créée pour chaque requête, et cette instance est détruite à la fin de la requête, libérant ainsi la mémoire. Le non-réutilisation du contrôleur est similaire au fonctionnement des frameworks traditionnels et convient à la plupart des développeurs. En raison des créations et destructions répétées du contrôleur, les performances sont légèrement inférieures à celles de la réutilisation du contrôleur (environ 10 % de moins en performance selon les tests de stress helloworld ; cette baisse de performance est négligeable dans des cas d'utilisation réels).

#### Réutilisation du contrôleur
Si la réutilisation est activée, une seule instance du contrôleur est créée par processus, et cette instance n'est pas libérée à la fin de la requête, mais réutilisée pour les requêtes ultérieures dans le même processus. La réutilisation du contrôleur offre de meilleures performances, mais ne convient pas à la plupart des développeurs.

#### Cas où la réutilisation du contrôleur ne peut pas être utilisée
Lorsque les requêtes modifient les propriétés du contrôleur, la réutilisation du contrôleur ne peut pas être activée, car ces modifications affecteront les requêtes ultérieures.

Certains développeurs aiment initialiser certaines valeurs pour chaque requête dans la méthode `__construct()` du contrôleur, dans ce cas, la réutilisation du contrôleur ne peut pas être activée, car le constructeur n'est appelé qu'une seule fois par processus, et non à chaque requête.
