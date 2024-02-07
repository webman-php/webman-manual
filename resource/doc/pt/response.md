# Resposta
A resposta é na verdade um objeto `support\Response`. Para facilitar a criação desse objeto, o webman fornece algumas funções assistentes.

## Retornar uma resposta arbitrária

**Exemplo**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

A função response é implementada da seguinte forma:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Também é possível criar um objeto vazio de resposta e, em seguida, utilizar os métodos `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` para configurar o conteúdo a ser retornado em lugares apropriados.
```php
public function hello(Request $request)
{
    // Criar um objeto
    $response = response();
    
    // .... Lógica de negócios omitida
    
    // Configurar cookie
    $response->cookie('foo', 'valor');
    
    // .... Lógica de negócios omitida
    
    // Configurar cabeçalho http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valor do Cabeçalho 1',
                'X-Header-Dois' => 'Valor do Cabeçalho 2',
            ]);

    // .... Lógica de negócios omitida

    // Configurar os dados a serem retornados
    $response->withBody('Dados a serem retornados');
    return $response;
}
```

## Retornar JSON
**Exemplo**
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
A função json é implementada da seguinte forma:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## Retornar XML
**Exemplo**
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
A função xml é implementada da seguinte forma:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Retornar uma visualização
Crie o arquivo `app/controller/FooController.php` como mostrado abaixo:

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
Crie o arquivo `app/view/foo/hello.html` como mostrado abaixo:

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

## Redirecionar
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
A função redirect é implementada da seguinte forma:
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

## Configuração de cabeçalho
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Valor do Cabeçalho' 
        ]);
    }
}
```
Também é possível configurar um único cabeçalho ou vários cabeçalhos usando os métodos `header` e `withHeaders`.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Valor do Cabeçalho 1',
            'X-Header-Dois' => 'Valor do Cabeçalho 2',
        ]);
    }
}
```
Também é possível configurar cabeçalhos antecipadamente e, em seguida, configurar os dados a serem retornados por último.
```php
public function hello(Request $request)
{
    // Criar um objeto
    $response = response();
    
    // .... Lógica de negócios omitida
  
    // Configurar cabeçalho http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valor do Cabeçalho 1',
                'X-Header-Dois' => 'Valor do Cabeçalho 2',
            ]);

    // .... Lógica de negócios omitida

    // Configurar os dados a serem retornados
    $response->withBody('Dados a serem retornados');
    return $response;
}
```

## Configuração de cookie
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'valor');
    }
}
```
Também é possível configurar cookies antecipadamente e, em seguida, configurar os dados a serem retornados por último.
```php
public function hello(Request $request)
{
    // Criar um objeto
    $response = response();
    
    // .... Lógica de negócios omitida
    
    // Configurar cookie
    $response->cookie('foo', 'valor');
    
    // .... Lógica de negócios omitida

    // Configurar os dados a serem retornados
    $response->withBody('Dados a serem retornados');
    return $response;
}
```
O método cookie possui os seguintes parâmetros completos:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Retornar stream de arquivo
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

- O webman suporta o envio de arquivos extremamente grandes
- Para arquivos grandes (maiores que 2M), o webman não carrega o arquivo inteiro na memória de uma só vez, ao invés disso, ele lê e envia o arquivo em segmentos no momento apropriado
- O webman otimiza a velocidade de leitura e envio de arquivos de acordo com a velocidade de recebimento do cliente, garantindo o envio mais rápido do arquivo com a menor ocupação de memória
- O envio de dados é não bloqueante e não afeta o processamento de outras requisições
- O método file adiciona automaticamente o cabeçalho `if-modified-since` e, na próxima requisição, verifica o cabeçalho `if-modified-since`. Se o arquivo não foi modificado, ele retorna diretamente o código 304, economizando largura de banda
- Os arquivos enviados automaticamente utilizam o cabeçalho `Content-Type` apropriado para serem enviados ao navegador
- Se o arquivo não existir, ele é automaticamente convertido em uma resposta 404
## Downloading Files
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'filename.ico');
    }
}
```

O método `download` é semelhante ao método `file`, com a diferença de que:
1. Após definir o nome do arquivo para download, o arquivo será baixado em vez de ser exibido no navegador.
2. O método `download` não verifica o cabeçalho `if-modified-since`.


## Obtendo Saída
Alguns bibliotecas podem imprimir diretamente o conteúdo do arquivo na saída padrão, ou seja, os dados são impressos no terminal de linha de comando e não são enviados para o navegador. Nesses casos, precisamos capturar os dados em uma variável usando `ob_start();` e `ob_get_clean();`, e então enviar os dados para o navegador. Por exemplo:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Criar imagem
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // Iniciar a captura de saída
        ob_start();
        // Imprimir a imagem
        imagejpeg($im);
        // Obter o conteúdo da imagem
        $image = ob_get_clean();
        
        // Enviar a imagem
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
