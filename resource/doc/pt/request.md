# Descrição

## Obtendo o objeto de requisição
O webman injetará automaticamente o objeto de requisição no primeiro parâmetro do método de ação, por exemplo:


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
        // Obtém o parâmetro "name" da requisição GET; se não houver parâmetro "name", retorna $default_name
        $name = $request->get('name', $default_name);
        // Retorna uma string para o navegador
        return response('olá ' . $name);
    }
}
```

Através do objeto `$request`, podemos obter qualquer dado relacionado à requisição.

**Às vezes, queremos obter o objeto `$request` na classe de outro lugar. Neste caso, só precisamos usar a função auxiliar `request()`**;

## Obtendo os parâmetros da requisição GET

**Obtendo todo o array GET**
```php
$request->get();
```
Se a requisição não tiver parâmetros GET, este método retornará um array vazio.

**Obtendo um valor específico do array GET**
```php
$request->get('name');
```
Se o array GET não contiver esse valor, este método retornará null.

Você também pode passar um valor padrão como segundo argumento para o método `get`. Se o valor correspondente não for encontrado no array GET, o método retornará o valor padrão. Por exemplo:
```php
$request->get('name', 'tom');
```

## Obtendo os parâmetros da requisição POST
**Obtendo todo o array POST**
```php
$request->post();
```
Se a requisição não tiver parâmetros POST, este método retornará um array vazio.

**Obtendo um valor específico do array POST**
```php
$request->post('name');
```
Se o array POST não contiver esse valor, este método retornará null.

Assim como o método `get`, você também pode passar um valor padrão como segundo argumento para o método `post`. Se o valor correspondente não for encontrado no array POST, o método retornará o valor padrão. Por exemplo:
```php
$request->post('name', 'tom');
```

## Obtendo o corpo bruto da requisição POST
```php
$post = $request->rawBody();
```
Esta função é semelhante à operação `file_get_contents("php://input");` no `php-fpm` e é útil para obter o corpo bruto da requisição HTTP quando se trata de dados de requisição POST em um formato não `application/x-www-form-urlencoded`.

## Obtendo o cabeçalho da requisição
**Obtendo todo o array de cabeçalhos**
```php
$request->header();
```
Se a requisição não tiver cabeçalhos, este método retornará um array vazio. Note que todas as chaves são em letras minúsculas.

**Obtendo um valor específico do array de cabeçalhos**
```php
$request->header('host');
```
Se o array de cabeçalhos não contiver esse valor, este método retornará null. Note que todas as chaves são em letras minúsculas.

Assim como o método `get`, você também pode passar um valor padrão como segundo argumento para o método `header`. Se o valor correspondente não for encontrado no array de cabeçalhos, o método retornará o valor padrão. Por exemplo:
```php
$request->header('host', 'localhost');
```

## Obtendo os cookies da requisição
**Obtendo todo o array de cookies**
```php
$request->cookie();
```
Se a requisição não tiver cookies, este método retornará um array vazio.

**Obtendo um valor específico do array de cookies**
```php
$request->cookie('name');
```
Se o array de cookies não contiver esse valor, este método retornará null.

Assim como o método `get`, você também pode passar um valor padrão como segundo argumento para o método `cookie`. Se o valor correspondente não for encontrado no array de cookies, o método retornará o valor padrão. Por exemplo:
```php
$request->cookie('name', 'tom');
```

## Obtendo todas as entradas
Inclui a coleção `post` e `get`.
```php
$request->all();
```

## Obtendo um valor específico de entrada
Obtém um valor específico da coleção `post` e `get`.
```php
$request->input('name', $default_value);
```

## Obtendo dados de entrada parciais
Obtém dados parciais da coleção `post` e `get`.
```php
// Obtém um array composto por username e password, ignorando chaves que não existem
$only = $request->only(['username', 'password']);
// Obtém todas as entradas exceto avatar e age
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

`$request->file()` retornará algo semelhante a:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Este é um array de instâncias `webman\Http\UploadFile`. A classe `webman\Http\UploadFile` estende a classe [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) interna do PHP e fornece alguns métodos úteis.

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
            var_export($spl_file->getUploadMimeType()); // Obtém o tipo MIME do arquivo enviado, por exemplo 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Obtém o código de erro de envio, por exemplo, UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Obtém o nome do arquivo enviado, por exemplo 'my-test.jpg'
            var_export($spl_file->getSize()); // Retorna o tamanho do arquivo, por exemplo 13364, em bytes
            var_export($spl_file->getPath()); // Obtém o diretório de envio, por exemplo '/tmp'
            var_export($spl_file->getRealPath()); // Obtém o caminho do arquivo temporário, por exemplo `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Observações:**

- Após o envio, o arquivo será nomeado como um arquivo temporário, por exemplo, `/tmp/workerman.upload.SRliMu`
- O tamanho dos arquivos enviados está sujeito ao limite de [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), que é 10MB por padrão e pode ser alterado para um valor diferente em `config/server.php` ajustando `max_package_size`.
- O arquivo temporário será removido automaticamente após o término da requisição.
- Se a requisição não enviar arquivos, `$request->file()` retornará um array vazio.
- A função `move_uploaded_file()` não é suportada para arquivos enviados; em vez disso, utilize o método `$file->move()` conforme o exemplo abaixo.

