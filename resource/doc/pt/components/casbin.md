# Casbin

## Descrição

Casbin é um poderoso e eficiente framework de controle de acesso de código aberto, cujo mecanismo de gerenciamento de permissões suporta vários modelos de controle de acesso.

## Endereço do projeto

https://github.com/teamones-open/casbin

## Instalação

```php
composer require teamones/casbin
```

## Site oficinal do Casbin

Para detalhes de uso, consulte a documentação oficial em chinês. Aqui, vamos discutir como configurar e usar no webman.

https://casbin.org/docs/zh-CN/overview

## Estrutura do diretório

```text
.
├── config                        Diretório de configuração
│   ├── casbin-restful-model.conf Arquivo de configuração do modelo de permissões usado
│   ├── casbin.php                Configuração do casbin
......
├── database                      Arquivos de banco de dados
│   ├── migrations                Arquivos de migração
│   │   └── 20210218074218_create_rule_table.php
......
```

## Arquivos de migração do banco de dados

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Método de alteração.
     *
     * Escreva suas migrações reversíveis usando este método.
     *
     * Mais informações sobre a escrita de migrações estão disponíveis aqui:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * Os comandos a seguir podem ser usados ​​neste método e o Phinx irá
     * revertê-los automaticamente ao reverter:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Quaisquer outras alterações destrutivas resultarão em erro ao tentar
     * reverter a migração.
     *
     * Lembre-se de chamar "create ()" ou "update ()" e NÃO "save ()" ao trabalhar
     * com a classe Table.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Tabela de regras']);

        //Adiciona campos de dados
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID principal'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Tipo de regra'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //Executar a criação
        $table->create();
    }
}

```

## Configuração do casbin

Para a sintaxe de configuração do modelo de regra de permissão, consulte: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Arquivo de configuração do modelo de regra de permissão
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelo ou adaptador
            'class' => \app\model\Rule::class,
        ],
    ],
    // Você pode configurar vários modelos de permissões
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Arquivo de configuração do modelo de regra de permissão
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelo ou adaptador
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adapter

O adaptador encapsulado no composer atual é o método do modelo think-orm, para outros ORM, consulte vendor/teamones/src/adapters/DatabaseAdapter.php

Então, altere a configuração

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Arquivo de configuração do modelo de regra de permissão
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // aqui tipo configurado como modo adaptador
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Instruções de uso

### Importação

```php
# Importação
use teamones\casbin\Enforcer;
```

### Dois tipos de uso

```php
# 1. Usar o padrão de configuração default
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. Usar o rbac configuração personalizada
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Introdução aos APIs comuns

Para mais usos de API, consulte o site oficial

- API de gerenciamento: https://casbin.org/docs/zh-CN/management-api
- API de RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# Adiciona permissão para usuário

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Exclui permissão de um usuário

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Obtém todas as permissões do usuário

Enforcer::getPermissionsForUser('user1');

# Adiciona função para usuário

Enforcer::addRoleForUser('user1', 'role1');

# Adiciona permissão para função

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Obtém todas as funções

Enforcer::getAllRoles();

# Obtém todas as funções do usuário

Enforcer::getRolesForUser('user1');

# Obtém usuários com base na função

Enforcer::getUsersForRole('role1');

# Verifica se o usuário pertence a uma função

Enforcer::hasRoleForUser('use1', 'role1');

# Exclui função do usuário

Enforcer::deleteRoleForUser('use1', 'role1');

# Exclui todas as funções do usuário

Enforcer::deleteRolesForUser('use1');

# Exclui função

Enforcer::deleteRole('role1');

# Exclui permissão

Enforcer::deletePermission('/user', 'read');

# Exclui todas as permissões do usuário ou função

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Verifica permissão, retorna true ou false

Enforcer::enforce("user1", "/user", "edit");
```
