# Casbin Framework de Controle de Acesso webman-permission

## Descrição

Baseia-se em [PHP-Casbin](https://github.com/php-casbin/php-casbin), uma estrutura de controle de acesso de código aberto poderosa e eficiente, que suporta modelos de controle de acesso como `ACL`, `RBAC`, `ABAC` e outros.

## Endereço do Projeto

https://github.com/Tinywan/webman-permission

## Instalação

```php
composer require tinywan/webman-permission
```
> Esta extensão requer PHP 7.1+ e [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998), Manual oficial: https://www.workerman.net/doc/webman#/db/others

## Configuração

### Registrar Serviço

Crie um arquivo de configuração `config/bootstrap.php` com o conteúdo semelhante a:

```php
// ...
webman\permission\Permission::class,
```

### Arquivo de Configuração do Modelo

Crie um arquivo de configuração `config/casbin-basic-model.conf` com o conteúdo semelhante a:

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

### Arquivo de Configuração de Permissão

Crie um arquivo de configuração `config/permission.php` com o conteúdo semelhante a:

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
            * Configuração do Modelo
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adaptador
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Configuração do Banco de Dados
            */
            'database' => [
                // Nome da conexão com o banco de dados, se não preenchido, usará a configuração padrão.
                'connection' => '',
                // Nome da tabela de políticas (sem prefixo de tabela)
                'rules_name' => 'rule',
                // Nome completo da tabela de políticas.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Início Rápido

```php
use webman\permission\Permission;

// Adiciona permissões a um usuário
Permission::addPermissionForUser('eve', 'articles', 'read');
// Adiciona uma função a um usuário
Permission::addRoleForUser('eve', 'writer');
// Adiciona permissões a uma regra
Permission::addPolicy('writer', 'articles', 'edit');
```

Você pode verificar se o usuário tem essa permissão

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permite que o eve edite artigos
} else {
    // nega a solicitação, mostra um erro
}
````

## Middleware de Autorização

Crie o arquivo `app/middleware/AuthorizationMiddleware.php` (se o diretório não existir, crie) conforme abaixo:

```php
<?php

/**
 * Middleware de Autorização
 * por ShaoBo Wan (Tinywan)
 * em 2021/09/07 14:15
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

Adicione o middleware global em `config/middleware.php` conforme abaixo:

```php
return [
    // Middleware global
    '' => [
        // ... outros middlewares aqui
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Agradecimentos

[Casbin](https://github.com/php-casbin/php-casbin), você pode consultar toda a documentação em seu [site oficial](https://casbin.org/).

## Licença

Este projeto está licenciado sob a [licença Apache 2.0](LICENSE).
