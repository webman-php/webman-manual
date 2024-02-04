Die Klasse `support\Context` dient zur Speicherung von Anfragedaten, die automatisch gelöscht werden, wenn die Anfrage abgeschlossen ist. Das bedeutet, die Lebensdauer der Kontextdaten entspricht der Lebensdauer der Anfrage. `support\Context` unterstützt die Fiber-, Swoole- und Swow-Kooperationsumgebung.

Weitere Informationen finden Sie unter [webman协程](./fiber.md).

# Schnittstelle
Der Kontext bietet folgende Schnittstellen

## Festlegung von Kontextdaten
```php
Context::set(string $name, $mixed $value);
```

## Abrufen von Kontextdaten
```php
Context::get(string $name = null);
```

## Löschen von Kontextdaten
```php
Context::delete(string $name);
```

> **Hinweis**
> Das Framework ruft automatisch die Schnittstelle Context::destroy() auf, um die Kontextdaten nach Abschluss der Anfrage zu zerstören. Der Geschäftsbetrieb darf Context::destroy() nicht manuell aufrufen.

# Beispiel
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

# Hinweis
**Bei Verwendung von Kooperation** sollten **anfragebezogene Statusdaten** nicht in globalen oder statischen Variablen gespeichert werden, da dies zu einer Verunreinigung globaler Daten führen kann. Die richtige Verwendung besteht darin, sie mit Context zu speichern und abzurufen.
