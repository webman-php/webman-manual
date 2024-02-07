# Fehlerbehandlung

## Konfiguration
`config/exception.php`
```php
return [
    // Hier konfigurieren Sie die Ausnahmebehandlungsklasse
    '' => support\exception\Handler::class,
];
```
Im Multi-App-Modus können Sie für jede Anwendung eine eigene Ausnahmebehandlungsklasse konfigurieren. Siehe [Multi-App](multiapp.md) für weitere Informationen.

## Standardausnahmebehandlungsklasse
In webman wird die Ausnahme standardmäßig von der Klasse `support\exception\Handler` behandelt. Sie können die Standardausnahmebehandlungsklasse in der Konfigurationsdatei `config/exception.php` ändern. Die Ausnahmebehandlungsklasse muss das `Webman\Exception\ExceptionHandlerInterface`-Interface implementieren.
```php
interface ExceptionHandlerInterface
{
    /**
     * Protokolliert das Ereignis
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Rendert die Response
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## Rendern der Antwort
Die Methode `render` in der Ausnahmebehandlungsklasse wird zum Rendern der Antwort verwendet. 

Wenn der Wert von `debug` in der Konfigurationsdatei `config/app.php` auf `true` gesetzt ist (im Folgenden als `app.debug=true` bezeichnet), werden detaillierte Ausnahmeinformationen zurückgegeben. Andernfalls werden verkürzte Ausnahmeinformationen zurückgegeben.

Wenn die Anfrage eine JSON-Antwort erwartet, werden die Ausnahmeinformationen als JSON zurückgegeben, z. B.
```json
{
    "code": "500",
    "msg": "Ausnahmeinformationen"
}
```
Wenn `app.debug=true`, wird im JSON-Feedback zusätzlich ein `trace`-Feld hinzugefügt, das einen detaillierten Aufrufstapel zurückgibt.

Sie können Ihre eigene Ausnahmebehandlungsklasse schreiben, um die Standardausnahmebehandlungslogik zu ändern.

# Geschäftsfehler BusinessException
Manchmal möchten wir eine Anfrage in einer verschachtelten Funktion abbrechen und dem Client eine Fehlermeldung zurückgeben. Sie können dies erreichen, indem Sie `BusinessException` werfen.
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
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Parameterfehler', 3000);
        }
    }
}
```
Das obige Beispiel gibt eine Nachricht wie folgt zurück:
```json
{"code": 3000, "msg": "Parameterfehler"}
```

> **Bitte beachten**
> Geschäftsfehler BusinessException müssen nicht von der Anwendung aufgefangen werden. Das Framework erfasst und gibt automatisch die entsprechende Ausgabe zurück.

## Benutzerdefinierte Geschäftsfehler

Wenn die oben genannte Antwort nicht Ihren Anforderungen entspricht und Sie beispielsweise die Meldung `msg` in `message` ändern möchten, können Sie eine benutzerdefinierte Ausnahme namens `MyBusinessException` erstellen.

Erstellen Sie die Datei `app/exception/MyBusinessException.php` mit folgendem Inhalt:
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
        // JSON-Anfrage gibt JSON-Daten zurück
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Nicht-JSON-Anfragen geben eine Seite zurück
        return new Response(200, [], $this->getMessage());
    }
}
```
Auf diese Weise wird bei einem Geschäftsaufruf
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parameterfehler', 3000);
```
eine JSON-Anfrage eine JSON-Antwort wie folgt erhalten:
```json
{"code": 3000, "message": "Parameterfehler"}
```

> **Hinweis**
> Da die BusinessException zu den erwarteten Geschäftsfehlern (z. B. falsche Benutzereingaben) gehört und vorhersehbar ist, geht das Framework nicht davon aus, dass es sich um einen schwerwiegenden Fehler handelt, und generiert keine Protokolleinträge.

## Zusammenfassung
Jederzeit, wenn Sie die aktuelle Anfrage unterbrechen und dem Client eine Nachricht zurückgeben möchten, können Sie erwägen, die `BusinessException` zu verwenden.
