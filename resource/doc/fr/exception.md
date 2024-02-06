# Gestion des exceptions

## Configuration
`config/exception.php`
```php
return [
    // Configurez ici la classe de gestion des exceptions
    '' => support\exception\Handler::class,
];
```
En mode multi-applications, vous pouvez configurer une classe de gestion des exceptions distincte pour chaque application, voir [Multi-Application](multiapp.md).


## Classe de gestion des exceptions par défaut
Par défaut, les exceptions dans webman sont gérées par la classe `support\exception\Handler`. Vous pouvez modifier la classe de gestion des exceptions par défaut en modifiant le fichier de configuration `config/exception.php`. La classe de gestion des exceptions doit implémenter l'interface `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * Enregistre un journal
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Rendu de la réponse
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e): Response;
}
```



## Rendu de la réponse
La méthode `render` de la classe de gestion des exceptions est utilisée pour le rendu de la réponse.

Si la valeur de `debug` dans le fichier de configuration `config/app.php` est définie sur `true` (ci-après abrégée `app.debug=true`), des informations détaillées sur l'exception seront renvoyées, sinon des informations succinctes sur l'exception seront renvoyées.

Si la requête attend une réponse JSON, les informations sur l'exception renvoyées sous forme de JSON seront similaires à :
```json
{
    "code": "500",
    "msg": "Informations sur l'exception"
}
```
Si `app.debug=true`, les données JSON incluront un champ `trace` supplémentaire renvoyant une pile d'appels détaillée.

Vous pouvez écrire votre propre classe de gestion des exceptions pour modifier la logique de gestion des exceptions par défaut.

# Exception métier BusinessException
Parfois, nous voulons interrompre une demande à l'intérieur d'une fonction imbriquée et renvoyer un message d'erreur au client. Cela peut être réalisé en lançant une `BusinessException`.
Par exemple :

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Paramètre incorrect', 3000);
        }
    }
}
```

L'exemple ci-dessus va renvoyer
```json
{"code": 3000, "msg": "Paramètre incorrect"}
```

> **Remarque**
> La BusinessException n'a pas besoin d'être capturée par un try-catch car le framework la capture automatiquement et renvoie la sortie appropriée en fonction du type de demande.

## Personnalisation de l'exception métier

Si la réponse ci-dessus ne correspond pas à vos besoins, par exemple si vous souhaitez changer `msg` en `message`, vous pouvez personnaliser une `MyBusinessException`.

Créez le fichier `app/exception/MyBusinessException.php` avec le contenu suivant :
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // Retourne des données JSON pour une requête JSON
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Sinon, renvoie une page non JSON
        return new Response(200, [], $this->getMessage());
    }
}
```

Ainsi, lorsque l'entreprise utilise
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Paramètre incorrect', 3000);
```
la demande JSON recevra une réponse semblable à :
```json
{"code": 3000, "message": "Paramètre incorrect"}
```

> **Conseil**
> Comme l'exception BusinessException est une exception métier (par exemple, une erreur de saisie de l'utilisateur), elle est prévisible. Par conséquent, le framework ne la considère pas comme une erreur fatale et n'enregistre pas de journal.

## En résumé
Vous pouvez envisager d'utiliser l'exception `BusinessException` à tout moment où vous voulez interrompre la demande actuelle et renvoyer des informations au client.
