# Linha de Comando

Componente de linha de comando do Webman

## Instalação
```
composer require webman/console
```

## Índice

### Geração de Código
- [make:controller](#make-controller) - Gerar classe de controlador
- [make:model](#make-model) - Gerar classe de modelo a partir da tabela do banco de dados
- [make:crud](#make-crud) - Gerar CRUD completo (modelo + controlador + validador)
- [make:middleware](#make-middleware) - Gerar classe de middleware
- [make:command](#make-command) - Gerar classe de comando de console
- [make:bootstrap](#make-bootstrap) - Gerar classe de inicialização bootstrap
- [make:process](#make-process) - Gerar classe de processo personalizado

### Build e Implantação
- [build:phar](#build-phar) - Empacotar projeto como arquivo PHAR
- [build:bin](#build-bin) - Empacotar projeto como binário independente
- [install](#install) - Executar script de instalação do Webman

### Comandos Utilitários
- [version](#version) - Exibir versão do framework Webman
- [fix-disable-functions](#fix-disable-functions) - Corrigir funções desabilitadas no php.ini
- [route:list](#route-list) - Exibir todas as rotas registradas

### Gerenciamento de Plugin de Aplicação (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Criar novo plugin de aplicação
- [app-plugin:install](#app-plugin-install) - Instalar plugin de aplicação
- [app-plugin:uninstall](#app-plugin-uninstall) - Desinstalar plugin de aplicação
- [app-plugin:update](#app-plugin-update) - Atualizar plugin de aplicação
- [app-plugin:zip](#app-plugin-zip) - Empacotar plugin de aplicação como ZIP

### Gerenciamento de Plugins (plugin:*)
- [plugin:create](#plugin-create) - Criar novo plugin Webman
- [plugin:install](#plugin-install) - Instalar plugin Webman
- [plugin:uninstall](#plugin-uninstall) - Desinstalar plugin Webman
- [plugin:enable](#plugin-enable) - Habilitar plugin Webman
- [plugin:disable](#plugin-disable) - Desabilitar plugin Webman
- [plugin:export](#plugin-export) - Exportar código-fonte do plugin

### Gerenciamento de Serviços
- [start](#start) - Iniciar processos worker do Webman
- [stop](#stop) - Parar processos worker do Webman
- [restart](#restart) - Reiniciar processos worker do Webman
- [reload](#reload) - Recarregar código sem interrupção
- [status](#status) - Visualizar status dos processos worker
- [connections](#connections) - Obter informações de conexão dos processos worker

## Geração de Código

<a name="make-controller"></a>
### make:controller

Gerar classe de controlador.

**Uso:**
```bash
php webman make:controller <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do controlador (sem sufixo) |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--plugin` | `-p` | Gerar controlador no diretório do plugin especificado |
| `--path` | `-P` | Caminho personalizado do controlador |
| `--force` | `-f` | Sobrescrever se o arquivo existir |
| `--no-suffix` | | Não adicionar sufixo "Controller" |

**Exemplos:**
```bash
# Criar UserController em app/controller
php webman make:controller User

# Criar em plugin
php webman make:controller AdminUser -p admin

# Caminho personalizado
php webman make:controller User -P app/api/controller

# Sobrescrever arquivo existente
php webman make:controller User -f

# Criar sem sufixo "Controller"
php webman make:controller UserHandler --no-suffix
```

**Estrutura do arquivo gerado:**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**Observações:**
- Os controladores são colocados em `app/controller/` por padrão
- O sufixo do controlador da configuração é adicionado automaticamente
- Solicita confirmação de sobrescrita se o arquivo existir (o mesmo vale para outros comandos)

<a name="make-model"></a>
### make:model

Gerar classe de modelo a partir da tabela do banco de dados. Suporta Laravel ORM e ThinkORM.

**Uso:**
```bash
php webman make:model [name]
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Não | Nome da classe do modelo, pode ser omitido no modo interativo |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--plugin` | `-p` | Gerar modelo no diretório do plugin especificado |
| `--path` | `-P` | Diretório de destino (relativo à raiz do projeto) |
| `--table` | `-t` | Especificar nome da tabela; recomendado quando o nome não segue a convenção |
| `--orm` | `-o` | Escolher ORM: `laravel` ou `thinkorm` |
| `--database` | `-d` | Especificar nome da conexão do banco de dados |
| `--force` | `-f` | Sobrescrever arquivo existente |

**Observações sobre caminhos:**
- Padrão: `app/model/` (aplicação principal) ou `plugin/<plugin>/app/model/` (plugin)
- `--path` é relativo à raiz do projeto, ex.: `plugin/admin/app/model`
- Ao usar `--plugin` e `--path` juntos, devem apontar para o mesmo diretório

**Exemplos:**
```bash
# Criar modelo User em app/model
php webman make:model User

# Especificar nome da tabela e ORM
php webman make:model User -t wa_users -o laravel

# Criar em plugin
php webman make:model AdminUser -p admin

# Caminho personalizado
php webman make:model User -P plugin/admin/app/model
```

**Modo interativo:** Quando o nome é omitido, entra no fluxo interativo: selecionar tabela → informar nome do modelo → informar caminho. Suporta: Enter para ver mais, `0` para criar modelo vazio, `/palavra-chave` para filtrar tabelas.

**Estrutura do arquivo gerado:**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (primary key)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

As anotações `@property` são geradas automaticamente a partir da estrutura da tabela. Suporta MySQL e PostgreSQL.

<a name="make-crud"></a>
### make:crud

Gerar modelo, controlador e validador a partir da tabela do banco de dados de uma só vez, formando capacidade CRUD completa.

**Uso:**
```bash
php webman make:crud
```

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--table` | `-t` | Especificar nome da tabela |
| `--model` | `-m` | Nome da classe do modelo |
| `--model-path` | `-M` | Diretório do modelo (relativo à raiz do projeto) |
| `--controller` | `-c` | Nome da classe do controlador |
| `--controller-path` | `-C` | Diretório do controlador |
| `--validator` | | Nome da classe do validador (requer `webman/validation`) |
| `--validator-path` | | Diretório do validador (requer `webman/validation`) |
| `--plugin` | `-p` | Gerar arquivos no diretório do plugin especificado |
| `--orm` | `-o` | ORM: `laravel` ou `thinkorm` |
| `--database` | `-d` | Nome da conexão do banco de dados |
| `--force` | `-f` | Sobrescrever arquivos existentes |
| `--no-validator` | | Não gerar validador |
| `--no-interaction` | `-n` | Modo não interativo, usar valores padrão |

**Fluxo de execução:** Quando `--table` não é especificado, entra na seleção interativa de tabela; o nome do modelo é inferido do nome da tabela por padrão; o nome do controlador é o nome do modelo + sufixo do controlador; o nome do validador é o nome do controlador sem sufixo + `Validator`. Caminhos padrão: modelo `app/model/`, controlador `app/controller/`, validador `app/validation/`; para plugins: subdiretórios correspondentes em `plugin/<plugin>/app/`.

**Exemplos:**
```bash
# Geração interativa (confirmação passo a passo após seleção da tabela)
php webman make:crud

# Especificar nome da tabela
php webman make:crud --table=users

# Especificar nome da tabela e plugin
php webman make:crud --table=users --plugin=admin

# Especificar caminhos
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Não gerar validador
php webman make:crud --table=users --no-validator

# Não interativo + sobrescrever
php webman make:crud --table=users --no-interaction --force
```

**Estrutura dos arquivos gerados:**

Modelo (`app/model/User.php`):
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

Controlador (`app/controller/UserController.php`):
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

Validador (`app/validation/UserValidator.php`):
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => 'Chave primária',
        'username' => 'Nome de usuário'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Observações:**
- A geração do validador é ignorada se `webman/validation` não estiver instalado ou habilitado (instale com `composer require webman/validation`)
- Os `attributes` do validador são gerados automaticamente a partir dos comentários dos campos do banco; sem comentários não há `attributes`
- As mensagens de erro do validador suportam i18n; o idioma é selecionado de `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

Gerar classe de middleware e registrar automaticamente em `config/middleware.php` (ou `plugin/<plugin>/config/middleware.php` para plugins).

**Uso:**
```bash
php webman make:middleware <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do middleware |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--plugin` | `-p` | Gerar middleware no diretório do plugin especificado |
| `--path` | `-P` | Diretório de destino (relativo à raiz do projeto) |
| `--force` | `-f` | Sobrescrever arquivo existente |

**Exemplos:**
```bash
# Criar middleware Auth em app/middleware
php webman make:middleware Auth

# Criar em plugin
php webman make:middleware Auth -p admin

# Caminho personalizado
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Estrutura do arquivo gerado:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**Observações:**
- Colocado em `app/middleware/` por padrão
- O nome da classe é adicionado automaticamente ao arquivo de configuração do middleware para ativação

<a name="make-command"></a>
### make:command

Gerar classe de comando de console.

**Uso:**
```bash
php webman make:command <command-name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `command-name` | Sim | Nome do comando no formato `group:action` (ex.: `user:list`) |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--plugin` | `-p` | Gerar comando no diretório do plugin especificado |
| `--path` | `-P` | Diretório de destino (relativo à raiz do projeto) |
| `--force` | `-f` | Sobrescrever arquivo existente |

**Exemplos:**
```bash
# Criar comando user:list em app/command
php webman make:command user:list

# Criar em plugin
php webman make:command user:list -p admin

# Caminho personalizado
php webman make:command user:list -P plugin/admin/app/command

# Sobrescrever arquivo existente
php webman make:command user:list -f
```

**Estrutura do arquivo gerado:**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**Observações:**
- Colocado em `app/command/` por padrão

<a name="make-bootstrap"></a>
### make:bootstrap

Gerar classe de inicialização bootstrap. O método `start` é chamado automaticamente quando o processo inicia, geralmente para inicialização global.

**Uso:**
```bash
php webman make:bootstrap <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome da classe Bootstrap |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--plugin` | `-p` | Gerar no diretório do plugin especificado |
| `--path` | `-P` | Diretório de destino (relativo à raiz do projeto) |
| `--force` | `-f` | Sobrescrever arquivo existente |

**Exemplos:**
```bash
# Criar MyBootstrap em app/bootstrap
php webman make:bootstrap MyBootstrap

# Criar sem habilitar automaticamente
php webman make:bootstrap MyBootstrap no

# Criar em plugin
php webman make:bootstrap MyBootstrap -p admin

# Caminho personalizado
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Sobrescrever arquivo existente
php webman make:bootstrap MyBootstrap -f
```

**Estrutura do arquivo gerado:**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**Observações:**
- Colocado em `app/bootstrap/` por padrão
- Ao habilitar, a classe é adicionada a `config/bootstrap.php` (ou `plugin/<plugin>/config/bootstrap.php` para plugins)

<a name="make-process"></a>
### make:process

Gerar classe de processo personalizado e gravar em `config/process.php` para inicialização automática.

**Uso:**
```bash
php webman make:process <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome da classe do processo (ex.: MyTcp, MyWebsocket) |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--plugin` | `-p` | Gerar no diretório do plugin especificado |
| `--path` | `-P` | Diretório de destino (relativo à raiz do projeto) |
| `--force` | `-f` | Sobrescrever arquivo existente |

**Exemplos:**
```bash
# Criar em app/process
php webman make:process MyTcp

# Criar em plugin
php webman make:process MyProcess -p admin

# Caminho personalizado
php webman make:process MyProcess -P plugin/admin/app/process

# Sobrescrever arquivo existente
php webman make:process MyProcess -f
```

**Fluxo interativo:** Solicita em ordem: escutar em porta? → tipo de protocolo (websocket/http/tcp/udp/unixsocket) → endereço de escuta (IP:porta ou caminho unix socket) → quantidade de processos. O protocolo HTTP também pergunta sobre modo integrado ou personalizado.

**Estrutura do arquivo gerado:**

Processo sem escuta (apenas `onWorkerStart`):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

Processos de escuta TCP/WebSocket geram os templates de callback correspondentes `onConnect`, `onMessage`, `onClose`.

**Observações:**
- Colocado em `app/process/` por padrão; configuração do processo gravada em `config/process.php`
- A chave de configuração é snake_case do nome da classe; falha se já existir
- O modo integrado HTTP reutiliza o arquivo de processo `app\process\Http`, não gera novo arquivo
- Protocolos suportados: websocket, http, tcp, udp, unixsocket

## Build e Implantação

<a name="build-phar"></a>
### build:phar

Empacotar projeto como arquivo PHAR para distribuição e implantação.

**Uso:**
```bash
php webman build:phar
```

**Iniciar:**

Navegue até o diretório build e execute

```bash
php webman.phar start
```

**Observações:**
* O projeto empacotado não suporta reload; use restart para atualizar o código

* Para evitar tamanho de arquivo grande e uso excessivo de memória, configure exclude_pattern e exclude_files em config/plugin/webman/console/app.php para excluir arquivos desnecessários.

* A execução do webman.phar cria um diretório runtime no mesmo local para logs e arquivos temporários.

* Se o projeto usa arquivo .env, coloque o .env no mesmo diretório do webman.phar.

* webman.phar não suporta processos personalizados no Windows

* Nunca armazene arquivos enviados por usuários dentro do pacote phar; operar em uploads de usuários via phar:// é perigoso (vulnerabilidade de desserialização phar). Os uploads de usuários devem ser armazenados separadamente em disco fora do phar. Veja abaixo.

* Se o seu negócio precisa enviar arquivos para o diretório público, extraia o diretório public para o mesmo local do webman.phar e configure config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Use a função auxiliar public_path($relative_path) para obter o caminho real do diretório público.


<a name="build-bin"></a>
### build:bin

Empacotar projeto como binário independente com runtime PHP embutido. Não é necessária instalação de PHP no ambiente de destino.

**Uso:**
```bash
php webman build:bin [version]
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `version` | Não | Versão do PHP (ex.: 8.1, 8.2), padrão é a versão atual do PHP, mínimo 8.1 |

**Exemplos:**
```bash
# Usar versão atual do PHP
php webman build:bin

# Especificar PHP 8.2
php webman build:bin 8.2
```

**Iniciar:**

Navegue até o diretório build e execute

```bash
./webman.bin start
```

**Observações:**
* Fortemente recomendado: a versão local do PHP deve corresponder à versão do build (ex.: PHP 8.1 local → build com 8.1) para evitar problemas de compatibilidade
* O build baixa o código-fonte do PHP 8 mas não instala localmente; não afeta o ambiente PHP local
* webman.bin atualmente só executa em Linux x86_64; não suportado em macOS
* O projeto empacotado não suporta reload; use restart para atualizar o código
* .env não é empacotado por padrão (controlado por exclude_files em config/plugin/webman/console/app.php); coloque .env no mesmo diretório do webman.bin ao iniciar
* Um diretório runtime é criado no diretório do webman.bin para arquivos de log
* webman.bin não lê php.ini externo; para configurações personalizadas de php.ini, use custom_ini em config/plugin/webman/console/app.php
* Exclua arquivos desnecessários via config/plugin/webman/console/app.php para evitar tamanho grande do pacote
* O build binário não suporta corrotinas Swoole
* Nunca armazene arquivos enviados por usuários dentro do pacote binário; operar via phar:// é perigoso (vulnerabilidade de desserialização phar). Os uploads de usuários devem ser armazenados separadamente em disco fora do pacote.
* Se o seu negócio precisa enviar arquivos para o diretório público, extraia o diretório public para o mesmo local do webman.bin e configure config/app.php conforme abaixo, depois reconstrua:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Executar script de instalação do framework Webman (chama `\Webman\Install::install()`), para inicialização do projeto.

**Uso:**
```bash
php webman install
```

## Comandos Utilitários

<a name="version"></a>
### version

Exibir versão do workerman/webman-framework.

**Uso:**
```bash
php webman version
```

**Observações:** Lê a versão de `vendor/composer/installed.php`; retorna falha se não conseguir ler.

<a name="fix-disable-functions"></a>
### fix-disable-functions

Corrigir `disable_functions` no php.ini, removendo funções necessárias para o Webman.

**Uso:**
```bash
php webman fix-disable-functions
```

**Observações:** Remove as seguintes funções (e correspondências por prefixo) de `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Ignora se php.ini não for encontrado ou `disable_functions` estiver vazio. **Modifica diretamente o arquivo php.ini**; recomenda-se backup.

<a name="route-list"></a>
### route:list

Listar todas as rotas registradas em formato de tabela.

**Uso:**
```bash
php webman route:list
```

**Exemplo de saída:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Colunas de saída:** URI, Method, Callback, Middleware, Name. Callbacks Closure são exibidos como "Closure".

## Gerenciamento de Plugin de Aplicação (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Criar novo plugin de aplicação, gerando estrutura de diretórios completa e arquivos base em `plugin/<name>`.

**Uso:**
```bash
php webman app-plugin:create <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do plugin; deve corresponder a `[a-zA-Z0-9][a-zA-Z0-9_-]*`, não pode conter `/` ou `\` |

**Exemplos:**
```bash
# Criar plugin de aplicação chamado foo
php webman app-plugin:create foo

# Criar plugin com hífen
php webman app-plugin:create my-app
```

**Estrutura de diretórios gerada:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, etc.
├── api/Install.php  # Hooks de instalação/desinstalação/atualização
├── public/
└── install.sql
```

**Observações:**
- O plugin é criado em `plugin/<name>/`; falha se o diretório já existir

<a name="app-plugin-install"></a>
### app-plugin:install

Instalar plugin de aplicação, executando `plugin/<name>/api/Install::install($version)`.

**Uso:**
```bash
php webman app-plugin:install <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do plugin; deve corresponder a `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Exemplos:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Desinstalar plugin de aplicação, executando `plugin/<name>/api/Install::uninstall($version)`.

**Uso:**
```bash
php webman app-plugin:uninstall <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do plugin |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--yes` | `-y` | Pular confirmação, executar diretamente |

**Exemplos:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Atualizar plugin de aplicação, executando `Install::beforeUpdate($from, $to)` e `Install::update($from, $to, $context)` em ordem.

**Uso:**
```bash
php webman app-plugin:update <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do plugin |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--from` | `-f` | Versão de origem, padrão é a versão atual |
| `--to` | `-t` | Versão de destino, padrão é a versão atual |

**Exemplos:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Empacotar plugin de aplicação como arquivo ZIP, saída em `plugin/<name>.zip`.

**Uso:**
```bash
php webman app-plugin:zip <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do plugin |

**Exemplos:**
```bash
php webman app-plugin:zip foo
```

**Observações:**
- Exclui automaticamente `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__`, etc.

## Gerenciamento de Plugins (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Criar novo plugin Webman (forma de pacote Composer), gerando diretório de configuração `config/plugin/<name>` e diretório de código-fonte do plugin `vendor/<name>`.

**Uso:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do pacote do plugin no formato `vendor/package` (ex.: `foo/my-admin`); deve seguir nomenclatura de pacote Composer |

**Exemplos:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Estrutura gerada:**
- `config/plugin/<name>/app.php`: Configuração do plugin (inclui chave `enable`)
- `vendor/<name>/composer.json`: Definição do pacote do plugin
- `vendor/<name>/src/`: Diretório de código-fonte do plugin
- Adiciona automaticamente mapeamento PSR-4 ao composer.json da raiz do projeto
- Executa `composer dumpautoload` para atualizar o autoload

**Observações:**
- O nome deve estar no formato `vendor/package`: letras minúsculas, números, `-`, `_`, `.`, e deve conter um `/`
- Falha se `config/plugin/<name>` ou `vendor/<name>` já existir
- Erro se argumento e `--name` forem fornecidos com valores diferentes

<a name="plugin-install"></a>
### plugin:install

Executar script de instalação do plugin (`Install::install()`), copiando recursos do plugin para o diretório do projeto.

**Uso:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do pacote do plugin no formato `vendor/package` (ex.: `foo/my-admin`) |

**Opções:**

| Opção | Descrição |
|--------|-------------|
| `--name` | Especificar nome do plugin como opção; use isto ou o argumento |

**Exemplos:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Executar script de desinstalação do plugin (`Install::uninstall()`), removendo recursos do plugin do projeto.

**Uso:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do pacote do plugin no formato `vendor/package` |

**Opções:**

| Opção | Descrição |
|--------|-------------|
| `--name` | Especificar nome do plugin como opção; use isto ou o argumento |

**Exemplos:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Habilitar plugin, definindo `enable` como `true` em `config/plugin/<name>/app.php`.

**Uso:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do pacote do plugin no formato `vendor/package` |

**Opções:**

| Opção | Descrição |
|--------|-------------|
| `--name` | Especificar nome do plugin como opção; use isto ou o argumento |

**Exemplos:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Desabilitar plugin, definindo `enable` como `false` em `config/plugin/<name>/app.php`.

**Uso:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do pacote do plugin no formato `vendor/package` |

**Opções:**

| Opção | Descrição |
|--------|-------------|
| `--name` | Especificar nome do plugin como opção; use isto ou o argumento |

**Exemplos:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Exportar configuração do plugin e diretórios especificados do projeto para `vendor/<name>/src/`, e gerar `Install.php` para empacotamento e publicação.

**Uso:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Argumentos:**

| Argumento | Obrigatório | Descrição |
|----------|----------|-------------|
| `name` | Sim | Nome do pacote do plugin no formato `vendor/package` |

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--name` | | Especificar nome do plugin como opção; use isto ou o argumento |
| `--source` | `-s` | Caminho para exportar (relativo à raiz do projeto); pode ser especificado múltiplas vezes |

**Exemplos:**
```bash
# Exportar plugin, padrão inclui config/plugin/<name>
php webman plugin:export foo/my-admin

# Exportar adicionalmente app, config, etc.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Observações:**
- O nome do plugin deve seguir nomenclatura de pacote Composer (`vendor/package`)
- Se `config/plugin/<name>` existir e não estiver em `--source`, é adicionado automaticamente à lista de exportação
- O `Install.php` exportado inclui `pathRelation` para uso por `plugin:install` / `plugin:uninstall`
- `plugin:install` e `plugin:uninstall` exigem que o plugin exista em `vendor/<name>`, com classe `Install` e constante `WEBMAN_PLUGIN`

## Gerenciamento de Serviços

<a name="start"></a>
### start

Iniciar processos worker do Webman. Modo DEBUG padrão (primeiro plano).

**Uso:**
```bash
php webman start
```

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--daemon` | `-d` | Iniciar em modo DAEMON (segundo plano) |

<a name="stop"></a>
### stop

Parar processos worker do Webman.

**Uso:**
```bash
php webman stop
```

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--graceful` | `-g` | Parada suave; aguardar conclusão das requisições atuais antes de encerrar |

<a name="restart"></a>
### restart

Reiniciar processos worker do Webman.

**Uso:**
```bash
php webman restart
```

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--daemon` | `-d` | Executar em modo DAEMON após reiniciar |
| `--graceful` | `-g` | Parada suave antes de reiniciar |

<a name="reload"></a>
### reload

Recarregar código sem interrupção. Para hot-reload após atualizações de código.

**Uso:**
```bash
php webman reload
```

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--graceful` | `-g` | Recarga suave; aguardar conclusão das requisições atuais antes de recarregar |

<a name="status"></a>
### status

Visualizar status de execução dos processos worker.

**Uso:**
```bash
php webman status
```

**Opções:**

| Opção | Atalho | Descrição |
|--------|----------|-------------|
| `--live` | `-d` | Exibir detalhes (status em tempo real) |

<a name="connections"></a>
### connections

Obter informações de conexão dos processos worker.

**Uso:**
```bash
php webman connections
```
