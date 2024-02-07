# Exemplo Simples

## Retornando uma String
**Criando um Controlador**

Crie o arquivo `app/controller/UserController.php` como abaixo

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obter o parâmetro 'name' da solicitação GET. Se nenhum parâmetro 'name' for passado, retornar $default_name.
        $name = $request->get('name', $default_name);
        // Retornar uma string para o navegador
        return response('hello ' . $name);
    }
}
```

**Acessando**

Acesse no navegador `http://127.0.0.1:8787/user/hello?name=tom`

O navegador retornará `hello tom`

## Retornando JSON
Altere o arquivo `app/controller/UserController.php` como abaixo

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

Usando a função auxiliar json para retornar dados, automaticamente é adicionado o cabeçalho `Content-Type: application/json`

## Retornando XML
Da mesma forma, utilizar a função auxiliar `xml($xml)` retornará uma resposta XML com cabeçalho `Content-Type: text/xml`.

O parâmetro `$xml` pode ser uma string XML ou um objeto `SimpleXMLElement`.

## Retornando JSONP
Da mesma forma, utilizar a função auxiliar `jsonp($data, $callback_name = 'callback')` retornará uma resposta JSONP.

## Retornando uma Visão
Altere o arquivo `app/controller/UserController.php` como abaixo

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

Crie o arquivo `app/view/user/hello.html` como abaixo

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
será retornado uma página HTML com o conteúdo `hello tom`.

Nota: Por padrão, o webman utiliza a sintaxe nativa do PHP como modelo. Se desejar usar outras visualizações, consulte [View](view.md).
