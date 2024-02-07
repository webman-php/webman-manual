# Kontext

Die Klasse `support\Context` wird verwendet, um die Daten des Anfragekontexts zu speichern. Wenn die Anfrage abgeschlossen ist, werden die entsprechenden Kontextdaten automatisch gelöscht. Das bedeutet, dass die Lebensdauer der Kontextdaten der Lebensdauer der Anfrage folgt. `support\Context` unterstützt die Fiber-, Swoole- und Swow-Koexistenzumgebung.

Weitere Informationen finden Sie unter [webman-Fiber](./fiber.md).

# Schnittstellen
Der Kontext bietet die folgenden Schnittstellen.

## Setzen von Kontextdaten
```php
Context::set(string $name, mixed $value);
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
> Das Framework ruft automatisch die Schnittstelle Context::destroy() auf, um die Kontextdaten nach Abschluss der Anfrage zu zerstören. Die Anwendung sollte nicht manuell Context::destroy() aufrufen.

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

# Achtung
Beim **Verwenden von Koexistenz** dürfen keine **anfragebezogenen Statusdaten** in globalen oder statischen Variablen gespeichert werden, da dies zu einer Verunreinigung globaler Daten führen kann. Die korrekte Verwendung ist, sie mit dem Kontext zu speichern und abzurufen.
