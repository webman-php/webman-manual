# Múltiplos Aplicativos

Às vezes, um projeto pode ser dividido em vários subprojetos, por exemplo, uma loja online pode ser dividida em três subprojetos: o projeto principal da loja, a interface de API da loja e o painel de administração da loja, todos eles usando a mesma configuração de banco de dados.

O webman permite que você planeje o diretório de aplicativos da seguinte maneira:
```plaintext
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Ao visitar o endereço `http://127.0.0.1:8787/shop/{controller}/{método}`, você estará acessando os controladores e métodos em `app/shop/controller`.

Ao visitar o endereço `http://127.0.0.1:8787/api/{controller}/{método}`, você estará acessando os controladores e métodos em `app/api/controller`.

Ao visitar o endereço `http://127.0.0.1:8787/admin/{controller}/{método}`, você estará acessando os controladores e métodos em `app/admin/controller`.

No webman, você pode até planejar o diretório do aplicativo da seguinte maneira:
```plaintext
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Dessa forma, quando o endereço for acessado em `http://127.0.0.1:8787/{controller}/{método}`, você estará acessando os controladores e métodos em `app/controller`. Ao iniciar com api ou admin no caminho, será acessado o controlador e os métodos no diretório correspondente.

Para aplicativos múltiplos, os namespaces das classes devem seguir o padrão `psr4`, por exemplo, o arquivo `app/api/controller/FooController.php` terá uma classe semelhante a esta:

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Configuração de middleware de vários aplicativos

Às vezes, você pode desejar configurar diferentes middlewares para aplicativos diferentes, por exemplo, o aplicativo `api` pode precisar de um middleware para lidar com solicitações de domínio cruzado, enquanto o aplicativo `admin` pode precisar de um middleware para verificar o login do administrador. Nesse caso, a configuração de `config/midlleware.php` pode se parecer com o exemplo a seguir:

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
> Os middlewares acima podem não existir, este é apenas um exemplo de como configurar middlewares por aplicativo.

A ordem de execução dos middlewares é `middlewares globais` -> `middlewares do aplicativo`.

Para desenvolvimento de middlewares, consulte o capítulo [middlewares](middleware.md).

## Configuração de tratamento de exceções de vários aplicativos

Da mesma forma, você pode querer configurar classes de tratamento de exceções diferentes para diferentes aplicativos. Por exemplo, se ocorrer uma exceção no aplicativo `shop`, talvez você queira fornecer uma página de erro amigável, enquanto no aplicativo `api`, a exceção resultante não seria uma página, mas sim uma string JSON. A configuração do arquivo `config/exception.php` para configurar classes de tratamento de exceções diferentes para diferentes aplicativos pode se parecer com o exemplo a seguir:

```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Ao contrário dos middlewares, cada aplicativo pode ter apenas uma classe de tratamento de exceção configurada.

> As classes de tratamento de exceções acima podem não existir, este é apenas um exemplo de como configurar o tratamento de exceções por aplicativo.

Para desenvolvimento de tratamento de exceções, consulte o capítulo [tratamento de exceções](exception.md).
