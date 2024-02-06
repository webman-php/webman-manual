# Ferramenta de migração de banco de dados Phinx

## Descrição

O Phinx permite que os desenvolvedores modifiquem e mantenham o banco de dados de forma concisa. Ele evita a escrita manual de instruções SQL, utilizando uma poderosa API em PHP para gerenciar a migração do banco de dados. Os desenvolvedores podem versionar suas migrações de banco de dados. O Phinx facilita a migração de dados entre diferentes bancos de dados. Também é possível rastrear quais scripts de migração foram executados, permitindo aos desenvolvedores se concentrarem em escrever sistemas melhores sem se preocupar com o estado do banco de dados.

## Endereço do projeto

https://github.com/cakephp/phinx

## Instalação

```php
composer require robmorgan/phinx
```

## Documentação oficial em chinês

Para detalhes de uso, consulte a documentação oficial em chinês. Aqui discutiremos apenas como configurar e usar o Phinx no webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Estrutura de diretórios de arquivos de migração

```
.
├── app                           Diretório da aplicação
│   ├── controller                Diretório dos controladores
│   │   └── Index.php             Controlador
│   ├── model                     Diretório dos modelos
......
├── database                      Arquivos de banco de dados
│   ├── migrations                Arquivos de migração
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Dados de teste
│   │   └── UserSeeder.php
......
```

## Configuração no arquivo phinx.php

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

Uma vez mesclados os arquivos de migração, não é permitido fazer modificações adicionais. Em caso de problemas, é necessário criar ou excluir um novo arquivo de operação.

#### Regras de nomeação de arquivos de operações de criação de tabelas

`{tempo (criado automaticamente)}_create_{nome da tabela em letras minúsculas em inglês}`

#### Regras de nomeação de arquivos de operações de modificação de tabelas

`{tempo (criado automaticamente)}_modify_{nome da tabela em letras minúsculas em inglês + item de modificação específico em letras minúsculas em inglês}`

### Regras de nomeação de arquivos de operações de exclusão de tabelas

`{tempo (criado automaticamente)}_delete_{nome da tabela em letras minúsculas em inglês + item de exclusão específico em letras minúsculas em inglês}`

### Regras de nomeação de arquivos de preenchimento de dados

`{tempo (criado automaticamente)}_fill_{nome da tabela em letras minúsculas em inglês + item de preenchimento específico em letras minúsculas em inglês}`
