# Plugins de aplicativos
Cada plugin de aplicativo é um aplicativo completo, com o código-fonte localizado no diretório `{diretório_principal}/plugin`

> **Dica**
> Usando o comando `php webman app-plugin:create {nome_do_plugin}` (necessário webman/console>=1.2.16) é possível criar um plugin de aplicativo localmente.
> Por exemplo, `php webman app-plugin:create cms` criará a seguinte estrutura de diretório

```
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

Notamos que um plugin de aplicativo possui a mesma estrutura de diretório e arquivos de configuração do webman. Na realidade, o desenvolvimento de um plugin de aplicativo é basicamente a mesma experiência que desenvolver um projeto webman, apenas prestando atenção a alguns aspectos.

## Namespace
O diretório e a nomenclatura do plugin seguem a especificação PSR4. Como os plugins são armazenados no diretório do plugin, os namespaces começam com plugin. Por exemplo, `plugin\cms\app\controller\UserController`, onde cms é o diretório principal do código-fonte do plugin.

## Acesso a URL
Os caminhos de endereço URL do plugin de aplicativo começam com `/app`, por exemplo, o endereço URL do `plugin\cms\app\controller\UserController` é `http://127.0.0.1:8787/app/cms/user`.

## Arquivos estáticos
Os arquivos estáticos estão localizados em `plugin/{plugin}/public`. Por exemplo, acessar `http://127.0.0.1:8787/app/cms/avatar.png` na verdade está obtendo o arquivo `plugin/cms/public/avatar.png`.

## Arquivos de configuração
As configurações do plugin são semelhantes às de um projeto webman normal, mas as configurações do plugin geralmente afetam apenas o plugin atual e não o projeto principal.
Por exemplo, o valor de `plugin.cms.app.controller_suffix` afeta apenas o sufixo do controlador do plugin, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.app.controller_reuse` afeta apenas se o controlador do plugin é reutilizado, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.middleware` afeta apenas os middleware do plugin, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.view` afeta apenas as visualizações usadas pelo plugin, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.container` afeta apenas o contêiner usado pelo plugin, sem afetar o projeto principal.
Por exemplo, o valor de `plugin.cms.exception` afeta apenas a classe de tratamento de exceções do plugin, sem afetar o projeto principal.

No entanto, como as rotas são globais, as configurações de rotas de plugins também afetam globalmente.

## Obtenção de configuração
A maneira de obter a configuração de um determinado plugin é `config('plugin.{plugin}.{configuração_específica}')`. Por exemplo, para obter todas as configurações de `plugin/cms/config/app.php`, a maneira é `config('plugin.cms.app')`.
Da mesma forma, o projeto principal ou outros plugins podem usar `config('plugin.cms.xxx')` para obter a configuração do plugin cms.

## Configurações não suportadas
Os plugins de aplicativos não oferecem suporte para as configurações server.php e session.php, nem oferecem suporte para as configurações `app.request_class`, `app.public_path` e `app.runtime_path`.

## Banco de dados
Os plugins podem configurar seu próprio banco de dados, por exemplo, o conteúdo de `plugin/cms/config/database.php` é o seguinte
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'banco_de_dados',
            'username'    => 'nome_de_usuário',
            'password'    => 'senha',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'banco_de_dados',
            'username'    => 'nome_de_usuário',
            'password'    => 'senha',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
A maneira de usá-lo é `Db::connection('plugin.{plugin}.{nome_de_conexão}');`, por exemplo
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Se desejar usar o banco de dados principal, simplesmente o use, por exemplo
```php
use support\Db;
Db::table('user')->first();
// Supondo que o projeto principal também tenha uma conexão chamada admin
Db::connection('admin')->table('admin')->first();
```

> **Dica**
> O thinkorm tem um uso semelhante.

## Redis
O uso do Redis é semelhante ao do banco de dados, por exemplo, `plugin/cms/config/redis.php`
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
Ao utilizá-lo
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('chave');
Redis::connection('plugin.cms.cache')->get('chave');
```

Da mesma forma, se desejar reutilizar a configuração do Redis do projeto principal
```php
use support\Redis;
Redis::get('chave');
// Supondo que o projeto principal também tenha uma conexão chamada cache
Redis::connection('cache')->get('chave');
```

## Log
O uso da classe de log é semelhante ao uso do banco de dados
```php
use support\Log;
Log::channel('plugin.admin.default')->info('teste');
```

Se desejar reutilizar a configuração de log do projeto principal, use diretamente
```php
use support\Log;
Log::info('conteúdo_do_log');
// Supondo que o projeto principal tenha uma configuração de log chamada teste
Log::channel('teste')->info('conteúdo_do_log');
```

# Instalação e desinstalação do plugin de aplicativo
Para instalar um plugin de aplicativo, basta copiar o diretório do plugin para o diretório `{diretório_principal}/plugin`, e será necessário recarregar ou reiniciar para que as mudanças entrem em vigor.
Para desinstalar, basta excluir o diretório do plugin correspondente em `{diretório_principal}/plugin`.
