# Middleware
Os middlewares são geralmente utilizados para interceptar solicitações ou respostas. Por exemplo, realizar a verificação unificada da identidade do usuário antes de executar um controlador, redirecionando para a página de login se o usuário não estiver logado, adicionar um cabeçalho à resposta, ou até mesmo calcular a proporção das solicitações de uma determinada URI, entre outras coisas.

## Modelo de cebola do Middleware

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── Pedido ───────────────────────> Controlador ─ Resposta ───────────────────────────> Cliente
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Os middlewares e os controladores compõem um modelo clássico de cebola, onde os middlewares são como camadas de casca de cebola e o controlador é o centro da cebola. Como mostrado no diagrama, a solicitação passa pelos middlewares 1, 2 e 3 para chegar ao controlador, que então retorna uma resposta. Em seguida, a resposta passa pelos middlewares 3, 2 e 1, para finalmente ser enviada de volta para o cliente. Ou seja, em cada middleware, podemos obter tanto a solicitação quanto a resposta.

## Intercepção de solicitações
Às vezes, não queremos que uma determinada solicitação chegue à camada do controlador. Por exemplo, se um middleware de autenticação verificar que o usuário não está logado, podemos interceptar a solicitação e retornar uma resposta de login. O fluxo seria semelhante ao exemplo a seguir:

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 Middleware de autenticação    │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── Pedido ───────────┐   │     │       Controlador      │     │     │
            │     │ Resposta│     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

Conforme mostrado no diagrama, a solicitação chega ao middleware de autenticação, que gera uma resposta de login. A resposta atravessa de volta o middleware 1 e é devolvida ao navegador.

## Interface do Middleware
Os middlewares devem implementar a interface `Webman\MiddlewareInterface`.
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
Isso significa que os middlewares devem implementar o método `process`, que deve retornar um objeto `support\Response`. Por padrão, esse objeto é gerado por `$handler($request)` (a solicitação continua a atravessar a cebola), mas também pode ser uma resposta gerada por funções auxiliares como `response()`, `json()`, `xml()`, `redirect()` e assim por diante (a solicitação é interrompida, sem percorrer mais a cebola).

## Obtendo solicitações e respostas dentro do Middleware
Dentro do middleware, é possível obter acesso à solicitação e à resposta do controlador. Portanto, o middleware consiste em três partes internas.
1. Fase de atravessamento da solicitação, ou seja, antes do processamento da solicitação.
2. Fase de processamento da solicitação pelo controlador.
3. Fase de retorno da resposta, ou seja, após o processamento da solicitação.

O exemplo a seguir demonstra as três fases dentro do middleware:
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
        echo 'Esta é a fase de atravessamento da solicitação, ou seja, antes do processamento da solicitação.';
        
        $response = $handler($request); // Continua a atravessar a cebola até o executar o controlador e obter a resposta
        
        echo 'Esta é a fase de retorno da resposta, ou seja, após o processamento da solicitação.';
        
        return $response;
    }
}
```
## Exemplo: Middleware de autenticação
Crie o arquivo `app/middleware/AuthCheckTest.php` (crie o diretório se não existir) como mostrado abaixo:
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
            // Já está logado, continue o pedido com a passagem da cebola
            return $handler($request);
        }

        // Obtém os métodos do controlador que não precisam de autenticação usando reflexão
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // O método acessado requer autenticação
        if (!in_array($request->action, $noNeedLogin)) {
            // Interceptar o pedido, retornar uma resposta de redirecionamento e interromper o pedido de passagem da cebola
            return redirect('/user/login');
        }

        // Não precisa de autenticação, continue o pedido com a passagem da cebola
        return $handler($request);
    }
}
```

Crie o controlador `app/controller/UserController.php` como abaixo:
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Métodos que não precisam de autenticação
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
> `$noNeedLogin` contém os métodos do controlador que não requerem autenticação

Adicione o middleware global no arquivo `config/middleware.php` conforme mostrado abaixo:
```php
return [
    // Middleware global
    '' => [
        // ... Outros middlewares aqui
        app\middleware\AuthCheckTest::class,
    ]
];
```

Com o middleware de autenticação, podemos nos concentrar em escrever o código do negócio na camada do controlador sem se preocupar se o usuário está logado ou não.

## Exemplo: Middleware de solicitação de origem cruzada (CORS)
Crie o arquivo `app/middleware/AccessControlTest.php` como mostrado abaixo:
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
        // Se for uma solicitação de opções, retorna uma resposta vazia, caso contrário, continue com a passagem da cebola e obtenha uma resposta
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Adiciona cabeçalhos de HTTP relacionados ao CORS à resposta
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

> **Observação**
> As solicitações CORS podem gerar solicitações OPTIONS. Não queremos que as solicitações OPTIONS atinjam o controlador, por isso retornamos diretamente uma resposta vazia (`response('')`) para interceptar a solicitação.
> Se a sua API precisar de rotas, use `Route::any(..)` ou `Route::add(['POST', 'OPTIONS'], ..)`.

