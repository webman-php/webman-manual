# Resposta
A resposta é na verdade um objeto `support\Response`. Para facilitar a criação deste objeto, o webman fornece algumas funções auxiliares.

## Retornar qualquer resposta

**Exemplo**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('olá, webman');
    }
}
```

A função de resposta é implementada da seguinte forma:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Também é possível criar um objeto `response` vazio e, em seguida, usar `$response->cookie()`, `$response->header()`, `$response->withHeaders()` e `$response->withBody()` para definir o conteúdo a ser retornado em locais apropriados.
```php
public function hello(Request $request)
{
    // Criar um objeto
    $response = response();
    
    // .... Lógica de negócios omitida
    
    // Definir cookie
    $response->cookie('foo', 'valor');
    
    // .... Lógica de negócios omitida
    
    // Definir cabeçalhos HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valor do Cabeçalho 1',
                'X-Header-Dois' => 'Valor do Cabeçalho 2',
            ]);

    // .... Lógica de negócios omitida

    // Definir os dados a serem retornados
    $response->withBody('Dados a serem retornados');
    return $response;
}
```

## Retorna JSON
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
A função `json` é implementada da seguinte maneira:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## Retorna XML
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
A função `xml` é implementada da seguinte maneira:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Retorna visualização
Crie um arquivo `app/controller/FooController.php` como abaixo:

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

Crie um arquivo `app/view/foo/hello.html` como abaixo:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
olá <?=htmlspecialchars($name)?>
</body>
</html>
```

## Redirecionamento
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

A função `redirect` é implementada da seguinte maneira:
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

## Definição de cabeçalho
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('olá, webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Valor do Cabeçalho' 
        ]);
    }
}
```
Também é possível usar os métodos `header` e `withHeaders` para definir cabeçalhos individualmente ou em lote.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('olá, webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Valor do Cabeçalho 1',
            'X-Header-Dois' => 'Valor do Cabeçalho 2',
        ]);
    }
}
```
Também é possível definir os cabeçalhos previamente e, em seguida, definir os dados a serem retornados no final.
```php
public function hello(Request $request)
{
    // Criar um objeto
    $response = response();
    
    // .... Lógica de negócios omitida
  
    // Definir cabeçalhos HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valor do Cabeçalho 1',
                'X-Header-Dois' => 'Valor do Cabeçalho 2',
            ]);

    // .... Lógica de negócios omitida

    // Definir os dados a serem retornados
    $response->withBody('Dados a serem retornados');
    return $response;
}
```

## Definição de cookie

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('olá, webman')
        ->cookie('foo', 'valor');
    }
}
```

Também é possível definir o cookie previamente e, em seguida, definir os dados a serem retornados no final.
```php
public function hello(Request $request)
{
    // Criar um objeto
    $response = response();
    
    // .... Lógica de negócios omitida
    
    // Definir cookie
    $response->cookie('foo', 'valor');
    
    // .... Lógica de negócios omitida

    // Definir os dados a serem retornados
    $response->withBody('Dados a serem retornados');
    return $response;
}
```

Os parâmetros completos do método cookie são os seguintes:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Retorna fluxo de arquivo
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

- O webman suporta o envio de arquivos muito grandes
- Para arquivos grandes (maiores que 2M), o webman não carrega o arquivo inteiro na memória de uma só vez, mas lê e envia o arquivo em segmentos no momento apropriado
- O webman otimiza a velocidade de leitura e envio do arquivo com base na velocidade de recepção do cliente, garantindo o envio mais rápido do arquivo com o menor uso de memória possível
- O envio de dados é não-bloqueante e não afeta o processamento de outras solicitações
- O método `file` adiciona automaticamente o cabeçalho `if-modified-since` e, na próxima solicitação, verifica o cabeçalho `if-modified-since`. Se o arquivo não foi modificado, retorna diretamente o status 304 para economizar largura de banda
- O arquivo enviado utiliza automaticamente o cabeçalho `Content-Type` e é enviado para o navegador
- Se o arquivo não existir, ele é automaticamente convertido em uma resposta 404


## Baixar arquivo
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'nome-do-arquivo.ico');
    }
}
```
O método `download` é semelhante ao método `file`, exceto que:
1. Após definir o nome do arquivo de download, o arquivo será baixado em vez de ser exibido no navegador
2. O método `download` não verifica o cabeçalho `if-modified-since`

## Obtenção da saída
Alguns bibliotecas escrevem o conteúdo do arquivo diretamente na saída padrão, ou seja, os dados são exibidos no terminal da linha de comando e não são enviados para o navegador. Nesse caso, é necessário capturar os dados em uma variável usando `ob_start();` `ob_get_clean();` e, em seguida, enviar os dados para o navegador, como mostrado abaixo:

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
        imagestring($im, 1, 5, 5,  'Uma String de Texto Simples', $text_color);

        // Iniciar a captura da saída
        ob_start();
        // Exibir a imagem
        imagejpeg($im);
        // Obter o conteúdo da imagem
        $image = ob_get_clean();
        
        // Enviar a imagem
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
