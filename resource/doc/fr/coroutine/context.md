La classe `support\Context` est utilisée pour stocker les données du contexte de la requête, et lorsque la requête est terminée, les données de contexte correspondantes sont automatiquement supprimées. Autrement dit, la durée de vie des données de contexte est liée à la durée de vie de la requête. `support\Context` prend en charge l'environnement de coroutine Fiber, Swoole et Swow.

Pour en savoir plus, consultez [webman coroutine](./fiber.md).

## Interface
Le contexte fournit les interfaces suivantes :

### Définir les données du contexte
```php
Context::set(string $name, $mixed $value);
```

### Obtenir les données du contexte
```php
Context::get(string $name = null);
```

### Supprimer les données du contexte
```php
Context::delete(string $name);
```

> **Remarque**
> Le framework appelle automatiquement l'interface Context::destroy() pour détruire les données du contexte après la fin de la requête. Les applications ne doivent pas appeler manuellement Context::destroy().

## Exemple
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# Remarque
**Lors de l'utilisation de la coroutine**, il ne faut pas stocker les **données d'état liées à la requête** dans des variables globales ou des variables statiques, car cela peut entraîner une contamination des données globales. La méthode correcte consiste à les stocker et à les récupérer en utilisant Context.
