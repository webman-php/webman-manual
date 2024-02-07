# Documentazione

## Ottenere l'oggetto della richiesta
Webman inietterà automaticamente l'oggetto della richiesta nel primo parametro del metodo action, ad esempio

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
        // Ottenere il parametro name dalla richiesta GET, se il parametro name non è stato passato, restituisce $default_name
        $name = $request->get('name', $default_name);
        // Restituire una stringa al browser
        return response('ciao ' . $name);
    }
}
```

Attraverso l'oggetto `$request` possiamo ottenere qualsiasi dato relativo alla richiesta.

**A volte potremmo voler ottenere l'oggetto `$request` della richiesta corrente in altre classi, in questo caso possiamo semplicemente utilizzare la funzione di supporto `request()`;**

## Ottenere i parametri della richiesta GET

**Ottenere l'intero array GET**
```php
$request->get();
```
Se la richiesta non contiene parametri GET, restituirà un array vuoto.

**Ottenere un valore specifico dell'array GET**
```php
$request->get('name');
```
Se l'array GET non contiene il valore specificato, restituirà null.

Possiamo anche passare un valore predefinito come secondo parametro al metodo get. Se l'array GET non contiene il valore corrispondente, verrà restituito il valore predefinito. Ad esempio:
```php
$request->get('name', 'tom');
```

## Ottenere i parametri della richiesta POST

**Ottenere l'intero array POST**
```php
$request->post();
```
Se la richiesta non contiene parametri POST, restituirà un array vuoto.

**Ottenere un valore specifico dell'array POST**
```php
$request->post('name');
```
Se l'array POST non contiene il valore specificato, restituirà null.

Come il metodo get, anche in questo caso possiamo passare un valore predefinito come secondo parametro. Se l'array POST non contiene il valore corrispondente, verrà restituito il valore predefinito. Ad esempio:
```php
$request->post('name', 'tom');
```

## Ottenere il corpo grezzo della richiesta POST
```php
$post = $request->rawBody();
```
Questa funzione è simile all'operazione `file_get_contents("php://input")` nei file `php-fpm`. È utile per ottenere il corpo grezzo della richiesta HTTP quando si tratta di dati di richiesta POST in formato diverso da `application/x-www-form-urlencoded`.

## Ottenere l'intestazione
**Ottenere l'intero array intestazione**
```php
$request->header();
```
Se la richiesta non contiene intestazioni, restituirà un array vuoto. Nota che tutte le chiavi sono in minuscolo.

**Ottenere un valore specifico dell'array intestazione**
```php
$request->header('host');
```
Se l'array intestazione non contiene il valore specificato, restituirà null. Nota che tutte le chiavi sono in minuscolo.

Come il metodo get, possiamo anche passare un valore predefinito come secondo parametro al metodo header. Se l'array intestazione non contiene il valore corrispondente, verrà restituito il valore predefinito. Ad esempio:
```php
$request->header('host', 'localhost');
```

## Ottenere i cookie
**Ottenere l'intero array dei cookie**
```php
$request->cookie();
```
Se la richiesta non contiene cookie, restituirà un array vuoto.

**Ottenere un valore specifico dell'array cookie**
```php
$request->cookie('name');
```
Se l'array cookie non contiene il valore specificato, restituirà null.

Come il metodo get, possiamo anche passare un valore predefinito come secondo parametro al metodo cookie. Se l'array cookie non contiene il valore corrispondente, verrà restituito il valore predefinito. Ad esempio:
```php
$request->cookie('name', 'tom');
```

## Ottenere tutti gli input
Include l'insieme dei dati `post` e `get`.
```php
$request->all();
```

## Ottenere un valore specifico dell'input
Ottenere un valore specifico dall'insieme dei dati `post` e `get`.
```php
$request->input('name', $default_value);
```

## Ottenere parte dei dati di input
Ottenere parte dei dati `post` e `get` escludendo alcune chiavi.
```php
// Ottenere un array composto da username e password, se la chiave corrispondente non esiste, verrà ignorata
$only = $request->only(['username', 'password']);
// Ottenere tutti gli input escludendo avatar e age
$except = $request->except(['avatar', 'age']);
```

## Ottenere file caricati
**Ottenere l'intero array dei file caricati**
```php
$request->file();
```

Form simile a:
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
È un array di istanze di `webman\Http\UploadFile`. La classe `webman\Http\UploadFile` estende la classe incorporata PHP [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) e fornisce alcuni metodi utili.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Verifica se il file è valido, ad esempio vero|falso
            var_export($spl_file->getUploadExtension()); // Estensione del file caricato, ad esempio 'jpg'
            var_export($spl_file->getUploadMimeType()); // Tipo mime del file caricato, ad esempio 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Ottieni il codice di errore del caricamento, ad esempio UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Nome del file caricato, ad esempio 'my-test.jpg'
            var_export($spl_file->getSize()); // Ottieni la dimensione del file, ad esempio 13364, in byte
            var_export($spl_file->getPath()); // Ottieni il percorso di caricamento, ad esempio '/tmp'
            var_export($spl_file->getRealPath()); // Ottieni il percorso del file temporaneo, ad esempio `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Nota:**

- Dopo il caricamento, i file vengono rinominati in un file temporaneo, ad esempio `/tmp/workerman.upload.SRliMu`
- La dimensione massima del file caricato è limitata da [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), che di default è di 10 MB ma può essere modificata in `config/server.php` cambiando il valore di `max_package_size`.
- Una volta terminata la richiesta, il file temporaneo verrà automaticamente eliminato
- Se la richiesta non contiene file caricati, `$request->file()` restituisce un array vuoto
- Il metodo `move_uploaded_file()` non supporta il caricamento di file. Si prega di utilizzare il metodo `$file->move()` come mostrato nell'esempio seguente

