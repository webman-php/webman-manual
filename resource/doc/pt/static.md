# Manipulação de arquivos estáticos

O webman suporta acesso a arquivos estáticos, que são todos armazenados no diretório `public`. Por exemplo, ao acessar `http://127.0.0.8787/upload/avatar.png`, na verdade está acessando `{diretório principal do projeto}/public/upload/avatar.png`.

> **Observação**
> A partir da versão 1.4, o webman suporta plugins de aplicativos. O acesso a arquivos estáticos iniciados com `/app/xx/nome_do_arquivo` na verdade está acessando o diretório `public` do plugin do aplicativo. Ou seja, o webman >=1.4.0 não suporta o acesso a diretórios em `{diretório principal do projeto}/public/app/`.
> Para mais informações, consulte [Plugins de Aplicativos](./plugin/app.md)

### Desativar o suporte a arquivos estáticos
Se não precisar do suporte a arquivos estáticos, abra `config/static.php` e altere a opção `enable` para false. Após desativar, todas as tentativas de acesso a arquivos estáticos resultarão em erro 404.

### Alterar o diretório de arquivos estáticos
Por padrão, o webman usa o diretório public como o diretório de arquivos estáticos. Se desejar alterar, modifique a função de auxílio `public_path()` em `support/helpers.php`.

### Middleware de arquivos estáticos
O webman possui um middleware de arquivos estáticos embutido, localizado em `app/middleware/StaticFile.php`.
Às vezes, é necessário manipular os arquivos estáticos, como adicionar cabeçalhos HTTP de acesso cruzado, ou proibir o acesso a arquivos que começam com ponto (`.`).

O conteúdo de `app/middleware/StaticFile.php` é semelhante ao seguinte:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Proibir o acesso a arquivos ocultos começando com ponto
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Adicionar cabeçalhos HTTP de acesso cruzado
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Se esse middleware for necessário, é preciso ativá-lo na opção `middleware` em `config/static.php`.
