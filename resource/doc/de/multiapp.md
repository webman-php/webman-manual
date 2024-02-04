# Mehrere Anwendungen
Manchmal kann ein Projekt in mehrere Unterprojekte aufgeteilt sein. Zum Beispiel kann ein Geschäft in Hauptgeschäft, Geschäfts-API-Schnittstelle und Verwaltungshintergrund für das Geschäft unterteilt werden, und alle verwenden dieselbe Datenbankkonfiguration.

Webman ermöglicht die Planung des App-Verzeichnisses auf diese Weise:
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
Wenn Sie die Adresse `http://127.0.0.1:8787/shop/{controller}/{method}` besuchen, greifen Sie auf den Controller und die Methode unter `app/shop/controller` zu.

Wenn Sie die Adresse `http://127.0.0.1:8787/api/{controller}/{method}` besuchen, greifen Sie auf den Controller und die Methode unter `app/api/controller` zu.

Wenn Sie die Adresse `http://127.0.0.1:8787/admin/{controller}/{method}` besuchen, greifen Sie auf den Controller und die Methode unter `app/admin/controller` zu.

In Webman können sogar das App-Verzeichnis wie folgt geplant werden:
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
Auf diese Weise greifen Sie auf den Controller und die Methode unter `app/controller` zu, wenn die Adresse `http://127.0.0.1:8787/{controller}/{method}` lautet. Wenn der Pfad mit api oder admin beginnt, greifen Sie auf den entsprechenden Unterordner zu und auf die entsprechenden Controller und Methoden.

Die Namensräume für Klassen in mehreren Anwendungen müssen `psr4` entsprechen, zum Beispiel die Datei `app/api/controller/FooController.php` sieht wie folgt aus:

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Konfiguration der Middleware für mehrere Anwendungen
Manchmal möchten Sie verschiedene Middleware für verschiedene Anwendungen konfigurieren. Zum Beispiel benötigt die `api`-Anwendung möglicherweise eine CORS-Middleware, und `admin` benötigt eine Middleware zur Überprüfung der Administratoranmeldung. Die Konfiguration von `config/middleware.php` könnte wie folgt aussehen:

```php
return [
    // Globale Middleware
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware für die api-Anwendung
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware für die admin-Anwendung
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Die oben genannten Middleware existieren möglicherweise nicht und dienen nur als Beispiel für die Konfiguration von Middlewares nach Anwendungen.

Die Reihenfolge der Middleware-Ausführung ist `Globale Middleware` -> `Anwendungs-Middleware`.

Weitere Informationen zur Middleware-Entwicklung finden Sie im [Middleware-Abschnitt](middleware.md).

## Konfiguration der Ausnahmehandlung für mehrere Anwendungen
Ebenso möchten Sie möglicherweise verschiedene Ausnahmehandlungsklassen für verschiedene Anwendungen konfigurieren. Zum Beispiel möchten Sie möglicherweise eine freundliche Hinweisseite anzeigen, wenn in der `shop`-Anwendung eine Ausnahme auftritt; für die `api`-Anwendung möchten Sie jedoch keinen Seitentext zurückgeben, sondern eine JSON-Zeichenkette. Die Konfigurationsdatei für die Konfiguration verschiedener Ausnahmehandlungsklassen pro Anwendung könnte wie folgt aussehen: `config/exception.php`:

```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Anders als bei Middleware kann jede Anwendung nur eine Ausnahmehandlerklasse konfigurieren.

> Die oben genannten Ausnahmehandlerklassen existieren möglicherweise nicht und dienen nur als Beispiel für die Konfiguration der Ausnahmehandlung nach Anwendungen.

Weitere Informationen zur Entwicklung von Ausnahmehandlungen finden Sie im [Abschnitt zur Fehlerbehandlung](exception.md).
