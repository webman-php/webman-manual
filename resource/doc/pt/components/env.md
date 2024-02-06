# webman

## Introdução
`vlucas/phpdotenv` é um componente de carregamento de variáveis de ambiente usado para diferenciar as configurações de diferentes ambientes, como ambiente de desenvolvimento, ambiente de teste, etc.

## Endereço do Projeto
https://github.com/vlucas/phpdotenv

## Instalação
```php
composer require vlucas/phpdotenv
```

## Uso

#### Criar arquivo `.env` na raiz do projeto
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Modificar arquivo de configuração
**config/database.php**
```php
return [
    // Banco de dados padrão
    'default' => 'mysql',

    // Diversas configurações de banco de dados
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

> **Nota**
> É recomendável adicionar o arquivo `.env` à lista `.gitignore` para evitar o envio ao repositório de código. Adicione um arquivo de configuração de exemplo `.env.example` ao repositório. Ao implantar o projeto, copie `.env.example` para `.env` e modifique as configurações em `.env` de acordo com o ambiente atual, permitindo assim que o projeto carregue configurações diferentes em ambientes diferentes.

> **Observação**
> `vlucas/phpdotenv` pode ter bugs na versão PHP TS (versão com threads seguros). Portanto, recomenda-se usar a versão NTS (versão não segura para threads).
> A versão atual do PHP pode ser verificada executando `php -v` 

## Mais Informações
Visite https://github.com/vlucas/phpdotenv
