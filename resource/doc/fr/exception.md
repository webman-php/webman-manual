# Gestion des exceptions

## Configuration
`config/exception.php`
```php
return [
    // Configurez ici la classe de gestion des exceptions
    '' => support\exception\Handler::class,
];
```
En mode d'application multiple, vous pouvez configurer une classe de gestion des exceptions distincte pour chaque application, voir [Application multiple](multiapp.md)

## Classe de gestion des exceptions par défaut
Les exceptions dans webman sont gérées par défaut par la classe `support\exception\Handler`. Vous pouvez modifier la classe de gestion des exceptions par défaut en modifiant le fichier de configuration `config/exception.php`. La classe de gestion des exceptions doit implémenter l'interface `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * Enregistrer les logs
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Rendre la réponse
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## Rendre une réponse
La méthode `render` dans la classe de gestion des exceptions est utilisée pour rendre une réponse.

Si la valeur de `debug` dans le fichier de configuration `config/app.php` est `true` (abrégé en `app.debug=true`), des informations détaillées sur l'exception seront renvoyées, sinon des informations succinctes sur l'exception seront renvoyées.

Si la demande attend une réponse en JSON, les informations sur l'exception seront renvoyées au format JSON, par exemple
```json
{
    "code": "500",
    "msg": "Informations sur l'exception"
}
```
Si `app.debug=true`, les données JSON incluront également un champ `trace` renvoyant une pile d'appels détaillée.

Vous pouvez écrire votre propre classe de gestion des exceptions pour modifier la logique de gestion des exceptions par défaut.

# Exception métier BusinessException
Parfois, nous voulons interrompre une requête à l'intérieur d'une fonction imbriquée et renvoyer un message d'erreur au client. Dans ce cas, nous pouvons le faire en lançant une `BusinessException`.
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
        $this->checkInput($request->post());
        return response('hello index');
    }
    
    protected function checkInput($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Erreur de paramètre', 3000);
        }
    }
}
```

L'exemple ci-dessus renverra un
```json
{"code": 3000, "msg": "Erreur de paramètre"}
```

> **Remarque**
> Une BusinessException n'a pas besoin d'être capturée par un bloc try-catch, le framework la capture automatiquement et renvoie une sortie appropriée en fonction du type de requête.

## Exception métier personnalisée

Si la réponse ci-dessus ne correspond pas à vos besoins, par exemple si vous souhaitez changer `msg` en `message`, vous pouvez créer une `MyBusinessException` personnalisée.

Créez un nouveau fichier `app/exception/MyBusinessException.php` avec le contenu suivant :
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
        // Renvoi des données JSON pour les requêtes JSON
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Renvoi d'une page pour les requêtes non-JSON
        return new Response(200, [], $this->getMessage());
    }
}
```

De cette manière, lorsque vous appelez
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Erreur de paramètre', 3000);
```
une requête JSON recevra une réponse au format JSON similaire à la suivante
```json
{"code": 3000, "message": "Erreur de paramètre"}
```

> **Remarque**
> Comme l'exception BusinessException est une exception métier (par exemple, une erreur de saisie utilisateur) et est prévisible, le framework ne la considère pas comme une erreur fatale et ne la journalise pas.

## Conclusion

Lorsque vous souhaitez interrompre une requête en cours et renvoyer des informations au client, vous pouvez envisager d'utiliser l'exception `BusinessException`.
