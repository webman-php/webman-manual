# Middlewares
Os middlewares são geralmente usados para interceptar solicitações ou respostas. Por exemplo, realizar a verificação unificada da identidade do usuário antes de executar o controlador, redirecionar para a página de login se o usuário não estiver logado, ou adicionar um cabeçalho específico à resposta. Outro exemplo seria o acompanhamento da proporção de solicitações para uma URI específica, e assim por diante.

## Modelo de Cebola dos Middlewares

```                          
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── Request ───────────────────────> Controller ─ Response ───────────────────────────> Cliente
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```

Os middlewares e os controladores compõem um modelo clássico de cebola, onde os middlewares são como as camadas externas da cebola e o controlador é o núcleo da cebola. Como mostrado no gráfico, a solicitação atravessa os middlewares 1, 2 e 3 para chegar ao controlador. O controlador retorna uma resposta, que então atravessa os middlewares na ordem 3, 2, 1 antes de ser enviada para o cliente. Ou seja, em cada middleware podemos obter tanto a solicitação quanto a resposta.

## Interceptação de Solicitações
Às vezes, não queremos que uma solicitação atinja a camada do controlador. Por exemplo, se descobrirmos, no middleware 2, que o usuário atual não está logado, podemos interceptar a solicitação e retornar uma resposta de login. Nesse caso, o fluxo se parece com o exemplo abaixo:

```                          
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Request ─────────┐     │    │    Controller    │      │      │     │
            │     │ Response │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

Como mostrado no gráfico, a solicitação atinge o middleware 2 e gera uma resposta de login, que atravessa o middleware 1 e retorna para o cliente.

## Interface do Middleware
Os middlewares devem implementar a interface `Webman\MiddlewareInterface`.

```php
interface MiddlewareInterface
{
    /**
     * Processa uma solicitação recebida pelo servidor.
     *
     * Processa uma solicitação recebida pelo servidor para produzir uma resposta.
     * Se não puder produzir a resposta por si só, poderá delegar ao manipulador de solicitações fornecido para fazê-lo.
     */
    public function process(Request $request, callable $handler): Response;
}
```

Isso significa que os middlewares devem implementar o método `process`, que deve retornar um objeto `support\Response`. Por padrão, este objeto é gerado por `$handler($request)` (a solicitação continua a cruzar as camadas da cebola). Também pode ser uma resposta gerada por funções auxiliares como `response()`, `json()`, `xml()` ou `redirect()` (a solicitação para de atravessar as camadas da cebola).
## Obter solicitação e resposta no middleware
No middleware, podemos obter a solicitação e também a resposta após o controle ser executado, portanto, o middleware é dividido em três partes internas.
1. Fase de passagem da solicitação, isto é, antes do processamento da solicitação
2. Fase de processamento do controlador, isto é, durante o processamento da solicitação
3. Fase de saída da resposta, isto é, após o processamento da solicitação

A representação dessas três fases no middleware é a seguinte:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'Esta é a fase de passagem da solicitação, ou seja, antes do processamento da solicitação';
        
        $response = $handler($request); // Continua a passagem até o núcleo da cebola, até que seja executado o controlador e se obtenha a resposta
        
        echo 'Esta é a fase de saída da resposta, ou seja, após o processamento da solicitação';
        
        return $response;
    }
}
```

## Exemplo: Middleware de autenticação
Crie o arquivo `app/middleware/AuthCheckTest.php` (se o diretório não existir, crie-o) da seguinte forma:
```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // Já autenticado, a solicitação continua a passagem
            return $handler($request);
        }

        // Obter os métodos do controlador que não exigem autenticação via reflexão
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // O método acessado requer autenticação
        if (!in_array($request->action, $noNeedLogin)) {
            // Interceptar a solicitação e retornar uma resposta de redirecionamento, parando a passagem da solicitação
            return redirect('/user/login');
        }

        // Não requer autenticação, a solicitação continua a passagem
        return $handler($request);
    }
}
```

Crie o controlador `app/controller/UserController.php` da seguinte forma:
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Métodos que não exigem autenticação
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'login ok']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **Observação**
> `$noNeedLogin` registra os métodos no controlador que podem ser acessados sem autenticação

Adicione o middleware de autenticação global no arquivo `config/middleware.php` da seguinte forma:
```php
return [
    // Middleware global
    '' => [
        // ... outros middlewares
        app\middleware\AuthCheckTest::class,
    ]
];
```

Com o middleware de autenticação, podemos nos concentrar em escrever o código de negócios na camada do controlador, sem se preocupar se o usuário está autenticado.

## Exemplo: Middleware de solicitação de recursos
Crie o arquivo `app/middleware/AccessControlTest.php` (se o diretório não existir, crie-o) da seguinte forma:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // Se for uma solicitação de opções, retornar uma resposta vazia; caso contrário, continuar a passagem e obter uma resposta
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Adicionar cabeçalhos http relacionados com o controle de recursos à resposta
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

> **Nota**
> As solicitações de recursos podem gerar solicitações de opções. Não queremos que as solicitações de opções cheguem ao controlador, portanto, retornamos uma resposta vazia (`response('')`) para interceptar a solicitação. Se a sua interface requer configuração de rota, use `Route::any(..)` or `Route::add(['POST', 'OPTIONS'], ..)` para configurar.

