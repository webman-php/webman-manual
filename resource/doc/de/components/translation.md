# Mehrsprachigkeit

Die Mehrsprachigkeit wird durch das [symfony/translation](https://github.com/symfony/translation) Modul realisiert.

## Installation
```
composer require symfony/translation
```

## Erstellung des Sprachpakets
Webman legt standardmäßig das Sprachpaket im Verzeichnis `resource/translations` ab (falls es nicht vorhanden ist, bitte manuell erstellen). Wenn Sie das Verzeichnis ändern möchten, konfigurieren Sie es in der Datei `config/translation.php`.
Jede Sprache entspricht einem Unterordner, und die Sprachdefinitionen sind standardmäßig in der Datei `messages.php` enthalten. Hier ist ein Beispiel:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Alle Sprachdateien enthalten ein Array, zum Beispiel:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hallo Webman',
];
```

## Konfiguration

`config/translation.php`

```php
return [
    // Standardsprache
    'locale' => 'zh_CN',
    // Fallback-Sprache, die verwendet wird, wenn in der aktuellen Sprache keine Übersetzung gefunden wird
    'fallback_locale' => ['zh_CN', 'en'],
    // Verzeichnis für Sprachdateien
    'path' => base_path() . '/resource/translations',
];
```

## Übersetzung

Die Übersetzung erfolgt mittels der `trans()` Methode.

Erstellen Sie die Sprachdatei `resource/translations/zh_CN/messages.php` wie folgt:
```php
return [
    'hello' => 'Hello Welt!',
];
```

Erstellen Sie die Datei `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // Hello Welt!
        return response($hello);
    }
}
```

Der Zugriff auf `http://127.0.0.1:8787/user/get` gibt "Hello Welt!" zurück.

## Ändern der Standardsprache

Die Sprache wird mit der `locale()` Methode geändert.

Erstellen Sie eine neue Sprachdatei `resource/translations/en/messages.php` wie folgt:
```php
return [
    'hello' => 'Hello Welt!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Sprache ändern
        locale('en');
        $hello = trans('hello'); // Hello Welt!
        return response($hello);
    }
}
```
Der Zugriff auf `http://127.0.0.1:8787/user/get` gibt "Hello Welt!" zurück.

Sie können auch die vierte Variable der `trans()` Funktion verwenden, um vorübergehend die Sprache zu ändern. Das folgende Beispiel ist äquivalent zum obigen:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Sprache über die vierte Variable ändern
        $hello = trans('hello', [], null, 'en'); // Hello Welt!
        return response($hello);
    }
}
```

## Festlegen der Sprache explizit für jede Anfrage
Die Übersetzung ist ein Singleton, was bedeutet, dass alle Anfragen dieselbe Instanz teilen. Wenn eine Anfrage die Standardsprache mit `locale()` festlegt, wirkt sich dies auf alle folgenden Anfragen des Prozesses aus. Daher sollte die Sprache für jede Anfrage explizit festgelegt werden. Verwenden Sie beispielsweise das folgende Middleware:

Erstellen Sie die Datei `app/middleware/Lang.php` (falls das Verzeichnis nicht vorhanden ist, bitte manuell erstellen) wie folgt:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

Fügen Sie in der Datei `config/middleware.php` globale Middleware wie unten gezeigt hinzu:
```php
return [
    // Globale Middleware
    '' => [
        // ... Hier werden andere Middleware-Angaben ausgelassen
        app\middleware\Lang::class,
    ]
];
```


## Verwendung von Platzhaltern
Manchmal enthält eine Nachricht die zu übersetzende Variable, z.B.
```php
trans('hello ' . $name);
```
In solchen Fällen verwenden wir Platzhalter.

Ändern Sie `resource/translations/zh_CN/messages.php` wie folgt:
```php
return [
    'hello' => 'Hallo %name%!',
```
Übergeben Sie die Daten beim Übersetzen über den zweiten Parameter den entsprechenden Wert des Platzhalters:
```php
trans('hello', ['%name%' => 'Webman']); // Hallo Webman!
```

## Plural behandeln
In manchen Sprachen bedingt die Anzahl von Objekten unterschiedliche Satzkonstruktionen, z.B. `Es gibt %count% Apfel`, bei ` %count%` = 1 ist die korrekte Satzkonstruktion, aber bei mehr als 1 ist sie falsch.

In solchen Fällen verwenden wir **Pipelines** (`|`) für die Angabe von Pluralformen.

Die Sprachdatei `resource/translations/en/messages.php` erhält eine neue Eintrag `apple_count` wie folgt:
```php
return [
    // ...

    'apple_count' => 'Es gibt einen Apfel|Es gibt %count% Äpfel',
];
```

```php
trans('apple_count', ['%count%' => 10]); // Es gibt 10 Äpfel
```

Sogar spezifische Zahlenbereiche können angegeben werden, um komplexere Regeln für die Pluralbildung zu erstellen:
```php
return [
    // ...

    'apple_count' => '{0} Es gibt keine Äpfel|{1} Es gibt einen Apfel|]1,19] Es gibt %count% Äpfel|[20,Inf[ Es gibt viele Äpfel',
];
```

```php
trans('apple_count', ['%count%' => 20]); // Es gibt viele Äpfel
```

## Festlegung der Sprachdatei
Der Standardname für die Sprachdatei ist `messages.php`, tatsächlich können Sie jedoch auch andere Dateinamen für Sprachdateien erstellen.

Erstellen Sie die Sprachdatei `resource/translations/zh_CN/admin.php` wie folgt:
```php
return [
    'hello_admin' => 'Hallo Admin!',
];
```

Spezifizieren Sie über das dritte Argument von `trans()` den Dateinamen der Sprachdatei (ohne die `.php` Erweiterung).
```php
trans('hello', [], 'admin', 'zh_CN'); // Hallo Admin!
```

## Weitere Informationen
Siehe [symfony/translation-Dokumentation](https://symfony.com/doc/current/translation.html)
