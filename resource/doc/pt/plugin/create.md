# Processo de criação e publicação de plugins básicos

## Princípio
1. Tomando como exemplo o plugin de cross-domain, o plugin é composto por três partes: um arquivo de programa de middleware de cross-domain, um arquivo de configuração de middleware.php e um arquivo Install.php gerado automaticamente por comando.
2. Usamos um comando para empacotar esses três arquivos e publicá-los no composer.
3. Quando o usuário instala o plugin de cross-domain usando o composer, o Install.php no plugin copiará o arquivo de programa de middleware de cross-domain e o arquivo de configuração para `{diretório raiz do projeto}/config/plugin`, para que o webman carregue e faça com que o arquivo de middleware de cross-domain seja automaticamente configurado e ativado.
4. Quando o usuário remove o plugin, o Install.php excluirá os arquivos correspondentes de programa de middleware de cross-domain e de configuração, realizando a desinstalação automática do plugin.

## Normas
1. O nome do plugin é composto por duas partes, `fornecedor` e `nome do plugin`, por exemplo, `webman/push`, o que corresponde ao nome do pacote do composer.
2. Os arquivos de configuração do plugin são uniformemente colocados em `config/plugin/fornecedor/nome do plugin/` (o comando console criará automaticamente o diretório de configuração). Se o plugin não precisar de configuração, o diretório de configuração criado automaticamente precisa ser excluído.
3. O diretório de configuração do plugin suporta apenas app.php (configuração principal do plugin), bootstrap.php (configuração de inicialização do processo), route.php (configuração de rota), middleware.php (configuração de middleware), process.php (configuração de processo personalizado), database.php (configuração do banco de dados), redis.php (configuração do Redis), thinkorm.php (configuração do ThinkORM). Essas configurações serão reconhecidas automaticamente pelo webman.
4. O plugin utiliza o seguinte método para obter configurações `config('plugin.fornecedor.nome do plugin.arquivo de configuração.configuração específica');`, por exemplo, `config('plugin.webman.push.app.app_key')`
5. Se o plugin tiver sua própria configuração de banco de dados, ela pode ser acessada da seguinte forma: `illuminate/database` para `Db::connection('plugin.fornecedor.nome do plugin.conexão específica')`, `thinkrom` para `Db::connect('plugin.fornecedor.nome do plugin.conexão específica')`
6. Se o plugin precisar colocar arquivos de negócios no diretório `app/`, é necessário garantir que não entrem em conflito com os projetos dos usuários ou outros plugins.
7. O plugin deve evitar a cópia de arquivos ou diretórios para o projeto principal, por exemplo, o plugin de cross-domain, além do arquivo de configuração que precisa ser copiado para o projeto principal, o arquivo de middleware deve ser colocado em `vendor/webman/cros/src` e não precisa ser copiado para o projeto principal.
8. É recomendável que o namespace do plugin use maiúsculas, por exemplo Webman/Console.

## Exemplo

**Instalar o CLI `webman/console`**

`composer require webman/console`

#### Criar plugin

Supondo que o plugin a ser criado se chama `foo/admin` (o nome será o nome do projeto a ser publicado posteriormente, e o nome deve ser em minúsculas).
Execute o comando
`php webman plugin:create --name=foo/admin`

Após criar o plugin, será gerado o diretório `vendor/foo/admin` para armazenar os arquivos relacionados ao plugin e `config/plugin/foo/admin` para armazenar as configurações relacionadas ao plugin.

> Atente-se
> `config/plugin/foo/admin` suporta a inclusão das seguintes configurações: app.php (configuração principal do plugin), bootstrap.php (configuração de inicialização do processo), route.php (configuração de rota), middleware.php (configuração de middleware), process.php (configuração de processo personalizado), database.php (configuração do banco de dados), redis.php (configuração do Redis), thinkorm.php (configuração do ThinkORM). O formato das configurações é o mesmo do webman, e essas configurações serão reconhecidas e mescladas automaticamente pelo webman.
Ao acessar, utilize um prefixo `plugin`, por exemplo, `config('plugin.foo.admin.app');`

