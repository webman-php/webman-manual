# Documentazione

## Ottenere l'oggetto richiesta
webman inietterà automaticamente l'oggetto richiesta come primo parametro nel metodo di azione, ad esempio

**Esempio**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Ottiene il parametro name dalla richiesta get, se non viene passato il parametro name, restituisce $default_name
        $name = $request->get('name', $default_name);
        // Restituisce una stringa al browser
        return response('ciao ' . $name);
    }
}
```

Con l'oggetto `$request` possiamo ottenere qualsiasi dato relativo alla richiesta.

**A volte vogliamo ottenere l'oggetto `$request` della richiesta corrente in un'altra classe, in questo caso basta usare la funzione di supporto `request()`**;

## Ottenere parametri di richiesta get

**Ottenere l'intero array get**
```php
$request->get();
```
Se la richiesta non contiene parametri get, restituisce un array vuoto.

**Ottenere un valore dell'array get**
```php
$request->get('name');
```
Se l'array get non contiene questo valore, restituisce null.

È anche possibile passare un valore predefinito come secondo parametro al metodo get, in modo che se non viene trovato il valore corrispondente nell'array get, verrà restituito il valore predefinito. Ad esempio:
```php
$request->get('nome', 'tom');
```

## Ottenere i parametri di richiesta post
**Ottenere l'intero array post**
```php
$request->post();
```
Se la richiesta non contiene parametri post, restituisce un array vuoto.

**Ottenere un valore dell'array post**
```php
$request->post('name');
```
Se l'array post non contiene questo valore, restituisce null.

Come nel caso del metodo get, è possibile passare un valore predefinito come secondo parametro al metodo post, in modo che se non viene trovato il valore corrispondente nell'array post, verrà restituito il valore predefinito. Ad esempio:
```php
$request->post('name', 'tom');
```

## Ottenere il corpo raw della richiesta post
```php
$post = $request->rawBody();
```
Questa funzione è simile all'operazione `file_get_contents("php://input")` di `php-fpm`. È utile per ottenere il corpo raw della richiesta http quando si ricevono dati post in un formato diverso da `application/x-www-form-urlencoded`.

## Ottenere l'header
**Ottenere l'intero array header**
```php
$request->header();
```
Se la richiesta non contiene parametri header, restituisce un array vuoto. Nota che tutte le chiavi sono in minuscolo.

**Ottenere un valore dell'array header**
```php
$request->header('host');
```
Se l'array header non contiene questo valore, restituisce null. Nota che tutte le chiavi sono in minuscolo.

Come nel caso del metodo get, è possibile passare un valore predefinito come secondo parametro al metodo header, in modo che se non viene trovato il valore corrispondente nell'array header, verrà restituito il valore predefinito. Ad esempio:
```php
$request->header('host', 'localhost');
```

## Ottenere i cookie
**Ottenere l'intero array cookie**
```php
$request->cookie();
```
Se la richiesta non contiene parametri cookie, restituisce un array vuoto.

**Ottenere un valore dell'array cookie**
```php
$request->cookie('name');
```
Se l'array cookie non contiene questo valore, restituisce null.

Come nel caso del metodo get, è possibile passare un valore predefinito come secondo parametro al metodo cookie, in modo che se non viene trovato il valore corrispondente nell'array cookie, verrà restituito il valore predefinito. Ad esempio:
```php
$request->cookie('name', 'tom');
```

## Ottenere tutti i dati di input
Includi una collezione di `post` e `get`.
```php
$request->all();
```

## Ottenere un valore di input specifico
Ottenere un valore specifico dalla collezione `post` e `get`.
```php
$request->input('name', $default_value);
```

## Ottenere parte dei dati di input
Ottenere parte dei dati da `post` e `get`.
```php
// Ottieni un array composto da username e password, se la chiave corrispondente non esiste, verrà ignorata
$only = $request->only(['username', 'password']);
// Ottieni tutti i dati tranne avatar e age
$except = $request->except(['avatar', 'age']);
```

## Ottenere file caricati
**Ottenere l'intero array di file caricati**
```php
$request->file();
```

Form simile:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

Il risultato restituito da `$request->file()` è simile a:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Si tratta di un array di istanze di `webman\Http\UploadFile`. La classe `webman\Http\UploadFile` estende la classe predefinita di PHP [`SplFileInfo`](https://www.php.net/manual/it/class.splfileinfo.php) e fornisce alcuni metodi utili.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Verifica se il file è valido, ad esempio true|false
            var_export($spl_file->getUploadExtension()); // Estensione del file caricato, ad esempio 'jpg'
            var_export($spl_file->getUploadMimeType()); // Tipo mime del file caricato, ad esempio 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Ottiene il codice di errore del caricamento, ad esempio UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Nome del file caricato, ad esempio 'my-test.jpg'
            var_export($spl_file->getSize()); // Ottiene la dimensione del file, ad esempio 13364, in byte
            var_export($spl_file->getPath()); // Ottiene la directory di caricamento, ad esempio '/tmp'
            var_export($spl_file->getRealPath()); // Ottiene il percorso del file temporaneo, ad esempio `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Nota:**

