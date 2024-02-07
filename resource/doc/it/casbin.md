# Casbin libreria di controllo degli accessi webman-permission

## Descrizione

Si basa su [PHP-Casbin](https://github.com/php-casbin/php-casbin), un framework di controllo degli accessi open source potente ed efficiente, che supporta modelli di controllo degli accessi basati su `ACL`, `RBAC`, `ABAC` e altri.

## Indirizzo del progetto

https://github.com/Tinywan/webman-permission

## Installazione

```php
composer require tinywan/webman-permission
```

> Questa estensione richiede PHP 7.1+ e [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998), documentazione ufficiale: https://www.workerman.net/doc/webman#/db/others

## Configurazione

### Registrazione del servizio
Creare il file di configurazione `config/bootstrap.php` con un contenuto simile a questo:

```php
    // ...
    webman\permission\Permission::class,
```

### File di configurazione del Model

Creare il file di configurazione `config/casbin-basic-model.conf` con un contenuto simile a questo:

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```

### File di configurazione delle Policy

Creare il file di configurazione `config/permission.php` con un contenuto simile a questo:

```php
<?php

return [
    /*
     * Permesso predefinito
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Impostazioni Model
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adattatore .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Impostazioni del database.
            */
            'database' => [
                // Nome della connessione al database, se non specificato, verrà utilizzata la configurazione predefinita.
                'connection' => '',
                // Nome della tabella delle regole (senza prefisso tabella)
                'rules_name' => 'rule',
                // Nome completo della tabella delle regole.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Inizio rapido

```php
use webman\permission\Permission;

// aggiunge i permessi a un utente
Permission::addPermissionForUser('eve', 'articoli', 'lettura');
// aggiunge un ruolo per un utente.
Permission::addRoleForUser('eve', 'scrittore');
// aggiunge permessi a una regola
Permission::addPolicy('scrittore', 'articoli', 'modifica');
```

È possibile verificare se un utente ha tali permessi

```php
if (Permission::enforce("eve", "articoli", "modifica")) {
    // permette a eve di modificare gli articoli
} else {
    // nega la richiesta, mostra un errore
}
````

## Middleware di autorizzazione

Creare il file `app/middleware/AuthorizationMiddleware.php` (se la directory non esiste, creare manualmente) come segue:

```php
<?php

/**
 * Middleware di autorizzazione
 * Autore ShaoBo Wan (Tinywan)
 * Data e ora 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $uri = $request->path();
        try {
            $userId = 10086;
            $action = $request->method();
            if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
                throw new \Exception('Spiacenti, non hai autorizzazione per accedere a questa interfaccia');
            }
        } catch (CasbinException $exception) {
            throw new \Exception('Eccezione di autorizzazione' . $exception->getMessage());
        }
        return $next($request);
    }
}
```

Aggiungere il middleware globale in `config/middleware.php` come segue:

```php
return [
    // Middleware globali
    '' => [
        // ... Altri middleware omit
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Ringraziamenti

[Casbin](https://github.com/php-casbin/php-casbin), puoi consultare tutta la documentazione sul suo [sito ufficiale](https://casbin.org/).

## Licenza

Questo progetto è distribuito con licenza [Apache 2.0](LICENSE).
