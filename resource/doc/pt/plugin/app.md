# Plug-in de Aplicativo
Cada plug-in de aplicativo é um aplicativo completo, cujo código-fonte é colocado no diretório `{projeto principal}/plugin`

> **Dica**
> Usando o comando `php webman app-plugin:create {nome_do_plug-in}` (requer webman/console>=1.2.16) é possível criar um plug-in de aplicativo localmente, por exemplo, `php webman app-plugin:create cms` criará a seguinte estrutura de diretório

```plaintext
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Podemos observar que um plug-in de aplicativo possui a mesma estrutura de diretório e arquivos de configuração que o webman. Na verdade, o desenvolvimento de um plug-in de aplicativo é basicamente a mesma experiência que desenvolver um projeto webman, apenas algumas coisas precisam ser observadas.

## Namespace
O diretório e o nome do plug-in seguem a especificação PSR4. Como os plug-ins são colocados no diretório de plugin, o namespace começa com plugin, por exemplo `plugin\cms\app\controller\UserController`, onde cms é o diretório principal de código-fonte do plug-in.

## Acesso à URL
Os caminhos dos endereços URL do plug-in de aplicativo começam com `/app`, por exemplo, o endereço URL de `plugin\cms\app\controller\UserController` é `http://127.0.0.1:8787/app/cms/user`.

## Arquivos estáticos
Os arquivos estáticos são colocados em `plugin/{nome_do_plug-in}/public`, por exemplo, acessar `http://127.0.0.1:8787/app/cms/avatar.png` na verdade está obtendo o arquivo `plugin/cms/public/avatar.png`.

## Arquivos de configuração
As configurações do plug-in são semelhantes às do projeto webman. No entanto, as configurações do plug-in geralmente afetam apenas o próprio plug-in e não o projeto principal.
Por exemplo, o valor de `plugin.cms.app.controller_suffix` afeta apenas o sufixo do controlador do plug-in, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.app.controller_reuse` afeta apenas se o plug-in reutiliza controladores, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.middleware` afeta apenas os middlewares do plug-in, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.view` afeta apenas as visualizações usadas pelo plug-in, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.container` afeta apenas o contêiner usado pelo plug-in, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.exception` afeta apenas a classe de tratamento de exceções do plug-in, sem afetar o projeto principal.

No entanto, porque as rotas são globais, as configurações de rota do plug-in também afetam globalmente.

## Obtenção de configurações
Para obter configurações de um determinado plug-in, use `config('plugin.{nome_do_plug-in}.{configuração_específica}')`, por exemplo, obter todas as configurações em `plugin/cms/config/app.php` usando `config('plugin.cms.app')`.
Da mesma forma, o projeto principal ou outros plug-ins podem usar `config('plugin.cms.xxx')` para obter as configurações do plug-in cms.

## Configurações não suportadas
Os plug-ins de aplicativo não suportam as configurações server.php e session.php, e também não suportam as configurações `app.request_class`, `app.public_path` e `app.runtime_path`.

## Banco de dados
Os plug-ins podem configurar seu próprio banco de dados. Por exemplo, o conteúdo de `plugin/cms/config/database.php` é o seguinte
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'nome_do_banco_de_dados',
            'username'    => 'nome_de_usuário',
            'password'    => 'senha',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'nome_do_banco_de_dados',
            'username'    => 'nome_de_usuário',
            'password'    => 'senha',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
A forma de utilização é `Db::connection('plugin.{nome_do_plug-in}.{nome_da_conexão}');`, por exemplo
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Se deseja usar o banco de dados do projeto principal, basta utilizá-lo diretamente, por exemplo
```php
use support\Db;
Db::table('user')->first();
// Supondo que o projeto principal também configurou uma conexão admin
Db::connection('admin')->table('admin')->first();
```

> **Dica**
> O thinkorm também possui um uso semelhante

## Redis
O uso do Redis é semelhante ao do banco de dados. Por exemplo, `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
Ao utilizar
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('chave');
Redis::connection('plugin.cms.cache')->get('chave');
```

Da mesma forma, caso queira reutilizar a configuração do Redis do projeto principal
```php
use support\Redis;
Redis::get('chave');
// Supondo que o projeto principal também configurou uma conexão cache
Redis::connection('cache')->get('chave');
```

## Log
O uso da classe de log é semelhante ao uso do banco de dados
```php
use support\Log;
Log::channel('plugin.admin.default')->info('teste');
```
Caso queira reutilizar a configuração de log do projeto principal, basta utilizá-la diretamente
```php
use support\Log;
Log::info('conteúdo_do_log');
// Supondo que o projeto principal tenha uma configuração de log chamada teste
Log::channel('teste')->info('conteúdo_do_log');
```

# Instalação e Desinstalação de Plug-ins de Aplicativo
Ao instalar um plug-in de aplicativo, basta copiar o diretório do plug-in para o diretório `{projeto_principal}/plugin` e, em seguida, recarregar ou reiniciar para que o plug-in tenha efeito.
Para desinstalar, basta excluir o diretório do plug-in correspondente em `{projeto_principal}/plugin`.
