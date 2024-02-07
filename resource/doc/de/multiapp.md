# Mehrere Anwendungen
Manchmal kann ein Projekt in mehrere Unterkategorien unterteilt werden, beispielsweise kann ein Online-Shop in Hauptshop, API-Schnittstelle und Administrationsbereich unterteilt werden. Sie alle verwenden dieselbe Datenbankkonfiguration.

Webman ermöglicht die Planung des App-Verzeichnisses wie folgt:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Wenn Sie die Adresse `http://127.0.0.1:8787/shop/{Controller}/{Methode}` aufrufen, greifen Sie auf den Controller und die Methode unter `app/shop/controller` zu.

Wenn Sie die Adresse `http://127.0.0.1:8787/api/{Controller}/{Methode}` aufrufen, greifen Sie auf den Controller und die Methode unter `app/api/controller` zu.

Wenn Sie die Adresse `http://127.0.0.1:8787/admin/{Controller}/{Methode}` aufrufen, greifen Sie auf den Controller und die Methode unter `app/admin/controller` zu.

In Webman können Sie das App-Verzeichnis sogar folgendermaßen planen.
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Wenn Sie die Adresse `http://127.0.0.1:8787/{Controller}/{Methode}` aufrufen, greifen Sie auf den Controller und die Methode unter `app/controller` zu. Wenn der Pfad mit "api" oder "admin" beginnt, greifen Sie auf die entsprechenden Verzeichnisse zu, um auf den Controller und die Methode zuzugreifen.

Die Namensräume für Klassen müssen in multilateralen Anwendungen dem `PSR-4`-Standard entsprechen. Zum Beispiel könnten Dateien wie `app/api/controller/FooController.php` folgendermaßen aussehen:

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Konfiguration von Middleware in mehreren Anwendungen
Manchmal möchten Sie für verschiedene Anwendungen unterschiedliche Middleware konfigurieren. Zum Beispiel benötigt die `API`-Anwendung möglicherweise eine CORS-Middleware, während `Admin` eine Middleware zur Überprüfung des Administrator-Logins benötigt. Die Konfiguration der Datei `config/midlleware.php` könnte wie folgt aussehen:

```php
return [
    // Globale Middleware
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware für die API-Anwendung
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware für die Admin-Anwendung
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Die genannten Middleware-Klassen müssen möglicherweise nicht vorhanden sein. Dies dient lediglich als Beispiel, wie Middleware nach Anwendung konfiguriert wird.

Die Reihenfolge der Middleware-Ausführung ist `Globale Middleware`->`Anwendungsmiddleware`.

Weitere Informationen zur Middleware-Entwicklung finden Sie im [Middleware-Abschnitt](middleware.md).

## Konfiguration der Behandlung von Ausnahmen in mehreren Anwendungen
Ebenso möchten Sie möglicherweise für verschiedene Anwendungen verschiedene Exception-Handling-Klassen konfigurieren. Zum Beispiel möchten Sie möglicherweise eine benutzerfreundliche Fehlerseite für die "Shop"-Anwendung anzeigen, wenn ein Fehler auftritt. Für die "API"-Anwendung möchten Sie jedoch keinen Seitenfehler, sondern eine JSON-Zeichenfolge zurückgeben. Die Konfigurationsdatei `config/exception.php` für verschiedene Anwendungen könnte wie folgt aussehen:

```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Im Gegensatz zur Middleware kann für jede Anwendung nur eine Exception-Behandlungsklasse konfiguriert werden.

> Die genannten Exception-Behandlungsklassen müssen möglicherweise nicht vorhanden sein. Dies dient lediglich als Beispiel, wie die Ausnahmen nach Anwendung konfiguriert werden.

Weitere Informationen zur Entwicklung der Exception-Behandlung finden Sie im [Abschnitt zur Exception-Behandlung](exception.md).
