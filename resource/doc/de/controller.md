# Controller

Erstellen Sie eine neue Controller-Datei `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Beim Aufruf von `http://127.0.0.1:8787/foo` wird die Seite `hello index` zurückgeben.

Beim Aufruf von `http://127.0.0.1:8787/foo/hello` wird die Seite `hello webman` zurückgeben.

Natürlich können Sie die Routenregeln durch die Routenkonfiguration ändern, siehe [Routen](route.md).

> **Hinweis**
> Wenn ein 404-Fehler auftritt, öffnen Sie bitte `config/app.php`, setzen Sie `controller_suffix` auf `Controller` und starten Sie neu.

## Controller-Suffix
Ab der Version 1.3 unterstützt webman die Einstellung eines Controller-Suffixes in `config/app.php`. Wenn `controller_suffix` in `config/app.php` auf leer `''` gesetzt ist, sieht der Controller wie folgt aus:

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Es wird empfohlen, das Controller-Suffix auf `Controller` zu setzen, um Konflikte zwischen Controller- und Model-Klassennamen zu vermeiden und die Sicherheit zu erhöhen.

## Erläuterung
- Das Framework gibt automatisch ein `support\Request`-Objekt an den Controller weiter. Über dieses Objekt können Benutzereingabedaten (GET, POST, Header, Cookie usw.) abgerufen werden, siehe [Anfrage](request.md).
- Der Controller kann Zahlen, Zeichenfolgen oder ein `support\Response`-Objekt zurückgeben, aber keine anderen Datentypen.
- Ein `support\Response`-Objekt kann mithilfe der Hilfsfunktionen `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` usw. erstellt werden.

## Controller-Lebenszyklus

Wenn `controller_reuse` in `config/app.php` auf `false` gesetzt ist, wird für jede Anfrage eine entsprechende Controller-Instanz initialisiert. Nach Abschluss der Anfrage wird die Controller-Instanz zerstört, was dem herkömmlichen Arbeitsablauf von Frameworks entspricht.

Wenn `controller_reuse` in `config/app.php` auf `true` gesetzt ist, wird für alle Anfragen dieselbe Controller-Instanz wiederverwendet. Das bedeutet, dass die Controller-Instanz nach der Erstellung im Speicher verbleibt und für alle folgenden Anfragen wiederverwendet wird.

> **Hinweis**
> Um die Controller-Wiederverwendung zu deaktivieren, benötigen Sie webman>=1.4.0. Das bedeutet, dass vor Version 1.4.0 die Controller standardmäßig für alle Anfragen wiederverwendet wurden und nicht geändert werden konnten.

> **Hinweis**
> Wenn die Controller-Wiederverwendung aktiviert ist, sollten keine Controller-Eigenschaften geändert werden, da diese Änderungen sich auf nachfolgende Anfragen auswirken.

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
        // Diese Methode behält nach dem ersten Aufruf von update?id=1 das Modell bei
        // Wenn delete?id=2 erneut aufgerufen wird, werden die Daten von 1 gelöscht
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Hinweis**
> Ein `return` in der Konstruktorfunktion `__construct()` des Controllers hat keine Wirkung, z.B.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Ein `return` in der Konstruktorfunktion hat keine Wirkung; der Browser erhält keine Antwort
        return response('hello'); 
    }
}
```

## Unterschiede zwischen dem Nicht-Wiederverwenden und dem Wiederverwenden von Controllern
Die Unterschiede sind wie folgt:

#### Nicht-wiederverwendete Controller
Bei jeder Anfrage wird eine neue Controller-Instanz erstellt, die nach Abschluss der Anfrage freigegeben und der Speicher freigegeben wird. Wenn keine Wiederverwendung stattfindet, entspricht dies dem traditionellen Framework-Workflow. Aufgrund der wiederholten Erstellung und Zerstörung von Controllern ist die Leistung im Vergleich zur Verwendung von wiederverwendeten Controllern geringfügig schlechter (Leistungseinbußen von etwa 10 % bei Helloworld-Belastungstests; bei Geschäftsentwicklung können sie im Wesentlichen ignoriert werden).

#### Wiederverwendete Controller
In diesem Fall wird jede Instanz des Controllers nur einmal in einem Prozess erstellt. Nach Abschluss der Anfrage wird diese Instanz jedoch nicht freigegeben, sondern für nachfolgende Anfragen im selben Prozess wiederverwendet. Die Leistung ist bei wiederverwendeten Controllern besser, entspricht jedoch nicht dem gewohnten Verhalten der meisten Entwickler.

#### Situationen, in denen die Wiederverwendung von Controllern nicht verwendet werden kann
Die Wiederverwendung von Controllern kann nicht aktiviert werden, wenn Anfragen die Eigenschaften des Controllers ändern. Die Änderungen an diesen Attributen wirken sich auf nachfolgende Anfragen aus.

Einige Entwickler bevorzugen es, in der Konstruktorfunktion `__construct()` des Controllers für jede Anfrage eine Initialisierung vorzunehmen. In diesem Fall kann die Wiederverwendung von Controllern nicht aktiviert werden, da der Konstruktor einer Prozessinstanz nur einmal aufgerufen wird, nicht für jede Anfrage.


