# Gestione delle eccezioni

## Configurazione
`config/exception.php`
```php
return [
    // Configura qui la classe di gestione delle eccezioni
    '' => support\exception\Handler::class,
];
```
In modalità multi-applicazione, puoi configurare una classe di gestione delle eccezioni separata per ogni app, vedi [Multiapp](multiapp.md)


## Classe di gestione delle eccezioni predefinita
Nel webman, le eccezioni di default sono gestite dalla classe `support\exception\Handler`. È possibile modificare la classe predefinita di gestione delle eccezioni nel file di configurazione `config/exception.php`. La classe di gestione delle eccezioni deve implementare l'interfaccia `Webman\Exception\ExceptionHandlerInterface`.
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
     * Rendering della risposta
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## Rendering della risposta
Il metodo `render` della classe di gestione delle eccezioni viene utilizzato per il rendering della risposta.

Se il valore di `debug` nel file di configurazione `config/app.php` è impostato su `true` (di seguito abbreviato come `app.debug=true`), verranno restituite informazioni dettagliate sull'eccezione, altrimenti verranno restituite informazioni concise sull'eccezione.

Se la richiesta è in formato json, le informazioni sull'eccezione verranno restituite in formato json, ad esempio
```json
{
    "code": "500",
    "msg": "Messaggio dell'eccezione"
}
```
Se `app.debug=true`, i dati json conterranno un campo aggiuntivo `trace` che restituirà una dettagliata traccia di chiamata.

È possibile scrivere una propria classe di gestione delle eccezioni per modificare la logica predefinita di gestione delle eccezioni.

# Eccezione aziendale BusinessException
A volte potremmo volere interrompere una richiesta all'interno di una funzione innestata e restituire un messaggio di errore al client, in tal caso è possibile farlo lanciando `BusinessException`.
Per esempio:

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
            throw new BusinessException('Errore nei parametri', 3000);
        }
    }
}
```

Nell'esempio sopra verrà restituito
```json
{"code": 3000, "msg": "Errore nei parametri"}
```

> **Nota**
> L'eccezione aziendale BusinessException non richiede una gestione tramite try-catch, il framework la catturerà automaticamente e restituirà un'uscita appropriata in base al tipo di richiesta.

## Eccezione aziendale personalizzata

Se la risposta precedente non soddisfa le tue esigenze, per esempio se desideri modificare `msg` in `message`, puoi personalizzare una `MyBusinessException`.

Crea il file `app/exception/MyBusinessException.php` con il seguente contenuto
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
        // Restituisce i dati json in caso di richiesta json
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Altrimenti restituisce una pagina
        return new Response(200, [], $this->getMessage());
    }
}
```

In questo modo, chiamando
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Errore nei parametri', 3000);
```
la richiesta in formato json riceverà una risposta simile a questa
```json
{"code": 3000, "message": "Errore nei parametri"}
```

> **Nota**
> Poiché l'eccezione BusinessException è un'eccezione aziendale (ad es. errore nei parametri inseriti dall'utente), è prevedibile, quindi il framework non la considererà un errore fatale e non ne registrerà i log.

## Conclusione
È possibile utilizzare `BusinessException` ogni volta che si desidera interrompere la richiesta corrente e restituire informazioni al client.
