# Lebenszyklus

## Prozesslebenszyklus
- Jeder Prozess hat eine lange Lebensdauer.
- Jeder Prozess arbeitet unabhängig voneinander und stört sich nicht gegenseitig.
- Jeder Prozess kann während seiner Lebensdauer mehrere Anfragen verarbeiten.
- Wenn ein Prozess die Befehle `stop`, `reload` oder `restart` empfängt, führt er einen Ausstieg aus und beendet seine aktuelle Lebensdauer.

> **Hinweis**
> Jeder Prozess arbeitet unabhängig voneinander, was bedeutet, dass jeder Prozess seine eigenen Ressourcen, Variablen und Klasseninstanzen wie z.B. eigene Datenbankverbindungen verwaltet. Einige Singleton-Instanzen werden in jedem Prozess einmal initialisiert, was bedeutet, dass sie mehrmals initialisiert werden, wenn mehrere Prozesse vorhanden sind.

## Anforderungslebenszyklus
- Jede Anfrage generiert ein `$request`-Objekt.
- Das `$request`-Objekt wird nach Abschluss der Anfrageabwicklung freigegeben.

## Controller-Lebenszyklus
- Jeder Controller wird in jedem Prozess nur einmal instanziiert, in mehreren Prozessen jedoch mehrmals (mit Ausnahme des Controller-Reuse, siehe [Controller-Lebenszyklus](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- Instanzen eines Controllers werden von mehreren Anfragen innerhalb des aktuellen Prozesses geteilt (mit Ausnahme des Controller-Reuse).
- Der Controller-Lebenszyklus endet, wenn der Prozess beendet wird (mit Ausnahme des Controller-Reuse).

## Zur Variablenlebensdauer
Webman basiert auf PHP und folgt daher vollständig dem Variablenbereinigungsmechanismus von PHP. Temporäre Variablen, die in der Geschäftslogik erstellt wurden, einschließlich Instanzen von Klassen, die mit dem `new`-Schlüsselwort erstellt wurden, werden automatisch nach Beendigung einer Funktion oder Methode bereinigt und müssen nicht manuell mit `unset` freigegeben werden. Das bedeutet, dass die Erfahrung der Entwicklung mit Webman im Wesentlichen der Entwicklung mit herkömmlichen Frameworks entspricht. Zum Beispiel wird die Instanz von `$foo` im folgenden Beispiel automatisch freigegeben, wenn die `index`-Methode abgeschlossen ist:

```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Angenommen, es gibt eine Klasse namens Foo
        return response($foo->sayHello());
    }
}
```
Wenn Sie möchten, dass eine Instanz einer Klasse wiederverwendet wird, können Sie die Klasse in einem statischen Attribut der Klasse oder in einem Attribut eines Objekts mit langer Lebensdauer (z. B. des Controllers) speichern oder die `get`-Methode des Container-Containers verwenden, um eine Instanz der Klasse zu initialisieren, zum Beispiel:

```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```
Die `Container::get()`-Methode dient zum Erstellen und Speichern von Klasseninstanzen. Bei erneutem Aufruf mit denselben Parametern wird die zuvor erstellte Instanz zurückgegeben.

> **Hinweis**
> `Container::get()` kann nur Instanzen ohne Konstruktorargumente initialisieren. `Container::make()` kann Instanzen mit Konstruktorargumenten erstellen, und im Gegensatz zu `Container::get()` wird `Container::make()` die Instanz nicht wiederverwenden, was bedeutet, dass sie immer eine neue Instanz zurückgibt, auch bei wiederholtem Aufruf mit denselben Parametern.

# Über Speicherlecks
In den allermeisten Fällen tritt in unserem Geschäftscode kein Speicherleck auf (nur sehr wenige Benutzer haben davon berichtet). Es genügt, darauf zu achten, dass die langzeitigen Array-Daten nicht endlos wachsen. Betrachten Sie bitte den folgenden Code:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Array-Eigenschaft
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
Ein Controller ist standardmäßig langzeitig (mit Ausnahme des Controller-Reuse), und ebenso ist die Eigenschaft `$data` des Controllers ebenfalls langzeitig. Wenn die Anfragen für `foo/index` weiterhin hinzugefügt werden, wächst das Element `$data`-Array endlos, was zu einem Speicherleck führen kann.

Für weitere Informationen siehe [Speicherlecks](./memory-leak.md).
