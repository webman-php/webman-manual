# Multilíngue

O multilíngue utiliza o componente [symfony/translation](https://github.com/symfony/translation).

## Instalação
```
composer require symfony/translation
```

## Criação de pacotes de idiomas
Por padrão, o webman coloca os pacotes de idiomas na pasta `resource/translations` (se não existir, crie). Se desejar alterar o diretório, defina-o em `config/translation.php`.
Cada idioma é representado por uma subpasta, e a definição do idioma é colocada por padrão em `messages.php`. Exemplo:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Todos os arquivos de idioma retornam um array, por exemplo:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Configuração
`config/translation.php`

```php
return [
    // Idioma padrão
    'locale' => 'zh_CN',
    // Idioma de fallback, quando a tradução não é encontrada no idioma atual, tenta-se usar a tradução do idioma de fallback
    'fallback_locale' => ['zh_CN', 'en'],
    // Pasta onde os arquivos de idioma são armazenados
    'path' => base_path() . '/resource/translations',
];
```

## Tradução
A tradução utiliza o método `trans()`.

Crie o arquivo de idioma `resource/translations/zh_CN/messages.php` da seguinte forma:
```php
return [
    'hello' => '你好 世界!',
];
```

Crie o arquivo `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // 你好 世界!
        return response($hello);
    }
}
```

Ao acessar `http://127.0.0.1:8787/user/get`, será retornado "你好 世界!"

## Alterar o idioma padrão
Para alterar o idioma, utilize o método `locale()`.

Crie o arquivo de idioma `resource/translations/en/messages.php` da seguinte forma:
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Alterar o idioma
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
Ao acessar `http://127.0.0.1:8787/user/get`, será retornado "hello world!"

Também é possível utilizar o quarto parâmetro da função `trans()` para alterar temporariamente o idioma. Por exemplo, o exemplo acima é equivalente ao seguinte:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // O quarto parâmetro altera o idioma
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Definir o idioma explicitamente para cada solicitação
A tradução é um singleton, o que significa que todas as solicitações compartilham a mesma instância. Portanto, devemos definir explicitamente o idioma para cada solicitação. Por exemplo, usando o middleware a seguir:

Crie o arquivo `app/middleware/Lang.php` (se o diretório não existir, crie) da seguinte forma:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

No arquivo `config/middleware.php`, adicione o middleware global da seguinte forma:
```php
return [
    // Middleware global
    '' => [
        // ... Outros middlewares aqui
        app\middleware\Lang::class,
    ]
];
```


## Utilização de espaços reservados
Às vezes, uma mensagem contém variáveis que precisam ser traduzidas, como por exemplo
```php
trans('hello ' . $name);
```
Nesses casos, usamos espaços reservados para manipular.

Altere `resource/translations/zh_CN/messages.php` da seguinte forma:
```php
return [
    'hello' => '你好 %name%!',
];
```
Ao traduzir, envie os valores dos espaços reservados como segunda argumento
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## Lidar com pluralidade
Alguns idiomas utilizam diferentes formas para a mesma mensagem, dependendo da quantidade de um determinado item, por exemplo `Há %count% maçã`. Quando `%count%` é 1, a mensagem está correta, mas quando é maior que 1, está incorreta.

Nesses casos, utilizamos um **pipe**(`|`) para listar as formas plurais.

Adicione o item `apple_count` no arquivo de idioma `resource/translations/en/messages.php`, da seguinte forma:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Também é possível especificar um intervalo numérico, criando regras plurais mais complexas:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Especificar arquivo de idioma
Por padrão, o arquivo de idioma tem o nome `messages.php`, mas na verdade é possível criar outros arquivos de idioma.

Crie o arquivo de idioma `resource/translations/zh_CN/admin.php` da seguinte forma:
```php
return [
    'hello_admin' => '你好 管理员!',
];
```

Especifique o arquivo de idioma (omitindo a extensão `.php`) usando o terceiro parâmetro da função `trans()`.
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## Mais informações
Consulte o [manual do symfony/translation](https://symfony.com/doc/current/translation.html)