Adicione o middleware de solicitação de recursos global no arquivo `config/middleware.php` da seguinte forma:
```php
return [
    // Middleware global
    '' => [
        // ... outros middlewares
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Observação**
> Se a solicitação ajax personalizou cabeçalhos, é necessário adicionar este cabeçalho personalizado ao campo `Access-Control-Allow-Headers` do middleware. Caso contrário, ocorrerá o erro `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

## Observações
  
 - O middleware é dividido em middleware global, middleware da aplicação (o middleware da aplicação só é válido no modo de várias aplicações, consulte [Múltiplas Aplicações](multiapp.md)) e middleware de rota
 - Atualmente não é compatível com middleware de um único controlador (mas é possível implementar algo semelhante ao middleware do controlador por meio do julgamento de `$request->controller` no middleware)
 - O arquivo de configuração do middleware está localizado em `config/middleware.php`
 - A configuração do middleware global está sob a chave `''`
 - A configuração do middleware da aplicação está sob o nome da aplicação específica, por exemplo:

```php
return [
    // Middleware global
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware da aplicação "api" (o middleware da aplicação só é válido no modo de várias aplicações)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware de rota

Podemos configurar um ou um grupo de rotas com middleware específico.
Por exemplo, adicione a seguinte configuração no arquivo `config/route.php`:
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```
## Construtor de Middleware com Parâmetros

> **Observação**
> Esta função requer webman-framework >= 1.4.8

A partir da versão 1.4.8, o arquivo de configuração suporta a instanciação direta de middleware ou funções anônimas. Isso permite passar parâmetros para o middleware por meio de construtores.
Por exemplo, a configuração em `config/middleware.php` também pode ser feita da seguinte maneira:

```php
return [
    // Middleware global
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware da aplicação API (somente válido no modo de múltiplas aplicações)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Da mesma forma, os middlewares de rotas também podem passar parâmetros por meio do construtor, como mostrado em `config/route.php`:

```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Ordem de Execução do Middleware

- A ordem de execução do middleware é `middleware global` -> `middleware da aplicação` -> `middleware de rota`.
- Com vários middlewares globais, a execução segue a ordem de configuração dos middlewares (o mesmo vale para os middlewares de aplicação e de rota).
- Requisições 404 não acionam nenhum middleware, incluindo os middlewares globais.

## Passagem de Parâmetros para o Middleware (route->setParams)

**Configuração de Rota `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (supondo ser um middleware global)**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // Como o $request->route padrão é null, é necessário verificar se está vazio
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Passagem de Parâmetros do Middleware para o Controlador

Às vezes, o controlador precisa usar dados gerados pelo middleware, nesse caso, podemos passar parâmetros para o controlador adicionando propriedades ao objeto `$request`. Por exemplo:

**Middleware**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**Controlador:**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## Middleware Obtendo Informações de Rota Atual da Requisição

> **Observação**
> Requer webman-framework >= 1.3.2

Podemos usar `$request->route` para obter o objeto de rota, e chamando os métodos correspondentes, obter as informações desejadas.

**Configuração de Rota**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Middleware**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // Se a requisição não corresponder a nenhuma rota (exceto a rota padrão), $request->route será null.
        // Supondo que o navegador acesse o endereço /user/111, o seguinte será mostrado
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **Observação**
> O método `$route->param()` requer webman-framework >= 1.3.16

## Middleware Obtendo Exceções

> **Observação**
> Requer webman-framework >= 1.3.15

Durante o processamento de uma requisição, pode ocorrer uma exceção. No middleware, podemos usar `$response->exception()` para obtê-la.

**Configuração de Rota**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**Middleware:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## Middleware Global

> **Observação**
> Esta função requer webman-framework >= 1.5.16

Os middlewares globais do projeto principal afetam apenas o projeto principal e não têm impacto nos [aplicativos de plugin](app/app.md). Às vezes, podemos querer adicionar um middleware que afete todos os plug-ins, incluindo o projeto principal. Nesse caso, podemos usar o middleware global. No arquivo `config/middleware.php`, a configuração seria a seguinte:

```php
return [
    '@' => [ // Adiciona um middleware global ao projeto principal e a todos os plug-ins
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Adiciona um middleware global apenas ao projeto principal
];
```

> **Observação**
> O middleware global `@` pode ser configurado não apenas no projeto principal, mas também em algum plug-in. Por exemplo, configurando o middleware global `@` em um arquivo `plugin/ai/config/middleware.php`, ele afetaria o projeto principal e todos os plug-ins.

## Adição de Middleware a um Plugin Específico

> **Observação**
> Esta função requer webman-framework >= 1.5.16

Às vezes, queremos adicionar um middleware a um [aplicativo de plugin](app/app.md), sem modificar o código do próprio plug-in (uma vez que as alterações seriam substituídas durante uma atualização). Nesse caso, podemos configurar o middleware no projeto principal.

No arquivo `config/middleware.php`, a configuração para adicionar middleware a um plug-in específico seria a seguinte:

```php
return [
    'plugin.ai' => [], // Adiciona um middleware ao plug-in ai
    'plugin.ai.admin' => [], // Adiciona um middleware ao módulo admin do plug-in ai
];
```

> **Observação**
> Da mesma forma, também é possível configurar um arquivo semelhante em um plug-in para afetar outros plug-ins. Por exemplo, adicionando essa configuração a um arquivo `plugin/foo/config/middleware.php`, ela afetaria o plug-in ai.
