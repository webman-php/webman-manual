# Multilinguagem

O uso de multilinguagem é feito com o componente [symfony/translation](https://github.com/symfony/translation).

## Instalação
```composer require symfony/translation```

## Criação de pacote de idiomas
Por padrão, o webman armazena o pacote de idiomas na diretoria `resource/translations` (se não existir, crie-a), se precisar de alterar o diretório, defina-o no arquivo `config/translation.php`.
Cada idioma corresponde a uma subpasta, e as definições de idioma são normalmente armazenadas no arquivo `messages.php`. Exemplo:
```shell
resource/
└── translations
    ├── en
    │   └── messages.php
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
    // Idioma de fallback, se a tradução não for encontrada no idioma atual, tenta usar a tradução do idioma de fallback
    'fallback_locale' => ['zh_CN', 'en'],
    // Diretório de armazenamento dos arquivos de idioma
    'path' => base_path() . '/resource/translations',
];
```

## Tradução

A tradução é feita utilizando o método `trans()`.

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

Acesse `http://127.0.0.1:8787/user/get` para retornar "你好 世界!"

## Alterar idioma padrão

Para alterar o idioma, utilize o método `locale()`.

Adicione o arquivo de idioma `resource/translations/en/messages.php` da seguinte forma:
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
        // Alterar idioma
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
Acesse `http://127.0.0.1:8787/user/get` para retornar "hello world!"

Também é possível utilizar o quarto parâmetro da função `trans()` para alterar temporariamente o idioma, como nos exemplos acima e abaixo:
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

## Definir idioma de forma explícita para cada solicitação
A tradução é um singleton, o que significa que todas as solicitações compartilham essa instância. Se uma solicitação definir o idioma padrão usando `locale()`, isso afetará todas as solicitações subsequentes nesse processo. Portanto, é necessário definir o idioma de forma explícita para cada solicitação. Por exemplo, utilizando o middleware a seguir:

Crie o arquivo `app/middleware/Lang.php` (crie-o se não existir) da seguinte forma:
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

Adicione o middleware global em `config/middleware.php`:
```php
return [
    // Middleware global
    '' => [
        // ... outros middlewares
        app\middleware\Lang::class,
    ]
];
```

## Uso de espaços reservados
Às vezes, uma mensagem contém variáveis que precisam ser traduzidas, por exemplo
```php
trans('hello ' . $name);
```
Nesses casos, utilize espaços reservados para lidar com eles.

Altere `resource/translations/zh_CN/messages.php` da seguinte forma:
```php
return [
    'hello' => '你好 %name%!',
];
```
Ao traduzir, forneça os valores correspondentes aos espaços reservados através do segundo parâmetro da função `trans()`.
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## Manuseio de plurais
Em alguns idiomas, a forma de expressar uma frase pode variar conforme a quantidade de itens, por exemplo `There is %count% apple`. Quando `%count%` é 1, a frase está correta, mas acima de 1, está incorreta.

Nesses casos, utilize o **pipe** (`|`) para listar as formas plurais.

Adicione a chave `apple_count` ao arquivo de idioma `resource/translations/en/messages.php` da seguinte forma:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

É possível até especificar um intervalo de números, criando regras plurais mais complexas:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Especificar o arquivo de idioma
O arquivo de idioma tem como nome padrão `messages.php`, mas na verdade é possível criar arquivos com outros nomes.

Crie o arquivo de idioma `resource/translations/zh_CN/admin.php` da seguinte forma:
```php
return [
    'hello_admin' => '你好 管理员!',
];
```

Use o terceiro parâmetro da função `trans()` para especificar o arquivo de idioma (sem o sufixo `.php`).
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## Mais Informações
Consulte o manual [symfony/translation](https://symfony.com/doc/current/translation.html)