- Dopo il caricamento, il file verrà rinominato come file temporaneo, ad esempio `/tmp/workerman.upload.SRliMu`
- Le dimensioni dei file caricati sono limitate dal [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), di default 10M, è possibile modificare il valore predefinito in `config/server.php` cambiando `max_package_size`.
- Al termine della richiesta, il file temporaneo verrà automaticamente eliminato.
- Se la richiesta non contiene file caricati, `$request->file()` restituirà un array vuoto.
- Il caricamento dei file non supporta il metodo `move_uploaded_file()`, si consiglia di utilizzare il metodo `$file->move()`, vedere l'esempio seguente

### Ottenere un file di caricamento specifico
```php
$request->file('avatar');
```
Se il file esiste, restituirà un'istanza corrispondente di `webman\Http\UploadFile`, altrimenti restituirà null.

**Esempio**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## Ottenere l'host
Ottenere le informazioni sull'host della richiesta.
```php
$request->host();
```
Se l'indirizzo della richiesta non è sulla porta standard 80 o 443, le informazioni sull'host potrebbero includere la porta, ad esempio `example.com:8080`. Se non è necessaria la porta, è possibile passare `true` come primo parametro.

```php
$request->host(true);
```

## Ottenere il metodo di richiesta
```php
 $request->method();
```
Il valore restituito potrebbe essere uno tra `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Ottenere l'uri della richiesta
```php
$request->uri();
```
Restituisce l'uri della richiesta, compreso il percorso e la stringa della query.

## Ottenere il percorso della richiesta
```php
$request->path();
```
Restituisce il percorso della richiesta.

## Ottenere la stringa della query della richiesta
```php
$request->queryString();
```
Restituisce la stringa della query della richiesta.

## Ottenere l'URL della richiesta
Il metodo `url()` restituisce l'URL senza i parametri `Query`.
```php
$request->url();
```
Restituisce qualcosa come `//www.workerman.net/workerman-chat`

Il metodo `fullUrl()` restituisce l'URL con i parametri `Query`.
```php
$request->fullUrl();
```
Restituisce qualcosa come `//www.workerman.net/workerman-chat?type=download`

> **Nota**
> `url()` e `fullUrl()` non includono la parte del protocollo (non includono http o https). Poiché nei browser l'utilizzo di `//example.com` come indirizzo inizia con `//` riconoscerà automaticamente il protocollo del sito corrente, effettuando automaticamente la richiesta tramite http o https.

Se si utilizzano proxy nginx, è necessario aggiungere `proxy_set_header X-Forwarded-Proto $scheme;` alla configurazione nginx, [fare riferimento al proxy nginx](others/nginx-proxy.md),
quindi è possibile utilizzare `$request->header('x-forwarded-proto');` per verificare se si tratta di http o https, ad esempio:
```php
echo $request->header('x-forwarded-proto'); // restituirà http o https
```

