## Gestione dei file statici
webman supporta l'accesso ai file statici, che sono tutti posizionati nella directory `public`. Ad esempio, accedere a `http://127.0.0.8787/upload/avatar.png` corrisponde effettivamente ad accedere a `{main project directory}/public/upload/avatar.png`.

> **Nota**
> A partire dalla versione 1.4, webman supporta i plugin dell'applicazione. L'accesso ai file statici che inizia con `/app/xx/nomefile` in realtà corrisponde all'accesso alla directory `public` del plugin dell'applicazione, il che significa che in webman >=1.4.0 non è supportato l'accesso alle directory sotto `{main project directory}/public/app/`.
> Per ulteriori informazioni, consultare il [plugin dell'applicazione](./plugin/app.md).

### Disabilita il supporto dei file statici
Se non è necessario il supporto dei file statici, modificare il file `config/static.php` impostando l'opzione `enable` su false. Dopo la disabilitazione, l'accesso a tutti i file statici restituirà un errore 404.

### Modifica della directory dei file statici
webman utilizza per impostazione predefinita la directory public come directory dei file statici. Se è necessario modificarla, modificare la funzione di supporto `public_path()` del file `support/helpers.php`.

### Middleware per i file statici
webman include un middleware per i file statici, posizionato in `app/middleware/StaticFile.php`.
A volte è necessario eseguire alcune operazioni sui file statici, ad esempio aggiungere intestazioni http per il controllo degli accessi da server diversi o impedire l'accesso ai file che iniziano con un punto (`.`). È possibile utilizzare questo middleware per farlo.

Il contenuto di `app/middleware/StaticFile.php` è simile al seguente:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        // Impedisce l'accesso ai file nascosti che iniziano con .
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Aggiunge intestazioni http per il controllo degli accessi da server diversi
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Se è necessario utilizzare questo middleware, è necessario abilitarlo nell'opzione `middleware` del file `config/static.php`.
