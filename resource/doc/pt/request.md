# Explicação

## Obtendo o objeto de requisição
Webman automaticamente injetará o objeto de requisição no primeiro parâmetro do método de ação, por exemplo:

**Exemplo**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obter o parâmetro 'name' da requisição GET, se não for passado, retorna $default_name
        $name = $request->get('name', $default_name);
        // Retornar uma string para o navegador
        return response('hello ' . $name);
    }
}
```

Com o objeto `$request`, podemos obter qualquer dado relacionado à requisição.

**Às vezes, queremos obter o objeto `$request` na classe, nesse caso, podemos usar a função auxiliar `request()`**.

## Obtendo o parâmetro de requisição GET
**Obter todo o array GET**
```php
$request->get();
```
Se a requisição não contém parâmetros GET, retorna um array vazio.

**Obter um valor específico do array GET**
```php
$request->get('name');
```
Se o array GET não contém esse valor, retorna null.

Também podemos fornecer um valor padrão como segundo argumento para o método `get`, que será retornado se o valor correspondente não for encontrado no array GET. Por exemplo:
```php
$request->get('name', 'tom');
```

## Obtendo o parâmetro de requisição POST
**Obter todo o array POST**
```php
$request->post();
```
Se a requisição não contém parâmetros POST, retorna um array vazio.

**Obter um valor específico do array POST**
```php
$request->post('name');
```
Se o array POST não contém esse valor, retorna null.

Assim como no método `get`, podemos fornecer um valor padrão como segundo argumento para o método `post`, que será retornado se o valor correspondente não for encontrado no array POST. Por exemplo:
```php
$request->post('name', 'tom');
```

## Obtendo o corpo bruto da requisição POST
```php
$post = $request->rawBody();
```
Essa funcionalidade é semelhante à operação `file_get_contents("php://input");` no `php-fpm`. É útil para obter o corpo bruto da requisição HTTP quando se trata de dados de requisição POST em um formato não `application/x-www-form-urlencoded`.

## Obtendo headers
**Obtendo todo o array headers**
```php
$request->header();
```
Se a requisição não contém headers, retorna um array vazio. Observe que todas as chaves são em letras minúsculas.

**Obtendo um valor específico do array de headers**
```php
$request->header('host');
```
Se o array de headers não contém esse valor, retorna null. Observe que todas as chaves são em letras minúsculas.

Assim como no método `get`, podemos fornecer um valor padrão como segundo argumento para o método `header`, que será retornado se o valor correspondente não for encontrado no array de headers. Por exemplo:
```php
$request->header('host', 'localhost');
```

## Obtendo cookies
**Obtendo todo o array de cookies**
```php
$request->cookie();
```
Se a requisição não contém cookies, retorna um array vazio.

**Obtendo um valor específico do array de cookies**
```php
$request->cookie('name');
```
Se o array de cookies não contém esse valor, retorna null.

Assim como no método `get`, podemos fornecer um valor padrão como segundo argumento para o método `cookie`, que será retornado se o valor correspondente não for encontrado no array de cookies. Por exemplo:
```php
$request->cookie('name', 'tom');
```

## Obtendo todos os inputs
Inclui a coleção `post` e `get`.
```php
$request->all();
```

## Obtendo um valor específico de entrada
Obtém um valor específico da coleção `post` e `get`.
```php
$request->input('name', $default_value);
```

## Obtendo parte dos dados de entrada
Obtém parte dos dados da coleção `post` e `get`.
```php
// Obter um array composto por username e password, ignorando as chaves que não existem
$only = $request->only(['username', 'password']);
// Obter todos os inputs exceto avatar e age
$except = $request->except(['avatar', 'age']);
```

## Obtendo arquivos enviados
**Obtendo todo o array de arquivos enviados**
```php
$request->file();
```

Formulário semelhante a:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

O retorno de `$request->file()` é semelhante a:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```

Este é um array de instâncias da classe `webman\Http\UploadFile`. A classe `webman\Http\UploadFile` herda a classe [`SplFileInfo`](https://www.php.net/manual/pt_BR/class.splfileinfo.php) integrada do PHP e fornece vários métodos úteis.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Verifica se o arquivo é válido, por exemplo, true|false
            var_export($spl_file->getUploadExtension()); // Obtém a extensão do arquivo enviado, por exemplo, 'jpg'
            var_export($spl_file->getUploadMimeType()); // Obtém o tipo MIME do arquivo enviado, por exemplo, 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Obtém o código de erro do envio, por exemplo UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Obtém o nome do arquivo enviado, por exemplo, 'my-test.jpg'
            var_export($spl_file->getSize()); // Obtém o tamanho do arquivo, por exemplo 13364, em bytes
            var_export($spl_file->getPath()); // Obtém o diretório de envio, por exemplo, '/tmp'
            var_export($spl_file->getRealPath()); // Obtém o caminho do arquivo temporário, por exemplo '/tmp/workerman.upload.SRliMu'
        }
        return response('ok');
    }
}
```

**Observações:**

- Após o envio, o arquivo é renomeado para um arquivo temporário, por exemplo, `/tmp/workerman.upload.SRliMu`
- O tamanho do arquivo enviado é limitado por [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), padrão de 10 MB, e pode ser alterado em `config/server.php` modificando `max_package_size`.
- Após a conclusão da requisição, o arquivo temporário será automaticamente removido.
- Se a requisição não contém arquivos enviados, `$request->file()` retorna um array vazio.
- O envio de arquivos não suporta o método `move_uploaded_file()`, deve-se utilizar o método `$file->move()` como substituto, consulte o exemplo abaixo.

### Obtendo um arquivo de envio específico
```php
$request->file('avatar');
```
Se o arquivo existe, retorna uma instância correspondente de `webman\Http\UploadFile`, caso contrário, retorna null.

**Exemplo**
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
## Obter host
Obter informações de host da requisição.
```php
$request->host();
```
Se o endereço da requisição não estiver usando a porta padrão 80 ou 443, as informações de host podem incluir a porta, por exemplo, `example.com:8080`. Se você não precisa da porta, o primeiro argumento pode ser passado como `true`.
```php
$request->host(true);
```

## Obter método da requisição
```php
$request->method();
```
O valor retornado pode ser um dos seguintes: `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Obter URI da requisição
```php
$request->uri();
```
Retorna o URI da requisição, incluindo a parte do caminho e da string de consulta.

