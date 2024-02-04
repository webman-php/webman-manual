# Exception Handling

## Konfiguration
`config/exception.php`
```php
return [
    // Hier wird die Ausnahmeverarbeitungsklasse konfiguriert
    '' => support\exception\Handler::class,
];
```
Im Multi-App-Modus können Sie für jede Anwendung eine separate Ausnahmeverarbeitungsklasse konfigurieren. Siehe [Multi-App](multiapp.md) für weitere Informationen.


## Standard-Ausnahmeverarbeitungsklasse
In webman wird die Ausnahme standardmäßig von der Klasse `support\exception\Handler` verarbeitet. Sie können die Standard-Ausnahmeverarbeitungsklasse ändern, indem Sie die Konfigurationsdatei `config/exception.php` anpassen. Die Ausnahmeverarbeitungsklasse muss das `Webman\Exception\ExceptionHandlerInterface`-Interface implementieren.
```php
interface ExceptionHandlerInterface
{
    /**
     * Protokollieren von Fehlern
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Rendern von Antwort
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## Rendern von Antworten
Die Methode `render` in der Ausnahmeverarbeitungsklasse wird zum Rendern von Antworten verwendet.

Wenn der Wert von `debug` in der Konfigurationsdatei `config/app.php` auf `true` gesetzt ist (im Folgenden als `app.debug=true` bezeichnet), werden detaillierte Ausnahmeinformationen zurückgegeben. Andernfalls werden kompakte Ausnahmeinformationen zurückgegeben.

Wenn die Anfrage eine JSON-Antwort erwartet, wird die Ausnahmeinformation im JSON-Format zurückgegeben, z.B.
```json
{
    "code": "500",
    "msg": "Fehlermeldung"
}
```
Wenn `app.debug=true` ist, wird im JSON-Daten ein zusätzliches Feld `trace` hinzugefügt, das detaillierte Aufruflisten enthält.

Sie können Ihre eigene Ausnahmeverarbeitungsklasse schreiben, um die standardmäßige Ausnahmeverarbeitungslogik zu ändern.

# Geschäfts-Ausnahme BusinessException
Manchmal möchten wir eine Anfrage in einer verschachtelten Funktion abbrechen und eine Fehlermeldung an den Client zurückgeben. In diesem Fall können wir dies durch Werfen einer `BusinessException` erreichen.
Beispiel:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('Hallo Index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Parameterfehler', 3000);
        }
    }
}
```

Das obige Beispiel gibt z.B. das Folgende zurück
```json
{"code": 3000, "msg": "Parameterfehler"}
```

> **Hinweis**
> Die Geschäfts-Ausnahme BusinessException muss nicht von der Anwendung aufgefangen werden, sie wird vom Framework automatisch erfasst und basierend auf dem Anfrage-Typ die entsprechende Ausgabe zurückgegeben.

## Eigene Geschäfts-Ausnahme

Wenn die oben genannte Antwort nicht Ihren Anforderungen entspricht, z.B. wenn Sie `msg` in `message` ändern möchten, können Sie eine eigene `MyBusinessException` erstellen.

Erstellen Sie die Datei `app/exception/MyBusinessException.php` mit folgendem Inhalt
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // Bei JSON-Anforderung werden JSON-Daten zurückgegeben
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Bei Nicht-JSON-Anforderung wird eine Seite zurückgegeben
        return new Response(200, [], $this->getMessage());
    }
}
```

Auf diese Weise wird bei einem Geschäftsaufruf
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parameterfehler', 3000);
```
Bei einer JSON-Anfrage wird eine ähnliche JSON-Antwort wie folgt zurückgegeben
```json
{"code": 3000, "message": "Parameterfehler"}
```

> **Hinweis**
> Da die BusinessException-Ausnahme eine Geschäfts-Ausnahme darstellt (z. B. einen Fehler bei der Benutzereingabe), die vorhersehbar ist, geht das Framework nicht davon aus, dass es sich um einen schwerwiegenden Fehler handelt, und protokolliert ihn nicht.

## Zusammenfassung
Verwenden Sie die `BusinessException`-Ausnahme, wenn Sie an beliebiger Stelle eine Anfrage abbrechen und eine Nachricht an den Client zurückgeben möchten.
