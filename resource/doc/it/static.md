## Gestione dei file statici
webman supporta l'accesso ai file statici, i quali sono tutti posizionati nella directory `public`. Ad esempio, accedere a `http://127.0.0.1:8787/upload/avatar.png` in realtà equivale ad accedere a `{directory principale del progetto}/public/upload/avatar.png`.

> **Nota**
> A partire dalla versione 1.4, webman supporta i plugin dell'applicazione. L'accesso ai file statici che inizia con `/app/xx/nomefile` in realtà punta alla directory `public` del plugin dell'applicazione, quindi a partire da webman >=1.4.0 non è supportato l'accesso alla directory `{directory principale del progetto}/public/app/`.
> Per ulteriori informazioni, consulta il [plugin dell'applicazione](./plugin/app.md).

### Disattivare il supporto dei file statici
Se non è necessario il supporto dei file statici, modifica il valore dell'opzione `enable` in `config/static.php` in false. Dopo la disattivazione, l'accesso a tutti i file statici restituirà un errore 404.

### Modificare la directory dei file statici
webman utilizza di default la directory public come directory dei file statici. Se si desidera modificarla, è possibile farlo modificando la funzione di supporto `public_path()` in `support/helpers.php`.

### Middleware per i file statici
webman include un middleware per i file statici, che si trova in `app/middleware/StaticFile.php`.
A volte è necessario effettuare delle operazioni sui file statici, ad esempio aggiungere intestazioni HTTP cross-origin o impedire l'accesso ai file con un punto (`.`) iniziale, e per fare ciò è possibile utilizzare questo middleware.

Il file `app/middleware/StaticFile.php` ha un contenuto simile al seguente:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Vietare l'accesso ai file nascosti che iniziano con .
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Aggiungere intestazioni HTTP cross-origin
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Se è necessario utilizzare questo middleware, è necessario attivarlo nell'opzione `middleware` in `config/static.php`.