## Obter caminho da requisição
```php
$request->path();
```
Retorna a parte do caminho da requisição.

## Obter string de consulta da requisição
```php
$request->queryString();
```
Retorna a parte da string de consulta da requisição.

## Obter URL da requisição
O método `url()` retorna a URL sem os parâmetros de consulta.
```php
$request->url();
```
Retorna algo parecido com `//www.workerman.net/workerman-chat`.

O método `fullUrl()` retorna a URL com os parâmetros de consulta.
```php
$request->fullUrl();
```
Retorna algo parecido com `//www.workerman.net/workerman-chat?type=download`.

> **Nota**
> Os métodos `url()` e `fullUrl()` não retornam a parte do protocolo (não retornam http ou https). Isso ocorre porque ao usar `//example.com` como um endereço que começa com `//`, o navegador automaticamente reconhece o protocolo do site atual e inicia a requisição usando http ou https.

Se você estiver usando um proxy nginx, adicione `proxy_set_header X-Forwarded-Proto $scheme;` à configuração do nginx, [consulte proxy nginx](others/nginx-proxy.md). Desta forma, você pode usar `$request->header('x-forwarded-proto')` para verificar se é http ou https, por exemplo:
```php
echo $request->header('x-forwarded-proto'); // exibe http ou https
```

## Obter versão do protocolo da requisição
```php
$request->protocolVersion();
```
Retorna a string `1.1` ou `1.0`.

## Obter ID da sessão da requisição
```php
$request->sessionId();
```
Retorna uma string composta por letras e números.

## Obter IP do cliente da requisição
```php
$request->getRemoteIp();
```

## Obter porta do cliente da requisição
```php
$request->getRemotePort();
```

## Obter IP real do cliente da requisição
```php
$request->getRealIp($safe_mode=true);
```
Quando o projeto usa um proxy (por exemplo, nginx), o uso de `$request->getRemoteIp()` frequentemente resulta no IP do servidor proxy (como `127.0.0.1` `192.168.x.x`) em vez do IP real do cliente. Nesse caso, você pode tentar usar `$request->getRealIp()` para obter o IP real do cliente.

`$request->getRealIp()` tentará obter o IP real do cliente dos cabeçalhos HTTP `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip` e `via`.

> Como os cabeçalhos HTTP são facilmente falsificáveis, o IP do cliente obtido por este método não é totalmente confiável, especialmente quando `$safe_mode` é falso. Uma maneira mais confiável de obter o IP real do cliente através de um proxy é conhecer o IP seguro do servidor proxy e saber exatamente qual cabeçalho HTTP contém o IP real. Se o IP retornado por `$request->getRemoteIp()` for confirmado como o IP seguro do servidor proxy conhecido, então você pode obter o IP real usando `$request->header('cabeçalho HTTP que contém o IP real')`.

## Obter IP do servidor da requisição
```php
$request->getLocalIp();
```

## Obter porta do servidor da requisição
```php
$request->getLocalPort();
```

## Verificar se é uma requisição Ajax
```php
$request->isAjax();
```

## Verificar se é uma requisição Pjax
```php
$request->isPjax();
```

## Verificar se a requisição espera uma resposta em JSON
```php
$request->expectsJson();
```

## Verificar se o cliente aceita respostas em JSON
```php
$request->acceptJson();
```

## Obter o nome do plugin da requisição
Requisições não ligadas a plugins retornam uma string vazia `''`.
```php
$request->plugin;
```
> Este recurso requer webman>=1.4.0

## Obter o nome da aplicação da requisição
Em um aplicativo único, sempre retorna uma string vazia `''`, [em aplicações múltiplas](multiapp.md) retorna o nome da aplicação.
```php
$request->app;
```
> Como as funções de fechamento não pertencem a nenhuma aplicação, as requisições de rotas de fechamento `$request->app` sempre retornam uma string vazia `''`. Consulte [rotas](route.md) para requisitos de fechamento.

## Obter o nome da classe do controlador da requisição
Obter o nome da classe correspondente ao controlador.
```php
$request->controller;
```
Retorna algo semelhante a `app\controller\IndexController`.

> Como as funções de fechamento não pertencem a nenhum controlador, as requisições de rotas de fechamento `$request->controller` sempre retornam uma string vazia `''`. Consulte [rotas](route.md) para requisitos de fechamento.

## Obter o nome do método da requisição
Obter o nome do método do controlador correspondente à requisição.
```php
$request->action;
```
Retorna algo como `index`.

> Como as funções de fechamento não pertencem a nenhum controlador, as requisições de rotas de fechamento `$request->action` sempre retornam uma string vazia `''`. Consulte [rotas](route.md) para requisitos de fechamento.
