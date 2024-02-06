# Biblioteca de controle de acesso Casbin webman-permission

## Descrição

É baseado no [PHP-Casbin](https://github.com/php-casbin/php-casbin), um framework de controle de acesso de código aberto poderoso e eficiente que suporta modelos de controle de acesso como `ACL`, `RBAC`, `ABAC`, entre outros.

## Endereço do projeto

https://github.com/Tinywan/webman-permission

## Instalação

```php
composer require tinywan/webman-permission
```
> Esta extensão requer PHP 7.1+ e [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998), manual oficial: https://www.workerman.net/doc/webman#/db/others

## Configuração

### Registrar serviço

Crie o arquivo de configuração `config/bootstrap.php` com conteúdo semelhante ao seguinte:

```php
// ...
webman\permission\Permission::class,
```

### Arquivo de configuração do modelo de modelo

Crie o arquivo de configuração `config/casbin-basic-model.conf` com conteúdo semelhante ao seguinte:

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```

### Arquivo de configuração de política

Crie o arquivo de configuração `config/permission.php` com conteúdo semelhante ao seguinte:

```php
<?php

return [
    /*
     * Permissão padrão
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Configuração do modelo
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adaptador.
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Configurações do banco de dados.
            */
            'database' => [
                // Nome da conexão do banco de dados, deixe em branco para a configuração padrão.
                'connection' => '',
                // Nome da tabela de políticas (sem prefixo).
                'rules_name' => 'rule',
                // Nome completo da tabela de políticas.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Começando rapidamente

```php
use webman\permission\Permission;

// adiciona permissões a um usuário
Permission::addPermissionForUser('eve', 'articles', 'read');
// adiciona uma função a um usuário.
Permission::addRoleForUser('eve', 'writer');
// adiciona permissões a uma regra
Permission::addPolicy('writer', 'articles','edit');
```

Você pode verificar se o usuário tem essas permissões

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permite que eve edite artigos
} else {
    // nega a solicitação, mostra um erro
}
````

## Middleware de autorização

Crie o arquivo `app/middleware/AuthorizationMiddleware.php` (se o diretório não existir, crie-o) da seguinte forma:

```php
<?php

/**
 * Middleware de autorização
 * por ShaoBo Wan (Tinywan)
 * 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $uri = $request->path();
        try {
            $userId = 10086;
            $action = $request->method();
            if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
                throw new \Exception('Desculpe, você não tem permissão para acessar esta API');
            }
        } catch (CasbinException $exception) {
            throw new \Exception('Erro de autorização' . $exception->getMessage());
        }
        return $next($request);
    }
}
```

Adicione o middleware global em `config/middleware.php` como a seguir:

```php
return [
    // Middleware global
    '' => [
        // ... outros middlewares omitidos aqui
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Agradecimentos

[Casbin](https://github.com/php-casbin/php-casbin), você pode encontrar toda a documentação em seu [site oficial](https://casbin.org/).

## Licença

Este projeto está licenciado sob a [licença Apache 2.0](LICENSE).
