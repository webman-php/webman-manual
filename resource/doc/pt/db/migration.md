# Ferramenta de migração de banco de dados Phinx

## Descrição

O Phinx permite que os desenvolvedores modifiquem e mantenham o banco de dados de maneira concisa. Ele evita a escrita manual de declarações SQL, utilizando uma poderosa API em PHP para gerenciar migrações de banco de dados. Os desenvolvedores podem usar o controle de versão para gerenciar suas migrações de banco de dados. O Phinx facilita a migração de dados entre diferentes bancos de dados e pode rastrear quais scripts de migração foram executados, permitindo que os desenvolvedores se preocupem menos com o estado do banco de dados e foquem em como escrever sistemas melhores.

## Endereço do projeto

https://github.com/cakephp/phinx

## Instalação

```php
composer require robmorgan/phinx
```

## Documentação oficial em chinês

Para detalhes sobre o uso, consulte a documentação oficial em chinês. Aqui, apenas explicaremos como configurar e usar no webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Estrutura de diretórios de arquivos de migração

```plaintext
.
├── app                           Diretório da aplicação
│   ├── controller                Diretório de controladores
│   │   └── Index.php             Controlador
│   ├── model                     Diretório de modelos
......
├── database                      Arquivos do banco de dados
│   ├── migrations                Arquivos de migração
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Dados de teste
│   │   └── UserSeeder.php
......
```

## Configuração phinx.php

Crie o arquivo phinx.php na raiz do projeto

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## Sugestões de uso

Uma vez que os arquivos de migração são mesclados no código, não é permitido modificá-los novamente. Se ocorrer um problema, será necessário criar um novo arquivo de modificação ou exclusão para resolver o problema.

#### Convenção de nomenclatura de arquivos de operações de criação de tabelas

`{tempo (criado automaticamente)}_create_{nome da tabela em minúsculas em inglês}`

#### Convenção de nomenclatura de arquivos de operações de modificação de tabelas

`{tempo (criado automaticamente)}_modify_{nome da tabela em minúsculas em inglês + item de modificação específico em minúsculas em inglês}`

#### Convenção de nomenclatura de arquivos de operações de exclusão de tabelas

`{tempo (criado automaticamente)}_delete_{nome da tabela em minúsculas em inglês + item de modificação específico em minúsculas em inglês}`

#### Convenção de nomenclatura de arquivos de preenchimento de dados

`{tempo (criado automaticamente)}_fill_{nome da tabela em minúsculas em inglês + item de modificação específico em minúsculas em inglês}`
