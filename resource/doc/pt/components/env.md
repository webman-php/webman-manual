# vlucas/phpdotenv

## Introdução
`vlucas/phpdotenv` é um componente de carregamento de variáveis de ambiente usado para diferenciar as configurações em diferentes ambientes, como ambiente de desenvolvimento, ambiente de teste, etc.

## Endereço do Projeto
https://github.com/vlucas/phpdotenv

## Instalação
```php
composer require vlucas/phpdotenv
```

## Uso

#### Criando um arquivo `.env` no diretório raiz do projeto
**.env**
```plaintext
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Alterando o arquivo de configuração
**config/database.php**
```php
return [
    // Banco de dados padrão
    'default' => 'mysql',

    // Configurações para vários bancos de dados
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **Dica**
> Recomenda-se adicionar o arquivo `.env` à lista do `.gitignore` para evitar a submissão ao repositório de código. Adicione um arquivo de exemplo de configuração `.env.example` ao repositório, e ao implantar o projeto, copie o `.env.example` como `.env` e modifique as configurações no `.env` de acordo com o ambiente atual. Isso permite que o projeto carregue configurações diferentes em ambientes diferentes.

> **Observação**
> `vlucas/phpdotenv` pode ter bugs na versão TS do PHP (versão Thread Safe), por favor, use a versão NTS (versão não Thread Safe).
> Para verificar a versão atual do PHP, execute `php -v`.

## Mais Informações

Visite https://github.com/vlucas/phpdotenv