#### Exportar o plugin

Após desenvolver o plugin, execute o seguinte comando para exportá-lo
`php webman plugin:export --name=foo/admin`

> Observação
> Após a exportação, o diretório config/plugin/foo/admin será copiado para vendor/foo/admin/src e automaticamente gerará um Install.php. O Install.php é utilizado para executar operações automaticamente durante a instalação e desinstalação do plugin.
> A operação padrão de instalação é copiar as configurações de vendor/foo/admin/src para o diretório config/plugin do projeto atual.
> A operação padrão de remoção é excluir os arquivos de configuração do diretório config/plugin do projeto atual.
> Você pode modificar o Install.php para realizar algumas operações personalizadas na instalação e desinstalação do plugin.

#### Submeter o plugin
* Supondo que você já tenha uma conta no [GitHub](https://github.com) e [Packagist](https://packagist.org)
* No [GitHub](https://github.com), crie um projeto chamado admin e envie o código. Suponha que o endereço do projeto seja `https://github.com/seunome/admin`
* Acesse `https://github.com/seunome/admin/releases/new` para publicar um release, como `v1.0.0`
* Acesse o [Packagist](https://packagist.org), clique em `Submit` na navegação e envie o endereço de seu projeto no GitHub `https://github.com/seunome/admin`, concluindo assim a publicação de um plugin.

> **Dica**
> Se houver conflitos ao enviar o plugin para o `Packagist`, você pode escolher um novo nome para o fornecedor, por exemplo, mudar `foo/admin` para `myfoo/admin`. 

Quando o código do seu projeto de plugin for atualizado, é necessário sincronizá-lo com o GitHub e, em seguida, acessar `https://github.com/seunome/admin/releases/new` para publicar novamente um release e, em seguida, na página `https://packagist.org/packages/foo/admin`, clique no botão `Update` para atualizar a versão.
## Adicionar comando ao plugin
Às vezes, nossos plugins precisam de comandos personalizados para oferecer algumas funcionalidades auxiliares. Por exemplo, após instalar o plugin `webman/redis-queue`, o projeto automaticamente terá um comando `redis-queue:consumer`, no qual o usuário apenas precisa executar `php webman redis-queue:consumer send-mail` para gerar uma classe consumidora SendMail.php no projeto, o que ajuda no desenvolvimento rápido.

Suponha que o plugin `foo/admin` precise adicionar o comando `foo-admin:add`, siga as etapas abaixo.

#### Criar novo comando
**Crie o arquivo de comando `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'Aqui vai a descrição do comando';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Adicionar nome');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }
}
```

> **Observação**
> Para evitar conflitos de comando entre plugins, é recomendado que o formato do comando seja `fabricante-nome-do-plugin:comando-específico`, por exemplo, todos os comandos do plugin `foo/admin` devem seguir o padrão `foo-admin:comando`, como `foo-admin:add`.

#### Adicionar configuração
**Crie o arquivo de configuração `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....adicionar várias configurações...
];
```

> **Dica**
> `command.php` é usado para configurar comandos personalizados para o plugin. Cada elemento no array corresponde a um arquivo de classe de comando e cada arquivo de classe corresponde a um comando. Quando o usuário executa um comando, o `webman/console` carrega automaticamente os comandos personalizados configurados em `command.php` de cada plugin. Para obter mais informações sobre comandos, consulte [Console](console.md).

#### Executar exportação
Execute o comando `php webman plugin:export --name=foo/admin` para exportar o plugin e enviá-lo para o `packagist`. Dessa forma, quando os usuários instalarem o plugin `foo/admin`, será adicionado o comando `foo-admin:add`. Ao executar `php webman foo-admin:add jerry`, será impresso `Admin add jerry`.