## Ottenere la versione HTTP della richiesta
```php
$request->protocolVersion();
```
Restituisce la stringa `1.1` o `1.0`.

## Ottenere l'ID della sessione della richiesta
```php
$request->sessionId();
```
Restituisce una stringa composta da lettere e numeri.

## Ottenere l'indirizzo IP del client della richiesta
```php
$request->getRemoteIp();
```

## Ottenere la porta del client della richiesta
```php
$request->getRemotePort();
```
## Ottenere l'IP reale del client della richiesta
```php
$request->getRealIp($safe_mode=true);
```

Quando il progetto utilizza un proxy (ad esempio nginx), utilizzando `$request->getRemoteIp()` si ottiene spesso l'IP del server proxy (simile a `127.0.0.1` `192.168.x.x`) anziché l'IP reale del client. In questo caso, si può provare a utilizzare `$request->getRealIp()` per ottenere l'IP reale del client.

`$request->getRealIp()` cercherà di ottenere l'IP reale del client dai campi dell'intestazione HTTP `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via`.

> Poiché le intestazioni HTTP possono essere facilmente contraffatte, l'IP del client ottenuto da questo metodo non è al 100% affidabile, specialmente quando `$safe_mode` è impostato su false. Un modo più affidabile per ottenere l'IP reale del client attraverso un proxy è conoscere l'IP del server proxy sicuro e sapere esplicitamente quale intestazione HTTP porta l'IP reale. Se l'IP restituito da `$request->getRemoteIp()` corrisponde a un server proxy sicuro noto, è quindi possibile ottenere l'IP reale tramite `$request->header('intestazione_che_contiene_l_IP_reale')`.

## Ottenere l'IP del server
```php
$request->getLocalIp();
```

## Ottenere la porta del server
```php
$request->getLocalPort();
```

## Verificare se la richiesta è un'richiesta Ajax
```php
$request->isAjax();
```

## Verificare se la richiesta è una richiesta Pjax
```php
$request->isPjax();
```

## Verificare se la richiesta prevede una risposta in formato json
```php
$request->expectsJson();
```

## Verificare se il client accetta una risposta in formato json
```php
$request->acceptJson();
```

## Ottenere il nome del plugin richiesto
Se la richiesta non proviene da un plugin, verrà restituita una stringa vuota `''`.
```php
$request->plugin;
```
> Questa funzionalità richiede webman >= 1.4.0

## Ottenere il nome dell'applicazione della richiesta
Quando si tratta di un'unica applicazione, verrà restituita sempre una stringa vuota `''`; in caso di [applicazioni multiple](multiapp.md), verrà restituito il nome dell'applicazione.
```php
$request->app;
```

> Poiché le funzioni di chiusura non appartengono a nessuna applicazione, le richieste provenienti da un percorso con funzione di chiusura restituiranno sempre una stringa vuota `''`.
> Vedi [Percorso](route.md) per le funzioni di chiusura.

## Ottenere il nome della classe del controller della richiesta
Ottenere il nome della classe corrispondente al controller
```php
$request->controller;
```
Restituisce qualcosa come `app\controller\IndexController`

> Poiché le funzioni di chiusura non appartengono a nessun controller, le richieste provenienti da una route con funzione di chiusura restituiranno sempre una stringa vuota `''`.
> Vedi [Percorso](route.md) per le funzioni di chiusura.

## Ottenere il nome del metodo della richiesta
Ottenere il nome del metodo del controller corrispondente alla richiesta
```php
$request->action;
```
Restituisce qualcosa come `index`

> Poiché le funzioni di chiusura non appartengono a nessun controller, le richieste provenienti da una route con funzione di chiusura restituiranno sempre una stringa vuota `''`.
> Vedi [Percorso](route.md) per le funzioni di chiusura.
