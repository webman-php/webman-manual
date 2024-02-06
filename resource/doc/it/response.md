# Risposta
La risposta è effettivamente un oggetto `support\Response`, e per facilitarne la creazione, webman fornisce alcune funzioni di supporto.

## Restituire una qualsiasi risposta

**Esempio**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('ciao webman');
    }
}
```

La funzione di implementazione della risposta è la seguente:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

È inoltre possibile creare preventivamente un'istanza vuota di `response` e, in seguito, utilizzare i metodi `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` per impostare il contenuto da restituire nel punto appropriato.
```php
public function hello(Request $request)
{
    // Creare un'istanza
    $response = response();
    
    // .... Logica di business omessa
    
    // Impostare i cookie
    $response->cookie('foo', 'valore');
    
    // .... Logica di business omessa
    
    // Impostare gli header HTTP
    $response->header('Content-Type', 'applicazione/json');
    $response->withHeaders([
                'X-Header-One' => 'Valore Header 1',
                'X-Header-Tow' => 'Valore Header 2',
            ]);

    // .... Logica di business omessa

    // Impostare i dati da restituire
    $response->withBody('Dati da restituire');
    return $response;
}
```

## Restituire JSON
**Esempio**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
L'implementazione della funzione json è la seguente
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'applicazione/json'], json_encode($data, $options));
}
```

## Restituire XML
**Esempio**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
L'implementazione della funzione xml è la seguente:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Restituire una vista
Creare il file `app/controller/FooController.php` come segue

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

Creare il file `app/view/foo/hello.html` come segue
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
ciao <?=htmlspecialchars($name)?>
</body>
</html>
```

## Reindirizzare
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/utente');
    }
}
```

L'implementazione della funzione redirect è la seguente:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## Impostare l'header
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('ciao webman', 200, [
            'Content-Type' => 'applicazione/json',
            'X-Header-One' => 'Valore Header' 
        ]);
    }
}
```
È anche possibile utilizzare i metodi `header` e `withHeaders` per impostare singolarmente o in blocco l'header.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('ciao webman')
        ->header('Content-Type', 'applicazione/json')
        ->withHeaders([
            'X-Header-One' => 'Valore Header 1',
            'X-Header-Tow' => 'Valore Header 2',
        ]);
    }
}
```
È anche possibile impostare preventivamente l'header e in seguito impostare i dati da restituire.
```php
public function hello(Request $request)
{
    // Creare un'istanza
    $response = response();
    
    // .... Logica di business omessa
  
    // Impostare gli header HTTP
    $response->header('Content-Type', 'applicazione/json');
    $response->withHeaders([
                'X-Header-One' => 'Valore Header 1',
                'X-Header-Tow' => 'Valore Header 2',
            ]);

    // .... Logica di business omessa

    // Impostare i dati da restituire
    $response->withBody('Dati da restituire');
    return $response;
}
```

## Impostare il cookie
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('ciao webman')
        ->cookie('foo', 'valore');
    }
}
```

È anche possibile impostare preventivamente il cookie e in seguito impostare i dati da restituire.
```php
public function hello(Request $request)
{
    // Creare un'istanza
    $response = response();
    
    // .... Logica di business omessa
    
    // Impostare il cookie
    $response->cookie('foo', 'valore');
    
    // .... Logica di business omessa

    // Impostare i dati da restituire
    $response->withBody('Dati da restituire');
    return $response;
}
```

I parametri completi del metodo cookie sono i seguenti:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Restituire un flusso di file
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- webman supporta l'invio di file di grandi dimensioni
- Per i file di grandi dimensioni (oltre 2 MB), webman non legge l'intero file in memoria in una sola volta, ma legge e invia il file a blocchi quando opportuno
- webman ottimizza la velocità di lettura e invio del file in base alla velocità di ricezione del client, garantendo il più rapido invio del file minimizzando al contempo l'utilizzo della memoria
- L'invio dei dati è non bloccante e non influisce sulla gestione delle altre richieste
- Il metodo file aggiunge automaticamente l'header 'if-modified-since' e, nella richiesta successiva, verifica l'header 'if-modified-since'. Se il file non è stato modificato, viene restituito direttamente il codice di stato 304 per risparmiare larghezza di banda
- Il file inviato automaticamente utilizza l'header 'Content-Type' appropriato per essere inviato al browser
- Se il file non esiste, viene automaticamente convertito in una risposta 404

## Scaricare un file
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'nomefile.ico');
    }
}
```
Il metodo download è sostanzialmente simile al metodo file, la differenza è che
1. impostato il nome del file da scaricare, il file viene effettivamente scaricato invece di essere visualizzato nel browser
2. il metodo download non verifica l'header 'if-modified-since'

## Ottenere l'output
Alcune librerie classi stampando direttamente il contenuto del file sull'output standard, cioè i dati verranno stampati sul terminale della riga di comando e non inviati al browser. In questo caso, è necessario catturare i dati in una variabile con `ob_start();` `ob_get_clean();` e quindi inviarli al browser, ad esempio:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Creare un'immagine
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'Una stringa di testo semplice', $text_color);

        // Inizio catturare l'output
        ob_start();
        // Output dell'immagine
        imagejpeg($im);
        // Ottenere il contenuto dell'immagine
        $image = ob_get_clean();
        
        // Inviare l'immagine
        return response($image)->header('Content-Type', 'immagine/jpeg');
    }
}
```
