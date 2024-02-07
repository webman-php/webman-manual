# Controller

Erstellen Sie die Controller-Datei `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('Hallo Index');
    }
    
    public function hello(Request $request)
    {
        return response('Hallo Webman');
    }
}
```

Wenn Sie `http://127.0.0.1:8787/foo` aufrufen, wird `Hallo Index` angezeigt.

Wenn Sie `http://127.0.0.1:8787/foo/hello` aufrufen, wird `Hallo Webman` angezeigt.

Natürlich können Sie durch die Routenkonfiguration die Routenregeln ändern, siehe [Routen](route.md).

> **Hinweis**
> Wenn beim Aufrufen ein 404-Fehler auftritt, öffnen Sie `config/app.php`, setzen Sie `controller_suffix` auf `Controller` und starten Sie das System neu.

## Controller-Suffix
Webman unterstützt ab Version 1.3 die Einstellung eines Controller-Suffix in `config/app.php`. Wenn `controller_suffix` in `config/app.php` auf `''` festgelegt ist, wird der Controller wie folgt aussehen:

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('Hallo Index');
    }
    
    public function hello(Request $request)
    {
        return response('Hallo Webman');
    }
}
```

Es wird dringend empfohlen, das Controller-Suffix auf `Controller` festzulegen, um Namenskonflikte mit Modellklassen zu vermeiden und die Sicherheit zu erhöhen.

## Erklärung
- Das Framework übergibt automatisch ein `support\Request`-Objekt an den Controller. Über dieses Objekt können Benutzereingabedaten (GET, POST, Header, Cookie usw.) abgerufen werden. Siehe [Anfrage](request.md).
- Der Controller kann Zahlen, Zeichenfolgen oder ein `support\Response`-Objekt zurückgeben, darf jedoch keine anderen Datentypen zurückgeben.
- Ein `support\Response`-Objekt kann mithilfe von Hilfsfunktionen wie `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` usw. erstellt werden.

## Controller-Lebenszyklus

Wenn `controller_reuse` in `config/app.php` auf `false` gesetzt ist, wird für jede Anfrage eine Instanz des entsprechenden Controllers initialisiert. Nach Abschluss der Anfrage wird die Controller-Instanz zerstört, was dem üblichen Betriebsmechanismus traditioneller Frameworks entspricht.

Wenn `controller_reuse` in `config/app.php` auf `true` gesetzt ist, werden alle Anfragen die Controller-Instanz wiederverwenden. Das bedeutet, dass die Controller-Instanz nach der Erstellung im Speicher verbleibt und für alle folgenden Anfragen wiederverwendet wird.

> **Hinweis**
> Das Deaktivieren der Controller-Wiederverwendung erfordert Webman>=1.4.0. Das bedeutet, dass in Versionen vor 1.4.0 die Controller standardmäßig für alle Anfragen wiederverwendet wurden und diese Einstellung nicht geändert werden konnte.

> **Hinweis**
> Beim Aktivieren der Controller-Wiederverwendung sollten keine Controller-Eigenschaften geändert werden, da diese Änderungen sich auf nachfolgende Anfragen auswirken. Zum Beispiel

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // Diese Methode behält das Modell nach dem ersten Aufruf von update?id=1 bei.
        // Wenn delete?id=2 erneut aufgerufen wird, werden die Daten von 1 gelöscht.
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Hinweis**
> In einem Konstruktor `__construct()` des Controllers wird das Anfrageergebnis nicht erzeugt, z.B.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Das Return im Konstruktor hat keine Wirkung - der Browser erhält diese Antwort nicht
        return response('Hallo'); 
    }
}
```

## Unterschied zwischen Controller ohne und mit Wiederverwendung
Der Unterschied ist wie folgt:

#### Controller ohne Wiederverwendung
Für jede Anfrage wird eine neue Controller-Instanz erstellt, die nach Abschluss der Anfrage freigegeben und der Speicher freigegeben wird. Eine solche Vorgehensweise entspricht der Arbeitsweise traditioneller Frameworks und entspricht den Gewohnheiten der meisten Entwickler. Aufgrund des wiederholten Erstellens und Freigebens von Controllern ist die Leistung etwas schlechter als bei Controllern mit Wiederverwendung (bei helloworld-Leistungstests ist die Leistungseinbuße ca. 10 %, bei realen Anwendungen kann dies vernachlässigt werden).

#### Controller mit Wiederverwendung
Wenn wiederverwendet wird, wird eine Instanz des Controllers nur einmal pro Prozess erstellt, und nach Abschluss der Anfrage wird diese Instanz nicht freigegeben, sondern bleibt für nachfolgende Anfragen im gleichen Prozess erhalten. Die Leistung von Controllern mit Wiederverwendung ist besser, entspricht jedoch nicht den Gewohnheiten der meisten Entwickler.

#### In folgenden Fällen kann die Controller-Wiederverwendung nicht verwendet werden

Wenn eine Anfrage die Eigenschaften des Controllers ändert, kann die Wiederverwendung der Controller nicht aktiviert werden, da diese Eigenschaftsänderungen sich auf nachfolgende Anfragen auswirken.

Einige Entwickler wollen im Konstruktor `__construct()` des Controllers initialisierende Arbeiten für jede Anfrage durchführen. In einem solchen Fall kann die Controller-Wiederverwendung nicht verwendet werden, da der Konstruktor in einem Prozess nur einmal aufgerufen wird und nicht für jede Anfrage.
