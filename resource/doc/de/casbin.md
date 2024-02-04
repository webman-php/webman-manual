# Casbin 访问控制库 webman-permission

## Beschreibung

Es basiert auf [PHP-Casbin](https://github.com/php-casbin/php-casbin), einem leistungsstarken Open-Source-Zugriffskontrollframework, das Zugriffssteuerungsmodelle wie `ACL`, `RBAC`, `ABAC` unterstützt.

## Projektadresse

https://github.com/Tinywan/webman-permission

## Installation

```php
composer require tinywan/webman-permission
```
> Diese Erweiterung erfordert PHP 7.1+ und [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998). Offizielles Handbuch: https://www.workerman.net/doc/webman#/db/others

## Konfiguration

### Service registrieren
Erstellen Sie die Konfigurationsdatei `config/bootstrap.php` mit dem folgenden Inhalt:

```php
    // ...
    webman\permission\Permission::class,
```

### Model Konfigurationsdatei
Erstellen Sie die Konfigurationsdatei `config/casbin-basic-model.conf` mit dem folgenden Inhalt:

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

### Policy Konfigurationsdatei
Erstellen Sie die Konfigurationsdatei `config/permission.php` mit dem folgenden Inhalt:

```php
<?php

return [
    /*
     * Standardberechtigung
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Model Einstellungen
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adapter.
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Datenbankeinstellungen.
            */
            'database' => [
                // Datenbankverbindungsnamen, wenn nicht vorhanden, wird die Standardkonfiguration verwendet.
                'connection' => '',
                // Richtlinientabellenname (ohne Tabellenpräfix)
                'rules_name' => 'rule',
                // Vollständiger Tabellenname der Richtlinie.
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
// Eine Rolle für einen Benutzer hinzufügen.
Permission::addRoleForUser('eve', 'writer');
// Berechtigungen für eine Regel hinzufügen
Permission::addPolicy('writer', 'articles','edit');
```

Sie können überprüfen, ob ein Benutzer solche Berechtigungen hat.

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // Erlaube eve das Bearbeiten von Artikeln
} else {
    // Die Anfrage verweigern, einen Fehler anzeigen
}
````

## Autorisierungsmiddleware

Erstellen Sie die Datei `app/middleware/AuthorizationMiddleware.php` (falls das Verzeichnis nicht vorhanden ist, erstellen Sie es bitte selbst) wie unten gezeigt:

```php
<?php

/**
 * Autorisierungsmiddleware
 * von ShaoBo Wan (Tinywan)
 * 07.09.2021 14:15 Uhr
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
				throw new \Exception('Entschuldigung, Sie haben keine Zugriffsberechtigung für diese Schnittstelle');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('Autorisierungsausnahme' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

Fügen Sie in `config/middleware.php` globale Middleware wie folgt hinzu:

```php
return [
    // Globale Middleware
    '' => [
        // ... Hier fehlen andere Mittelwares
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Dank

[Casbin](https://github.com/php-casbin/php-casbin). Sie können die komplette Dokumentation auf ihrer [Website](https://casbin.org/) einsehen.

## Lizenz

Dieses Projekt ist unter der [Apache-2.0-Lizenz](LICENSE) lizenziert.
