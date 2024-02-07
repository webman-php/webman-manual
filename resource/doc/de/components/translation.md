# Mehrsprachigkeit

Mehrsprachigkeit wird durch das [symfony/translation](https://github.com/symfony/translation) Komponente realisiert.

## Installation
```shell
composer require symfony/translation
```

## Erstellung von Sprachdateien
Standardmäßig werden die Sprachdateien von webman im Verzeichnis `resource/translations` abgelegt (falls nicht vorhanden, bitte manuell erstellen). Sollte das Verzeichnis geändert werden, kann dies in der Datei `config/translation.php` eingestellt werden.
Jede Sprache entspricht einem Unterordner, und die Sprachdefinitionen werden standardmäßig in der Datei `messages.php` abgelegt. Hier ist ein Beispiel:

```plaintext
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Alle Sprachdateien geben ein Array zurück, zum Beispiel:

```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Konfiguration

`config/translation.php`

```php
return [
    // Standardsprache
    'locale' => 'zh_CN',
    // Fallback-Sprache: Wenn eine Übersetzung in der aktuellen Sprache nicht gefunden wird, wird versucht, die Übersetzung in der Fallback-Sprache zu finden.
    'fallback_locale' => ['zh_CN', 'en'],
    // Verzeichnis für Sprachdateien
    'path' => base_path() . '/resource/translations',
];
```

## Übersetzung

Die Übersetzung erfolgt mithilfe der `trans()`-Methode.

Erstellen Sie die Sprachdatei `resource/translations/zh_CN/messages.php` wie folgt:

```php
return [
    'hello' => '你好 世界!',
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
        $hello = trans('hello'); // 你好 世界!
        return response($hello);
    }
}
```

Ein Aufruf von `http://127.0.0.1:8787/user/get` gibt "你好 世界!" zurück.

## Ändern der Standardsprache

Die Sprache kann mit der Methode `locale()` geändert werden.

Erstellen Sie eine neue Sprachdatei `resource/translations/en/messages.php`, wie folgt:

```php
return [
    'hello' => 'hello world!',
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
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
Ein Aufruf von `http://127.0.0.1:8787/user/get` gibt "hello world!" zurück.

Sie können auch das vierte Argument der `trans()`-Funktion verwenden, um die Sprache temporär zu ändern. Das folgende Beispiel ist äquivalent zu dem obigen:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Sprache durch das vierte Argument ändern
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Festlegen der Sprache für jede Anfrage explizit

Translation ist ein Singleton, was bedeutet, dass alle Anfragen diese Instanz gemeinsam nutzen. Wenn eine Anfrage die Standardsprache mit `locale()` festlegt, wirkt sich dies auf alle folgenden Anfragen in diesem Prozess aus. Daher sollten wir die Sprache für jede Anfrage explizit festlegen. Zum Beispiel mit dem folgenden Middleware:

Erstellen Sie die Datei `app/middleware/Lang.php` (falls das Verzeichnis nicht existiert, bitte manuell erstellen) wie folgt:
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

Fügen Sie in der Datei `config/middleware.php` globale Middleware wie folgt hinzu:
```php
return [
    // Globale Middleware
    '' => [
        // ... andere Middleware hier
        app\middleware\Lang::class,
    ]
];
```

## Verwendung von Platzhaltern
Manchmal enthält eine Nachricht Variablen, die übersetzt werden müssen, zum Beispiel
```php
trans('hello ' . $name);
```
In solchen Fällen verwenden wir Platzhalter.

Ändern Sie `resource/translations/zh_CN/messages.php` wie folgt:
```php
return [
    'hello' => '你好 %name%!',
];
```
Übergeben Sie die Daten beim Aufruf der Übersetzungsfunktion als Wert des Platzhalters durch das zweite Argument weiter:
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## Behandeln von Pluralformen
In manchen Sprachen muss aufgrund der Anzahl unterschiedlicher Satzstrukturen unterschieden werden, z.B. `There is %count% apple`. Diese Satzstruktur ist richtig, wenn `%count%` gleich 1 ist, aber falsch, wenn sie größer als 1 ist.

In solchen Fällen verwenden wir **Pipes** (`|`), um die verschiedenen Pluralformen anzugeben.

Fügen Sie in der Sprachdatei `resource/translations/en/messages.php` die Schlüssel-Wert-Paare für `apple_count` hinzu, wie folgt:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Sie können sogar Zahlenbereiche angeben, um komplexere Regeln für Pluralformen zu erstellen:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Spezifische Sprachdateien verwenden
Der Standardname für eine Sprachdatei ist `messages.php`, aber in Wirklichkeit können Sie Sprachdateien mit anderen Namen erstellen.

Erstellen Sie eine Sprachdatei `resource/translations/zh_CN/admin.php` wie folgt:
```php
return [
    'hello_admin' => '你好 管理员!',
];
```

Verwenden Sie das dritte Argument von `trans()` um die Sprachdatei (ohne die `.php`-Erweiterung) anzugeben.
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## Weitere Informationen
Siehe [Symfony/Translation-Dokumentation](https://symfony.com/doc/current/translation.html)
