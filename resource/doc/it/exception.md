# Gestione delle eccezioni

## Configurazione
`config/exception.php`
```php
return [
    // Qui è possibile configurare la classe di gestione delle eccezioni
    '' => support\exception\Handler::class,
];
```
In modalità multi-applicazione, è possibile configurare una classe di gestione delle eccezioni separata per ogni applicazione, vedere [Multi-Applicazione](multiapp.md).

## Classe di gestione delle eccezioni predefinita
Nel webman, le eccezioni sono gestite di default dalla classe `support\exception\Handler`. È possibile modificare la classe predefinita di gestione delle eccezioni modificando il file di configurazione `config/exception.php`. La classe di gestione delle eccezioni deve implementare l'interfaccia `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * Registra il log
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Reinderizza la risposta
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## Reindirizzamento della risposta
Il metodo `render` nella classe di gestione delle eccezioni è utilizzato per reindirizzare la risposta.

Se il valore `debug` nel file di configurazione `config/app.php` è impostato su `true` (di seguito indicato come `app.debug=true`), verranno restituite informazioni dettagliate sull'eccezione, altrimenti verranno restituite informazioni concise sull'eccezione.

Se la richiesta è in attesa di una risposta in formato json, le informazioni sull'eccezione restituite saranno in formato json, come ad esempio
```json
{
    "code": "500",
    "msg": "Informazioni sull'eccezione"
}
```
Se `app.debug=true`, i dati json includeranno anche un campo `trace` con i dettagli della traccia delle chiamate.

È possibile scrivere una propria classe di gestione delle eccezioni per modificare la logica predefinita di gestione delle eccezioni.

# Eccezione Commerciale BusinessException
A volte potremmo voler interrompere una richiesta all'interno di una funzione nidificata e restituire un messaggio di errore al client; in questo caso possiamo utilizzare l'eccezione `BusinessException` per farlo.
Ad esempio:

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
            throw new BusinessException('Parametro errato', 3000);
        }
    }
}
```
Nell'esempio sopra verrà restituito
```json
{"code": 3000, "msg": "Parametro errato"}
```
> **Nota**
> L'eccezione BusinessException non richiede di essere catturata da un blocco try-catch; il framework si occuperà automaticamente di catturare e restituire un output appropriato in base al tipo di richiesta.

## Eccezione Commerciale Personalizzata
Se la risposta sopra non soddisfa le tue esigenze e ad esempio vuoi cambiare `msg` in `message`, è possibile creare una propria eccezione `MyBusinessException`.
Creare il file `app/exception/MyBusinessException.php` con il seguente contenuto:
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
        // Se la richiesta è in formato json, restituisci i dati in formato json
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Altrimenti, restituisci una pagina
        return new Response(200, [], $this->getMessage());
    }
}
```

In questo modo, quando viene lanciata l'eccezione nella logica di business con
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parametro errato', 3000);
```
una richiesta in formato json riceverà una risposta in json simile a quella seguente
```json
{"code": 3000, "message": "Parametro errato"}
```
> **Nota**
> Poiché l'eccezione BusinessException è relativa ad errori di logica di business (ad esempio errori nei parametri di input dell'utente), è prevedibile; di conseguenza, il framework non la considera un errore fatale e non vi registrerà nessun log.

## Conclusione
In qualsiasi situazione in cui si desideri interrompere la richiesta corrente e restituire un messaggio al client, si può considerare l'utilizzo dell'eccezione `BusinessException`.
