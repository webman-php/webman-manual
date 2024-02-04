# Lebenszyklus

## Prozesslebenszyklus
- Jeder Prozess hat eine lange Lebensdauer.
- Jeder Prozess läuft unabhängig voneinander, ohne sich zu beeinflussen.
- Jeder Prozess kann während seiner Lebensdauer mehrere Anfragen bearbeiten.
- Beim Empfang von Befehlen wie `stop`, `reload` oder `restart` führt der Prozess einen Ausstieg durch und beendet den aktuellen Lebenszyklus.

> **Hinweis**
> Jeder Prozess arbeitet unabhängig voneinander, was bedeutet, dass jeder Prozess seine eigenen Ressourcen, Variablen und Klasseninstanzen verwaltet. Dies äußert sich darin, dass jeder Prozess seine eigene Datenbankverbindung hat und einige Singleton-Instanzen für jeden Prozess initialisiert werden. Dadurch werden viele Prozesse mehrfach initialisiert.

## Anfragelebenszyklus
- Jede Anfrage erzeugt ein `$request`-Objekt.
- Das `$request`-Objekt wird nach Abschluss der Anfrageverarbeitung freigegeben.

## Controller-Lebenszyklus
- Jeder Controller wird in jedem Prozess nur einmal instanziiert, in mehreren Prozessen mehrmals (mit Ausnahme der Controller-Wiederverwendung, siehe [Controller-Lebenszyklus](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- Controller-Instanzen werden von mehreren Anfragen innerhalb des aktuellen Prozesses geteilt (mit Ausnahme der Controller-Wiederverwendung).
- Der Controller-Lebenszyklus endet, wenn der Prozess beendet wird (mit Ausnahme der Controller-Wiederverwendung).

## Über den Variablen-Lebenszyklus
Da webman auf PHP basiert, folgt es vollständig dem Variablenentsorgungsmechanismus von PHP. Temporäre Variablen, einschließlich Instanzen von Klassen, die mit dem Schlüsselwort `new` erstellt wurden, werden automatisch nach dem Ende einer Funktion oder Methode entsorgt, und ein manuelles `unset` ist nicht erforderlich. Mit anderen Worten, die Entwicklung mit webman unterscheidet sich im Grunde nicht von der traditionellen Framework-Entwicklung. Im folgenden Beispiel wird die Instanz `$foo` automatisch freigegeben, wenn die Methode `index` abgeschlossen wird:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Hier wird angenommen, dass es eine Klasse Foo gibt
        return response($foo->sayHello());
    }
}
```
Wenn Sie eine Instanz einer Klasse wiederverwenden möchten, können Sie die Klasse in einer statischen Eigenschaft der Klasse oder in einer Eigenschaft eines langen Lebenszyklusobjekts (wie dem Controller) speichern. Alternativ können Sie die Methode `get` des Container-Containers verwenden, um eine Instanz der Klasse zu initialisieren, wie zum Beispiel:
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

Die Methode `Container::get()` wird verwendet, um eine Instanz der Klasse zu erstellen und zu speichern. Beim erneuten Aufruf mit denselben Parametern wird die zuvor erstellte Instanz zurückgegeben.

> **Hinweis**
> `Container::get()` kann nur Instanzen ohne Konstruktorparameter initialisieren. `Container::make()` kann Instanzen mit Konstruktoren parametern erstellen, aber im Gegensatz zu `Container::get()` wird `Container::make()` die Instanz nicht wiederverwenden, dh selbst bei gleichen Parametern wird `Container::make()` immer eine neue Instanz zurückgeben.

# Über Memory Leaks
In den meisten Fällen tritt in unseren Geschäftslogiken kein Memory Leak auf (nur sehr wenige Benutzer haben auf Memory Leaks hingewiesen). Es ist ausreichend, darauf zu achten, dass sich die Array-Daten mit langem Lebenszyklus nicht unbegrenzt erweitern. Sehen Sie sich bitte den folgenden Code an:
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
        return response('Hallo Index');
    }

    public function hello(Request $request)
    {
        return response('Hallo Webman');
    }
}
```
Ein Controller ist standardmäßig mit einem langen Lebenszyklus ausgestattet (mit Ausnahme der Controller-Wiederverwendung), und die Array-Eigenschaft `$data` desselben Controllers hat ebenfalls einen langen Lebenszyklus. Mit zunehmenden Anfragen an `foo/index` erweitert sich das `$data`-Array und führt zu einem Memory Leak.

Für weitere Informationen siehe [Memory-Leaks](./memory-leak.md)
