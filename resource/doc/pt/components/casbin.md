# Webman

## Introdução

Webman é um framework PHP de alto desempenho baseado no Workerman. Abaixo estão alguns detalhes sobre o uso do Webman.

## Guia do Projeto

https://github.com/teamones-open/casbin

## Instalação

```php
composer require teamones/casbin
```

## Site Oficial do Casbin

Para detalhes sobre o uso, consulte a documentação oficial em chinês. Aqui, vamos apenas explicar como configurar e usar o Casbin no Webman.

https://casbin.org/docs/zh-CN/overview

## Estrutura de Diretórios

```
.
├── config                        Diretório de configuração
│   ├── casbin-restful-model.conf Arquivo de configuração do modelo de permissões utilizado
│   ├── casbin.php                Configuração do casbin
......
├── database                      Arquivos de banco de dados
│   ├── migrations                Arquivos de migração
│   │   └── 20210218074218_create_rule_table.php
......
```

## Arquivos de Migração do Banco de Dados

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Tabela de Regras']);

        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID Principal'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Tipo de Regra'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        $table->create();
    }
}
```

## Configuração do Casbin

Para a sintaxe de configuração do modelo de regras de permissão, consulte: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Arquivo de configuração do modelo de regras de permissão
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelo ou adaptador
            'class' => \app\model\Rule::class,
        ],
    ],
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Arquivo de configuração do modelo de regras de permissão
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelo ou adaptador
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adaptadores

O pacote Composer atualmente suporta o método de modelo do think-orm. Para outros ORMs, consulte vendor/teamones/src/adapters/DatabaseAdapter.php e atualize a configuração conforme necessário.

Depois, atualize a configuração como segue:

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Arquivo de configuração do modelo de regras de permissão
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // defina o tipo como adaptador aqui
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Instruções de Uso

### Importação

```php
# Importação
use teamones\casbin\Enforcer;
```

### Dois Modos de Uso

```php
# 1. Usar a configuração padrão
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Usar uma configuração de rbac personalizada
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### API Comum

Para mais informações sobre os métodos da API, consulte:

- API de Gerenciamento: https://casbin.org/docs/zh-CN/management-api
- API RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# Adicionando permissões para um usuário

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Removendo permissão de um usuário

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Obtendo todas as permissões de um usuário

Enforcer::getPermissionsForUser('user1'); 

# Adicionando funções para um usuário

Enforcer::addRoleForUser('user1', 'role1');

# Adicionando permissões para uma função

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Obtendo todas as funções

Enforcer::getAllRoles();

# Obtendo todas as funções de um usuário

Enforcer::getRolesForUser('user1');

# Obtendo usuários para uma função

Enforcer::getUsersForRole('role1');

# Verificando se um usuário pertence a uma função

Enforcer::hasRoleForUser('use1', 'role1');

# Removendo função de um usuário

Enforcer::deleteRoleForUser('use1', 'role1');

# Removendo todas as funções de um usuário

Enforcer::deleteRolesForUser('use1');

# Remoção de função

Enforcer::deleteRole('role1');

# Remoção de permissão

Enforcer::deletePermission('/user', 'read');

# Remoção de todas as permissões de um usuário ou função

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Verificação de permissão, retornando true ou false

Enforcer::enforce("user1", "/user", "edit");
```
