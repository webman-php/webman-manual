# Risposta
La risposta è effettivamente un oggetto `support\Response`. Per facilitarne la creazione, webman fornisce alcune funzioni helper.

## Restituire una risposta arbitraria

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

La funzione `response` è implementata come segue:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

È anche possibile creare un oggetto `response` vuoto e poi, in posizioni appropriate, utilizzare i metodi `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` per impostare il contenuto della risposta.
```php
public function hello(Request $request)
{
    // Creare un oggetto
    $response = response();
    
    // .... Logica aziendale omessa
    
    // Impostare il cookie
    $response->cookie('foo', 'valore');
    
    // .... Logica aziendale omessa
    
    // Impostare l'intestazione http
    $response->header('Content-Type', 'applicazione/json');
    $response->withHeaders([
                'X-Header-One' => 'Valore intestazione 1',
                'X-Header-Tow' => 'Valore intestazione 2',
            ]);

    // .... Logica aziendale omessa

    // Impostare i dati da restituire
    $response->withBody('Dati da restituire');
    return $response;
}
```

## Restituire json
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
La funzione `json` è implementata come segue:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'applicazione/json'], json_encode($data, $options));
}
```


## Restituire xml
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
La funzione `xml` è implementata come segue:
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
Creare un file `app/controller/FooController.php` come segue:

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

Creare un file `app/view/foo/hello.html` come segue:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Redirect
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```
La funzione `redirect` è implementata come segue:
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

## Impostare l'intestazione
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
            'X-Header-One' => 'Valore intestazione' 
        ]);
    }
}
```
È possibile utilizzare i metodi `header` e `withHeaders` per impostare un' intestazione singola o multipla.

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
            'X-Header-One' => 'Valore intestazione 1',
            'X-Header-Tow' => 'Valore intestazione 2',
        ]);
    }
}
```
È anche possibile impostare in anticipo l'intestazione e successivamente impostare i dati da restituire.

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
È possibile impostare il cookie in anticipo e successivamente impostare i dati da restituire.

```php
public function hello(Request $request)
{
    // Creare un oggetto
    $response = response();
    
    // .... Logica aziendale omessa
    
    // Impostare il cookie
    $response->cookie('foo', 'valore');
    
    // .... Logica aziendale omessa

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
- Per i file di grandi dimensioni (oltre 2 MB), webman non legge l'intero file in memoria una sola volta, ma legge e invia il file a segmenti al momento opportuno
- webman ottimizza la velocità di lettura e invio del file in base alla velocità con cui il client lo riceve, garantendo di inviare il file più rapidamente possibile riducendo al minimo l'uso della memoria
- L'invio dei dati è non bloccante e non influisce sulle altre richieste in elaborazione
- Il metodo `file` aggiunge automaticamente l'intestazione `if-modified-since` e controlla l'intestazione `if-modified-since` nella richiesta successiva; se il file non è stato modificato, restituisce direttamente lo stato 304 per risparmiare larghezza di banda
- Il file inviato userà automaticamente l'intestazione `Content-Type` appropriata per essere inviato al browser
- Se il file non esiste, viene convertito automaticamente in una risposta 404
## Scarica il file
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'nome_file.ico');
    }
}
```

Il metodo download è essenzialmente simile al metodo file, la differenza è
1. Dopo aver impostato il nome del file da scaricare, il file verrà effettivamente scaricato anziché mostrato nel browser.
2. Il metodo download non controlla l'intestazione `if-modified-since`.


## Ottenere l'output
Alcune librerie stampano direttamente il contenuto del file in standard output, ovvero i dati vengono stampati nel terminale della riga di comando e non inviati al browser. In questo caso è necessario catturare i dati in una variabile utilizzando `ob_start();` `ob_get_clean();`, e successivamente inviarli al browser, per esempio:

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
        imagestring($im, 1, 5, 5,  'Una semplice stringa di testo', $text_color);

        // Iniziare a ottenere l'output
        ob_start();
        // Output dell'immagine
        imagejpeg($im);
        // Ottenere il contenuto dell'immagine
        $image = ob_get_clean();
        
        // Inviare l'immagine
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