### Ottenere un file di caricamento specifico
```php
$request->file('avatar');
```
Se il file esiste, restituirà un'istanza di `webman\Http\UploadFile` corrispondente al file. In caso contrario, restituirà null.

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
Ottieni le informazioni sull'host della richiesta.
```php
$request->host();
```
Se l'indirizzo della richiesta non è sulla porta 80 o 443, le informazioni sull'host potrebbero includere la porta, ad esempio `example.com:8080`. Se non è necessaria la porta, è possibile passare `true` come primo parametro.

```php
$request->host(true);
```

## Ottenere il metodo della richiesta
```php
$request->method();
```
Il valore restituito potrebbe essere uno tra `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Ottenere l'URI della richiesta
```php
$request->uri();
```
Restituisce l'URI della richiesta, includendo la parte del percorso e della query string.

## Ottenere il percorso della richiesta
```php
$request->path();
```
Restituisce la parte del percorso della richiesta.

## Ottenere la stringa di query della richiesta
```php
$request->queryString();
```
Restituisce la stringa di query della richiesta.

## Ottenere l'URL della richiesta
Il metodo `url()` restituisce l'URL senza il parametro `Query`.
```php
$request->url();
```
Restituisce qualcosa come `//www.workerman.net/workerman-chat`.

Il metodo `fullUrl()` restituisce l'URL con il parametro `Query`.
```php
$request->fullUrl();
```
Restituisce qualcosa come `//www.workerman.net/workerman-chat?type=download`.

> **NOTA**
> `url()` e `fullUrl()` non restituiscono la parte del protocollo (non restituiscono http o https). Poiché nei browser l'uso di indirizzi che iniziano con `//` riconosce automaticamente il protocollo del sito corrente, avvia automaticamente la richiesta con http o https.

Se si utilizza un proxy nginx, è necessario aggiungere `proxy_set_header X-Forwarded-Proto $scheme;` alla configurazione di nginx, [vedi proxy nginx](others/nginx-proxy.md), in questo modo è possibile verificare se si tratta di http o https utilizzando `$request->header('x-forwarded-proto');`, ad esempio:
```php
echo $request->header('x-forwarded-proto'); // restituisce http o https
```


## Ottenere la versione del protocollo della richiesta
```php
$request->protocolVersion();
```
Restituisce una stringa `1.1` o `1.0`.

## Ottenere l'ID di sessione della richiesta
```php
$request->sessionId();
```
Restituisce una stringa composta da lettere e numeri.

## Ottenere l'IP del client della richiesta
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
Quando si utilizza un proxy (ad esempio nginx), utilizzando `$request->getRemoteIp()` si otterrà spesso l'IP del server proxy (simile a `127.0.0.1` `192.168.x.x`) invece dell'IP reale del client. In questo caso è possibile provare a ottenere l'IP reale del client utilizzando `$request->getRealIp()`.

`$request->getRealIp()` cercherà di ottenere l'IP reale dal campo `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via` degli header HTTP.

> Poiché gli header HTTP sono facilmente falsificabili, l'IP del client ottenuto da questo metodo non è al 100% affidabile, specialmente quando `$safe_mode` è impostato su `false`. Un metodo più attendibile per ottenere l'IP reale del client attraverso un proxy consiste nell'utilizzare un server proxy noto come sicuro e sapere con certezza quale header HTTP porta l'IP reale. Se l'IP restituito da `$request->getRemoteIp()` è confermato essere quello di un server proxy noto come sicuro, è possibile ottenere l'IP reale utilizzando `$request->header('header contenente l\'IP reale')`.

## Ottenere l'IP del server della richiesta
```php
$request->getLocalIp();
```

## Ottenere la porta del server della richiesta
```php
$request->getLocalPort();
```

## Verificare se è una richiesta Ajax
```php
$request->isAjax();
```

## Verificare se è una richiesta Pjax
```php
$request->isPjax();
```

## Verificare se si prevede una risposta in formato JSON
```php
$request->expectsJson();
```

## Verificare se il client accetta una risposta in formato JSON
```php
$request->acceptJson();
```

## Ottenere il nome del plugin della richiesta
Per le richieste non relative ai plugin restituisce una stringa vuota `''`.
```php
$request->plugin;
```
> Questa funzionalità richiede webman>=1.4.0

## Ottenere il nome dell'applicazione della richiesta
Per singola applicazione restituisce sempre una stringa vuota `''`, [per più applicazioni](multiapp.md) restituisce il nome dell'applicazione.
```php
$request->app;
```

> Poiché le funzioni di chiusura non appartengono a nessuna applicazione, pertanto le richieste dalle rotte di chiusura restituiranno sempre una stringa vuota `''`.
> Vedi le rotte delle chiusure [Qui](route.md)

## Ottenere il nome della classe del controller della richiesta
Ottieni il nome della classe del controller corrispondente.
```php
$request->controller;
```
Restituisce qualcosa come `app\controller\IndexController`.

> Poiché le funzioni di chiusura non appartengono a nessun controller, pertanto le richieste dalle rotte di chiusura restituiranno sempre una stringa vuota `''`.
> Vedi le rotte delle chiusure [Qui](route.md)

## Ottenere il nome del metodo della richiesta
Ottieni il nome del metodo del controller corrispondente alla richiesta.
```php
$request->action;
```
Restituisce qualcosa come `index`.

> Poiché le funzioni di chiusura non appartengono a nessun controller, pertanto le richieste dalle rotte di chiusura restituiranno sempre una stringa vuota `''`.
> Vedi le rotte delle chiusure [Qui](route.md)
