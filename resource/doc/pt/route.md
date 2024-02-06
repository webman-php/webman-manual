## Rotas
## Regra de roteamento padrão
A regra de roteamento padrão do webman é `http://127.0.0.1:8787/{controlador}/{ação}`.

O controlador padrão é `app\controller\IndexController`, e a ação padrão é `index`.

Por exemplo, ao acessar:
- `http://127.0.0.1:8787` irá acessar por padrão o método `index` da classe `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` irá acessar por padrão o método `index` da classe `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` irá acessar por padrão o método `test` da classe `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` irá acessar por padrão o método `test` da classe `app\admin\controller\FooController` (consulte [Múltiplas Aplicações](multiapp.md))

Além disso, o webman 1.4 em diante suporta regras de roteamento padrão mais complexas, por exemplo
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

Quando desejar alterar o roteamento de uma solicitação, altere o arquivo de configuração `config/route.php`.

Se deseja desativar o roteamento padrão, adicione a seguinte configuração na última linha do arquivo de configuração `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Roteamento de fechamento
Adicione o seguinte código de roteamento ao arquivo `config/route.php`:
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> ** Observação **
> Como as funções de fechamento não pertencem a nenhum controlador, `$request->app`, `$request->controller`, `$request->action` estão todos vazios.

Quando o endereço for `http://127.0.0.1:8787/test`, será retornado a string `test`.

> ** Observação **
> O caminho da rota deve começar com `/`, por exemplo

```php
// Uso incorreto
Route::any('test', function ($request) {
    return response('test');
});

// Uso correto
Route::any('/test', function ($request) {
    return response('test');
});
```


## Roteamento de classe
Adicione o seguinte código de roteamento ao arquivo `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Quando o endereço for `http://127.0.0.1:8787/testclass`, será retornado o valor de retorno do método `test` da classe `app\controller\IndexController`.


## Parâmetros de roteamento
Se houver parâmetros na rota, use `{chave}` para corresponder, o resultado correspondente será passado para os parâmetros do método do controlador (a partir do segundo parâmetro), por exemplo:
```php
// Corresponde a /user/123 ou /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Parâmetro recebido: '.$id);
    }
}
```

Mais exemplos:
```php
// Corresponde a /user/123, não corresponde a /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Corresponde a /user/foobar, não corresponde a /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Corresponde a /user,  /user/123 e /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Corresponde a todas as solicitações de opções
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Agrupamento de rotas
Às vezes, as rotas contêm um grande número de prefixos semelhantes, nesses casos, podemos usar o agrupamento de rotas para simplificar a definição. Por exemplo:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Equivalente a:
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Uso aninhado de grupo

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## Middleware de roteamento

Podemos definir um middleware para uma rota ou um grupo de rotas.
Por exemplo:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> ** Observação **: 
> Na versão webman-framework <= 1.5.6, a utilização de `->middleware()` em um grupo de middleware, a rota atual deve estar dentro do grupo atual

```php
# Exemplo incorreto (a partir da versão webman-framework >= 1.5.7, esta prática será válida)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Exemplo correto
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## Roteamento de recurso
```php
Route::resource('/test', app\controller\IndexController::class);

// Roteamento de recurso específico
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Roteamento de recurso indefinido
// Se notificar o endereço, será uma rota de qualquer tipo /test/notify ou /test/notify/{id} routeName para teste de notificação
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verbo   | URI                 | Ação   | Nome da rota   |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |




## Geração de URL
> ** Observação ** 
> A geração de URL de roteamento aninhado de grupo não é suportada por enquanto

Por exemplo, com a rota:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Podemos usar o seguinte método para gerar a URL desta rota.
```php
route('blog.view', ['id' => 100]); // Resultado: /blog/100
```

Ao usar a URL da rota em visualizações, podemos utilizar este método para que, independentemente de como a regra de roteamento for alterada, a URL seja gerada automaticamente, evitando a necessidade de alterar um grande número de arquivos de visualização devido a alterações nos endereços de roteamento.


## Obter informações de rota
> ** Observação **
> Necessita do webman-framework >= 1.3.2

Através do objeto `$request->route`, é possível obter informações sobre a rota atual da solicitação, por exemplo

```php
$route = $request->route; // Equivalente a $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Esta funcionalidade requer webman-framework >= 1.3.16
}
```

> ** Observação **
> Se a solicitação atual não corresponder a nenhuma das rotas configuradas em `config/route.php`, então `$request->route` será nulo, ou seja, ao usar a rota padrão, `$request->route` será nulo


## Lidar com 404
Quando a rota não é encontrada, por padrão retorno o código de status 404 e o conteúdo do arquivo `public/404.html` são exibidos.

Se os desenvolvedores desejarem intervir no fluxo de negócios quando a rota não for encontrada, podem utilizar o método de rota de fallback fornecido pelo webman `Route::fallback($callback)`. Por exemplo, a lógica do código a seguir redireciona para a página inicial quando a rota não é encontrada.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Outro exemplo é retornar um JSON quando a rota não é encontrada, o que é muito útil quando o webman é utilizado como uma API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 não encontrado']);
});
```

Links relacionados [Página de erro personalizada](others/custom-error-page.md)
## Interface de Roteamento
```php
// Define uma rota para qualquer método de requisição para $uri
Route::any($uri, $callback);
// Define uma rota de requisição GET para $uri
Route::get($uri, $callback);
// Define uma rota de requisição POST para $uri
Route::post($uri, $callback);
// Define uma rota de requisição PUT para $uri
Route::put($uri, $callback);
// Define uma rota de requisição PATCH para $uri
Route::patch($uri, $callback);
// Define uma rota de requisição DELETE para $uri
Route::delete($uri, $callback);
// Define uma rota de requisição HEAD para $uri
Route::head($uri, $callback);
// Define várias rotas para diferentes tipos de requisição simultaneamente
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Agrupa as rotas
Route::group($path, $callback);
// Rota de recurso
Route::resource($path, $callback, [$options]);
// Desativa a rota padrão
Route::disableDefaultRoute($plugin = '');
// Rota de fallback, define a rota padrão de fallback
Route::fallback($callback, $plugin = '');
```
Se não houver uma rota correspondente para $uri (incluindo a rota padrão) e nenhuma rota de fallback foi definida, será retornado o código de status 404.

## Múltiplos arquivos de configuração de rota
Se você deseja gerenciar as rotas com múltiplos arquivos de configuração, por exemplo, em [aplicações múltiplas](multiapp.md), onde cada aplicação tem seu próprio arquivo de configuração de rota, você pode carregar os arquivos de configuração de rota externos usando `require`.
Por exemplo, em `config/route.php`
```php
<?php

// Carrega o arquivo de configuração de rota da aplicação admin
require_once app_path('admin/config/route.php');
// Carrega o arquivo de configuração de rota da aplicação api
require_once app_path('api/config/route.php');

```
