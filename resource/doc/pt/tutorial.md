# Exemplo Simples

## Retornar uma string
**Criar um controlador**

Crie o arquivo `app/controller/UserController.php` com o seguinte conteúdo:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obter o parâmetro 'name' da requisição GET. Se não houver nenhum parâmetro 'name' passado, retorna $default_name
        $name = $request->get('name', $default_name);
        // Retorna a string para o navegador
        return response('hello ' . $name);
    }
}
```

**Acessando**

Acesse no navegador `http://127.0.0.1:8787/user/hello?name=tom`

O navegador retornará `hello tom`

## Retornar JSON
Altere o arquivo `app/controller/UserController.php` para o seguinte conteúdo:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**Acessando**

Acesse no navegador `http://127.0.0.1:8787/user/hello?name=tom`

O navegador retornará `{"code":0,"msg":"ok","data":"tom"}`

O uso da função auxiliar json para retornar dados automaticamente adicionará o cabeçalho `Content-Type: application/json`

## Retornar XML
Da mesma forma, usando a função auxiliar `xml($xml)` retornará uma resposta `XML` com o cabeçalho `Content-Type: text/xml`.

O parâmetro `$xml` pode ser uma string `XML` ou um objeto `SimpleXMLElement`.

## Retornar JSONP
Da mesma forma, usando a função auxiliar `jsonp($data, $callback_name = 'callback')` retornará uma resposta `JSONP`.

## Retornar uma visualização
Altere o arquivo `app/controller/UserController.php` para o seguinte conteúdo:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

Crie o arquivo `app/view/user/hello.html` com o seguinte conteúdo:

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

Acesse no navegador `http://127.0.0.1:8787/user/hello?name=tom`
Será retornado uma página HTML com o conteúdo `hello tom`.

Observação: O webman usa a sintaxe nativa do PHP como modelo por padrão. Para utilizar outras visualizações, consulte [Visão](view.md).
