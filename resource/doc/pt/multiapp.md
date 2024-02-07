# Múltiplos Aplicativos
Às vezes, um projeto pode ser dividido em vários subprojetos, como por exemplo, uma loja pode ser dividida em um projeto principal da loja, uma interface de API da loja e um painel de administração da loja, todos eles usando a mesma configuração de banco de dados.

O webman permite planejar o diretório do aplicativo da seguinte forma:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Quando você acessa o endereço `http://127.0.0.1:8787/shop/{controller}/{action}`, você está acessando o controlador e a ação no diretório `app/shop/controller`.

Quando você acessa o endereço `http://127.0.0.1:8787/api/{controller}/{action}`, você está acessando o controlador e a ação no diretório `app/api/controller`.

Quando você acessa o endereço `http://127.0.0.1:8787/admin/{controller}/{action}`, você está acessando o controlador e a ação no diretório `app/admin/controller`.

No webman, você até pode planejar o diretório do aplicativo da seguinte forma.
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Assim, quando você acessa o endereço `http://127.0.0.1:8787/{controller}/{action}`, você está acessando o controlador e a ação no diretório `app/controller`. Quando o caminho começa com api ou admin, você está acessando o controlador e a ação nos diretórios correspondentes.

Para aplicativos múltiplos, os namespaces das classes devem seguir o `PSR-4`. Por exemplo, o arquivo `app/api/controller/FooController.php` seria semelhante ao seguinte:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Configuração de Middleware para Múltiplos Aplicativos
Às vezes, você pode querer configurar middleware diferentes para aplicativos diferentes, por exemplo, o aplicativo `api` pode precisar de um middleware de controle de acesso cruzado, e `admin` pode precisar de um middleware para verificar o login do administrador. Nesse caso, a configuração do arquivo `config/midlleware.php` pode se parecer com o exemplo a seguir:
```php
return [
    // Middleware global
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware do aplicativo api
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware do aplicativo admin
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Os middlewares mencionados acima podem não existir, este é apenas um exemplo de como configurar middleware para aplicativos diferentes.

A ordem de execução do middleware é `middleware global` -> `middleware do aplicativo`.

Para desenvolvimento de middlewares, consulte o [capítulo de middlewares](middleware.md).

## Configuração de Tratamento de Exceção para Múltiplos Aplicativos
Da mesma forma, você pode querer configurar classes de tratamento de exceção diferentes para aplicativos diferentes, por exemplo, quando ocorrer uma exceção no aplicativo `shop`, talvez você queira fornecer uma página amigável de aviso; quando ocorrer uma exceção no aplicativo `api`, talvez você queira retornar não uma página, mas uma string JSON. A configuração do arquivo `config/exception.php` para configurar classes de tratamento de exceção diferentes para diferentes aplicativos pode se parecer com o exemplo a seguir:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Ao contrário dos middlewares, cada aplicativo pode configurar apenas uma classe de tratamento de exceção.

> As classes de tratamento de exceção mencionadas acima podem não existir, este é apenas um exemplo de como configurar o tratamento de exceção para diferentes aplicativos.

Para o desenvolvimento de tratamento de exceção, consulte o [capítulo de tratamento de exceções](exception.md).
