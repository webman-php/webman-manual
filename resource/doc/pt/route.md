## Rotas
## Regras de roteamento padrão
A regra de roteamento padrão do webman é `http://127.0.0.1:8787/{controlador}/{ação}`.

O controlador padrão é `app\controller\IndexController` e a ação padrão é `index`.

Por exemplo, ao acessar:
- `http://127.0.0.1:8787` irá acessar por padrão o método `index` da classe `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` irá acessar por padrão o método `index` da classe `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` irá acessar por padrão o método `test` da classe `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` irá acessar por padrão o método `test` da classe `app\admin\controller\FooController` (consulte [Multiaplicação](multiapp.md))

Além disso, a partir da versão 1.4, o webman suporta regras de roteamento padrão mais complexas, por exemplo
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

Ao querer alterar o roteamento de uma solicitação, altere o arquivo de configuração `config/route.php`.

Se você deseja desativar o roteamento padrão, adicione a seguinte configuração na última linha do arquivo de configuração `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Rota fechada
Adicione o seguinte código de rota ao arquivo `config/route.php`
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Nota**
> Como a função fechada não pertence a nenhum controlador, `$request->app`, `$request->controller` e `$request->action` serão todos strings vazias.

Ao acessar o endereço `http://127.0.0.1:8787/test`, será retornado o string "test".

> **Nota**
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

## Rota da classe
Adicione o seguinte código de rota ao arquivo `config/route.php`
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Ao acessar o endereço `http://127.0.0.1:8787/testclass`, será retornado o valor de retorno do método `test` da classe `app\controller\IndexController`.

## Parâmetros de rota
Se houver parâmetros na rota, use `{chave}` para corresponder, e o resultado da correspondência será passado para os parâmetros do método do controlador (a partir do segundo parâmetro em diante), por exemplo:
```php
// Correspondência com /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Recebido o parâmetro'.$id);
    }
}
```

Mais exemplos:
```php
// Correspondência com /user/123, não com /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Correspondência com /user/foobar, não com /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Correspondência com /user /user/123 e /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Corresponder a todas as solicitações de opções
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Grupo de rotas
Às vezes, as rotas contêm um grande número de prefixos semelhantes, nesses casos, podemos usar grupos de rotas para simplificar a definição. Por exemplo:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Equivalentemente
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Uso aninhado de grupos

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

Podemos definir um middleware para uma rota única ou para um grupo de rotas.
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

> **Nota**:
> No webman-framework <= 1.5.6, quando `->middleware()` é aplicado a um grupo após a definição do grupo, a rota atual deve estar dentro desse grupo.

```php
# Exemplo errado (válido a partir do webman-framework >= 1.5.7)
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
    });  
});
```
## Roteamento de Recursos
```php
Route::resource('/test', app\controller\IndexController::class);

// Rota de recurso específico
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Rota de recurso não definida
// Se o endereço de acesso for notify, então qualquer tipo de rota /test/notify ou /test/notify/{id} é possível, com o nome da rota sendo test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```

| Verb   | URI                 | Ação   | Nome da Rota    |
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

> **Observação**
> A geração de URL para rotas aninhadas em grupos não é suportada no momento  

Por exemplo, para a rota:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Podemos usar o seguinte método para gerar a URL dessa rota.
```php
route('blog.view', ['id' => 100]); // Resultado: /blog/100
```

Ao usar este método para URLs de rota em visualizações, a URL será gerada automaticamente, evitando a necessidade de alterar muitos arquivos de visualização devido a alterações nas regras de roteamento.

## Obtendo informações de rota

> **Observação**
> É necessário webman-framework >= 1.3.2

Através do objeto `$request->route`, podemos obter informações sobre a rota atual, por exemplo:

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
> **Observação**
> Se a solicitação atual não corresponder a nenhuma rota configurada em config/route.php, então `$request->route` será nulo, ou seja, ao usar a rota padrão, `$request->route` será nulo.

## Lidando com o erro 404
Quando a rota não é encontrada, o código de status 404 é retornado por padrão e o conteúdo do arquivo `public/404.html` é exibido.

Se o desenvolvedor quiser intervir no fluxo de negócios quando a rota não é encontrada, pode usar o método de fallback de roteamento fornecido pelo webman. Por exemplo, a lógica a seguir redireciona para a página inicial quando a rota não é encontrada.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Por exemplo, quando a rota não existe, retornar dados JSON é muito útil em webman quando é utilizado como uma interface de API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```
Link relacionado [Página de erro personalizada](others/custom-error-page.md)

## Interfaces de Roteamento
```php
// Definir uma rota para qualquer método para $uri
Route::any($uri, $callback);
// Definir uma rota GET para $uri
Route::get($uri, $callback);
// Definir uma rota POST para $uri
Route::post($uri, $callback);
// Definir uma rota PUT para $uri
Route::put($uri, $callback);
// Definir uma rota PATCH para $uri
Route::patch($uri, $callback);
// Definir uma rota DELETE para $uri
Route::delete($uri, $callback);
// Definir uma rota HEAD para $uri
Route::head($uri, $callback);
// Definir várias rotas para tipos de solicitação múltiplos
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Rota de grupo
Route::group($path, $callback);
// Rota de recurso
Route::resource($path, $callback, [$options]);
// Desativar rota padrão
Route::disableDefaultRoute($plugin = '');
// Rota de fallback, definir uma rota padrão padrão
Route::fallback($callback, $plugin = '');
```
Se não houver correspondência de rota para o $uri (incluindo a rota padrão), e fallback não estiver configurado, será retornado o código 404.
## Vários arquivos de configuração de rota
Se você deseja gerenciar o roteamento usando vários arquivos de configuração de rota, por exemplo, em [multiapp](multiapp.md), onde cada aplicação tem seu próprio arquivo de configuração de rota, pode carregar o arquivo de configuração de rota externo usando `require`.
Por exemplo, em `config/route.php`:
```php
<?php

// Carregar o arquivo de configuração de rota da aplicação admin
require_once app_path('admin/config/route.php');
// Carregar o arquivo de configuração de rota da aplicação api
require_once app_path('api/config/route.php');
```