### Obtendo um arquivo de envio específico
```php
$request->file('avatar');
```
Se o arquivo existir, este método retornará uma instância de `webman\Http\UploadFile` correspondente. Caso contrário, retornará null.

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
            return json(['code' => 0, 'msg' => 'envio bem-sucedido']);
        }
        return json(['code' => 1, 'msg' => 'arquivo não encontrado']);
    }
}
```

## Obtendo o host
Obtém as informações do host da requisição.
```php
$request->host();
```
Se o endereço da requisição não estiver em uma porta padrão (80 ou 443), as informações do host poderão conter a porta, por exemplo, `example.com:8080`. Se você não deseja a porta, pode passar `true` como primeiro argumento.

```php
$request->host(true);
```

## Obtendo o método da requisição
```php
 $request->method();
```
O valor retornado pode ser `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS` ou `HEAD`.

## Obtendo o URI da requisição
```php
$request->uri();
```
Retorna o URI da requisição, incluindo a parte do caminho e da string de consulta.

## Obtendo o caminho da requisição

```php
$request->path();
```
Retorna a parte do caminho da requisição.

## Obtendo a string de consulta da requisição

```php
$request->queryString();
```
Retorna a string de consulta da requisição.

## Obtendo a URL da requisição
O método `url()` retorna a URL sem os parâmetros de consulta.
```php
$request->url();
```
Retorna algo parecido com `//www.workerman.net/workerman-chat`

O método `fullUrl()` retorna a URL com os parâmetros de consulta.
```php
$request->fullUrl();
```
Retorna algo parecido com `//www.workerman.net/workerman-chat?type=download`

> **Observação**
> `url()` e `fullUrl()` não incluem a parte do protocolo (não incluem `http` ou `https`).
> Isso acontece porque, no navegador, o uso de endereços que começam com `//` automaticamente reconhecerá o protocolo do site atual, iniciando a requisição com `http` ou `https`.

Se você estiver usando um proxy Nginx, adicione `proxy_set_header X-Forwarded-Proto $scheme;` à configuração do Nginx, [consulte o proxy Nginx](others/nginx-proxy.md),
assim você poderá usar `$request->header('x-forwarded-proto');` para verificar se é http ou https, por exemplo:
```php
echo $request->header('x-forwarded-proto'); // Saída: http ou https
```

## Obtendo a versão HTTP da requisição

```php
$request->protocolVersion();
```
Retorna a string `1.1` ou `1.0`.

## Obtendo o ID da sessão da requisição

```php
$request->sessionId();
```
Retorna uma string composta por letras e números.

## Obtendo o IP do cliente da requisição
```php
$request->getRemoteIp();
```

## Obtendo a porta do cliente da requisição
```php
$request->getRemotePort();
```
## Obter o IP real do cliente

```php
$request->getRealIp($safe_mode=true);
```

Quando o projeto utiliza um proxy (como o nginx), o uso de `$request->getRemoteIp()` geralmente resulta no IP do servidor proxy (como `127.0.0.1` `192.168.x.x`) em vez do IP real do cliente. Nesse caso, pode-se tentar usar `$request->getRealIp()` para obter o IP real do cliente.

`$request->getRealIp()` tentará obter o IP real do cliente a partir dos campos `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip` e `via` nos cabeçalhos HTTP.

> Devido à facilidade de falsificação dos cabeçalhos HTTP, o IP do cliente obtido por este método não é 100% confiável, especialmente quando `$safe_mode` é false. Um método mais confiável para obter o IP real do cliente através de um proxy é saber o IP seguro do servidor proxy e estar ciente de qual cabeçalho HTTP carrega o IP real. Se o IP retornado por `$request->getRemoteIp()` for confirmado como o IP seguro do servidor proxy conhecido, então, pode-se obter o IP real através de `$request->header('cabeçalho que carrega o IP real')`.

## Obter o IP do servidor

```php
$request->getLocalIp();
```

## Obter a porta do servidor

```php
$request->getLocalPort();
```

## Verificar se é uma solicitação ajax

```php
$request->isAjax();
```

## Verificar se é uma solicitação pjax

```php
$request->isPjax();
```

## Verificar se espera uma resposta json

```php
$request->expectsJson();
```

## Verificar se o cliente aceita uma resposta json

```php
$request->acceptJson();
```

## Obter o nome do plugin da solicitação
Retorna uma string vazia `''` se não for uma solicitação de plugin.
```php
$request->plugin;
```
> Este recurso requer webman>=1.4.0

## Obter o nome do aplicativo da solicitação
Retorna uma string vazia `''` quando há apenas um aplicativo, e o nome do aplicativo quando há [múltiplos aplicativos](multiapp.md).
```php
$request->app;
```

> Como as funções de fechamento não pertencem a nenhum aplicativo, as solicitações de rota de fechamento sempre retornam uma string vazia `''`. Consulte [Rotas](route.md) para mais informações sobre rotas de fechamento.

## Obter o nome da classe do controlador da solicitação
Obtém o nome da classe correspondente ao controlador.
```php
$request->controller;
```
Retorna algo como `app\controller\IndexController`.

> Como as funções de fechamento não pertencem a nenhum controlador, as solicitações de rota de fechamento sempre retornam uma string vazia `''`. Consulte [Rotas](route.md) para mais informações sobre rotas de fechamento.

## Obter o nome do método da solicitação
Obtém o nome do método do controlador correspondente à solicitação.
```php
$request->action;
```
Retorna algo como `index`.

> Como as funções de fechamento não pertencem a nenhum controlador, as solicitações de rota de fechamento sempre retornam uma string vazia `''`. Consulte [Rotas](route.md) para mais informações sobre rotas de fechamento.

