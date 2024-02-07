# Casbin-Zugriffskontrollbibliothek webman-permission

## Erklärung

Es basiert auf [PHP-Casbin](https://github.com/php-casbin/php-casbin), einem leistungsstarken, effizienten Open-Source-Zugriffskontroll-Framework, das Modelle für den Zugriff auf `ACL`, `RBAC`, `ABAC` und andere Zugriffssteuerungsmodelle unterstützt.

## Projektadresse

https://github.com/Tinywan/webman-permission

## Installation

```php
composer require tinywan/webman-permission
```
> Dieses erforderte Erweitung PHP 7.1+ und [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998), Offizielle Handbuch: https://www.workerman.net/doc/webman#/db/others

## Konfiguration

### Dienst registrieren
Erstellen Sie eine Konfigurationsdatei `config/bootstrap.php` mit ähnlichem Inhalt:

```php
// ...
webman\permission\Permission::class,
```

### Modellkonfigurationsdatei

Erstellen Sie eine Konfigurationsdatei `config/casbin-basic-model.conf` mit ähnlichem Inhalt:

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

### Richtlinienkonfigurationsdatei

Erstellen Sie eine Konfigurationsdatei `config/permission.php` mit ähnlichem Inhalt:

```php
<?php

return [
    /*
     *Standardberechtigung
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Modellkonfiguration
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adapter.
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Datenbankeinstellungen
            */
            'database' => [
                // Datenbankverbindungsname, wenn leer, wird die Standardeinstellung verwendet.
                'connection' => '',
                // Richtlinientabellenname (ohne Tabellenpräfix)
                'rules_name' => 'rule',
                // Vollständiger Name der Richtlinientabelle.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Schnellstart

```php
use webman\permission\Permission;

// Berechtigungen für einen Benutzer hinzufügen
Permission::addPermissionForUser('eve', 'articles', 'read');
// Eine Rolle für einen Benutzer hinzufügen
Permission::addRoleForUser('eve', 'writer');
// Berechtigungen für eine Regel hinzufügen
Permission::addPolicy('writer', 'articles', 'edit');
```

Sie können überprüfen, ob ein Benutzer solche Berechtigungen hat.

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // Erlauben Sie eve, Artikel zu bearbeiten
} else {
    // Die Anfrage verweigern, einen Fehler anzeigen
}
````

## Berechtigungsmittleres

Erstellen Sie die Datei `app/middleware/AuthorizationMiddleware.php` (falls das Verzeichnis nicht existiert, erstellen Sie es bitte selbst) wie folgt:

```php
<?php

/**
 * Autorisierungsmittel
 * von ShaoBo Wan (Tinywan)
 * Datum und Uhrzeit 2021/09/07 14:15
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
                throw new \Exception('Entschuldigung, Sie haben keine Berechtigung für den Zugriff auf diese Schnittstelle');
            }
        } catch (CasbinException $exception) {
            throw new \Exception('Autorisierungsausnahme' . $exception->getMessage());
        }
        return $next($request);
    }
}
```

Fügen Sie in `config/middleware.php` globale Mittelware wie folgt hinzu:

```php
return [
    // Globale mittlere Waren
    '' => [
        // ... Andere Mittel waren hier ausgelassen
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Dank

[Casbin](https://github.com/php-casbin/php-casbin), Sie können die vollständige Dokumentation auf ihrer [offiziellen Website](https://casbin.org/) einsehen.

## Lizenz

Dieses Projekt steht unter der [Apache 2.0 Lizenz](LICENSE).