Adicione o middleware global no arquivo `config/middleware.php` como mostrado abaixo:
```php
return [
    // Middleware global
    '' => [
        // ... Outros middlewares aqui
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Observação**
> Se a solicitação AJAX incluir cabeçalhos personalizados, esses cabeçalhos personalizados devem ser adicionados ao campo `Access-Control-Allow-Headers` no middleware, caso contrário, um erro `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` será relatado

## Explicação

- Os middlewares podem ser globais, de aplicativos (funcionam apenas no modo de vários aplicativos, consulte [Múltiplos Aplicativos](multiapp.md)) ou de rota
- Atualmente não há suporte para middlewares de controladores individuais (mas é possível simular a funcionalidade de middlewares de controladores verificando `$request->controller` no middleware)
- O arquivo de configuração de middlewares está localizado em `config/middleware.php`
- A configuração do middleware global está sob a chave `''`
- A configuração do middleware do aplicativo está sob o nome do aplicativo específico, por exemplo

```php
return [
    // Middleware global
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware do aplicativo 'api' (os middlewares de aplicativo só funcionam no modo de vários aplicativos)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middlewares de rota

Podemos atribuir middlewares a uma rota específica ou a um grupo de rotas.
Por exemplo, adicione a seguinte configuração em `config/route.php`:
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

## Passagem de parâmetros para o middleware através da função de construtor (route->setParams)

> **Observação**
> Este recurso requer webman-framework >= 1.4.8

Após a versão 1.4.8, o arquivo de configuração pode instanciar diretamente o middleware ou função anônima, o que facilita a passagem de parâmetros para o middleware por meio do construtor.
Por exemplo, no arquivo `config/middleware.php`, você pode configurar da seguinte forma:
```
return [
    // Middleware global
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware do aplicativo 'api' (os middlewares de aplicativo só funcionam no modo de vários aplicativos)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Da mesma forma, os middlewares de rota também podem passar parâmetros por meio do construtor. Por exemplo, no arquivo `config/route.php`:
```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Ordem de execução dos middlewares

- A ordem de execução dos middlewares é `middleware global`->`middleware de aplicativo`->`middleware de rota`.
- Quando há vários middlewares globais, eles são executados na ordem em que foram configurados (o mesmo aplica-se aos middlewares de aplicativo e de rota).
- As solicitações 404 não acionam nenhum middleware, incluindo os middlewares globais

## Passagem de parâmetros para o controlador através do middleware

Às vezes, o controlador precisa usar os dados gerados no middleware. Nesse caso, podemos passar os parâmetros para o controlador adicionando novas propriedades ao objeto `$request`. Por exemplo:

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


## Middleware para obter informações de rota atual

> **Nota**
> Requer webman-framework >= 1.3.2

Podemos usar `$request->route` para obter o objeto de rota e obter as informações correspondentes chamando os métodos correspondentes.

**Configuração de rota**
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
        // Se a solicitação não corresponder a nenhuma rota (exceto a rota padrão), $request->route será nulo
        // Supondo que o navegador acesse o endereço /user/111, as informações a seguir serão impressas
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

> **Nota**
> O método `$route->param()` requer webman-framework >= 1.3.16

## Middleware para obter exceções

> **Nota**
> Requer webman-framework >= 1.3.15

Durante o processo de tratamento de negócios, podem ocorrer exceções. No middleware, use `$response->exception()` para obter a exceção.

**Configuração de rota**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('teste de exceção');
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

## Middleware global

> **Nota**
> Este recurso requer webman-framework >= 1.5.16

Os middleware globais do projeto principal afetam apenas o projeto principal e não afetam os [plug-ins de aplicativos](app/app.md). Às vezes, queremos adicionar um middleware que afete globalmente todos os plug-ins, neste caso, podemos usar middleware global.

Configure em `config/middleware.php` como a seguir:
```php
return [
    '@' => [ // Adiciona middleware global para o projeto principal e todos os plug-ins
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Apenas adiciona middleware global para o projeto principal
];
```

> **Dica**
> O middleware `@` global não só pode ser configurado no projeto principal, mas também em um determinado plug-in, por exemplo, configurando o middleware `@` global em `plugin/ai/config/middleware.php` também afetará o projeto principal e todos os plug-ins.

## Adicionar middleware a um determinado plug-in

> **Nota**
> Este recurso requer webman-framework >= 1.5.16

Às vezes queremos adicionar um middleware a um [plug-in de aplicativo](app/app.md), mas não queremos alterar o código do plug-in (pois será sobregravado durante uma atualização). Neste caso, podemos configurar o middleware para ele no projeto principal.

Configure em `config/middleware.php` como a seguir:
```php
return [
    'plugin.ai' => [], // Adiciona middleware para o plug-in ai
    'plugin.ai.admin' => [], // Adiciona middleware para o módulo admin do plug-in ai
];
```

> **Dica**
> É claro que também é possível adicionar uma configuração semelhante em um determinado plug-in para afetar outros plug-ins, por exemplo, adicionando a configuração acima em `plugin/foo/config/middleware.php` afetará o plug-in ai.
