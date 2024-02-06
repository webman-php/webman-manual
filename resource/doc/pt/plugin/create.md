# Processo de criação e publicação de plugins básicos

## Princípio
1. Tomando o exemplo de um plugin de domínio cruzado, o plugin é composto por três partes: um arquivo de programa de middleware de domínio cruzado, um arquivo de configuração de middleware.php e um Install.php gerado automaticamente por meio de um comando.
2. Usamos um comando para empacotar e publicar os três arquivos no composer.
3. Quando um usuário instala o plugin de domínio cruzado usando o composer, o Install.php do plugin copiará o programa de middleware de domínio cruzado e o arquivo de configuração para `{main_project}/config/plugin`, para que o webman os carregue. Isso implementa a configuração automática e eficaz do programa de middleware de domínio cruzado.
4. Quando um usuário remove o plugin usando o composer, o Install.php removerá os respectivos programas de middleware de domínio cruzado e arquivos de configuração, implementando a desinstalação automática do plugin.

## Padrão
1. O nome do plugin é composto por duas partes: `fabricante` e `nome_do_plugin`, por exemplo, `webman/push`, que corresponde ao nome do pacote do composer.
2. Os arquivos de configuração do plugin devem ser colocados em `config/plugin/fabricante/nome_do_plugin/` (o comando console criará automaticamente o diretório de configuração). Se o plugin não precisar de configuração, o diretório de configuração criado automaticamente deve ser excluído.
3. O diretório de configuração do plugin suporta apenas app.php (configuração principal do plugin), bootstrap.php (configuração de inicialização do processo), route.php (configuração de rota), middleware.php (configuração de middleware), process.php (configuração de processo personalizado), database.php (configuração de banco de dados), redis.php (configuração de redis) e thinkorm.php (configuração de thinkorm). Essas configurações serão reconhecidas automaticamente pelo webman.
4. O plugin usa o seguinte método para acessar a configuração: `config('plugin.fabricante.nome_do_plugin.nome_do_arquivo_de_configuracao.configuracao_especifica');`, por exemplo, `config('plugin.webman.push.app.app_key')`
5. Se o plugin tiver configurações de banco de dados próprias, elas podem ser acessadas da seguinte maneira: `illuminate/database` para `Db::connection('plugin.fabricante.nome_do_plugin.conexao_especifica')`, `thinkorm` para `Db::connect('plugin.fabricante.nome_do_plugin.conexao_especifica')`
6. Se o plugin precisar colocar arquivos de negócios no diretório `app/`, certifique-se de que eles não entrem em conflito com os arquivos do projeto do usuário ou de outros plugins.
7. O plugin deve evitar ao máximo copiar arquivos ou diretórios para o projeto principal. Por exemplo, o plugin de domínio cruzado deve colocar o arquivo do middleware em `vendor/webman/cros/src` e não precisa ser copiado para o projeto principal.
8. É recomendável que o namespace do plugin seja em letras maiúsculas, por exemplo, Webman/Console.

## Exemplo

**Instalando o comando `webman/console`**

`composer require webman/console`

#### Criando um plugin

Suponha que o nome do plugin a ser criado seja `foo/admin` (o nome também é o nome do projeto a ser publicado no Composer, e o nome deve estar em minúsculas).
Execute o comando
`php webman plugin:create --name=foo/admin`

Após criar o plugin, um diretório `vendor/foo/admin` será gerado para armazenar arquivos relacionados ao plugin e `config/plugin/foo/admin` será criado para armazenar configurações relacionadas ao plugin.

> Observação
> `config/plugin/foo/admin` suporta as seguintes configurações: app.php (configuração principal do plugin), bootstrap.php (configuração de inicialização do processo), route.php (configuração de rota), middleware.php (configuração de middleware), process.php (configuração de processo personalizado), database.php (configuração de banco de dados), redis.php (configuração de redis) e thinkorm.php (configuração de thinkorm). O formato das configurações é o mesmo que o webman reconhece, e essas configurações serão automaticamente reconhecidas e mescladas com as configurações.
Acesse usando o prefixo `plugin`, por exemplo, config('plugin.foo.admin.app');

#### Exportando o plugin

Após o desenvolvimento do plugin, execute o seguinte comando para exportar o plugin
`php webman plugin:export --name=foo/admin`
Exportar

> Explicação
> Após a exportação, o diretório config/plugin/foo/admin será copiado para src/vendor/foo/admin e um Install.php será gerado automaticamente. Install.php é usado para executar algumas operações automaticamente ao instalar e desinstalar o plugin.
A operação padrão de instalação é copiar as configurações de src/vendor/foo/admin para config/plugin do projeto atual
A operação de remoção padrão é excluir os arquivos de configuração de config/plugin do projeto atual
Você pode modificar o Install.php para realizar operações personalizadas durante a instalação e desinstalação do plugin.

#### Submetendo o plugin
* Suponha que você já tenha uma conta no [GitHub](https://github.com) e no [Packagist](https://packagist.org)
* No [GitHub](https://github.com), crie um projeto chamado admin e faça upload do código. O endereço do projeto é assumido como `https://github.com/seu_nome/admin`
* Acesse `https://github.com/seu_nome/admin/releases/new` para publicar um release, por exemplo, `v1.0.0`
* No [Packagist](https://packagist.org), clique em `Submit` no menu de navegação e envie o endereço do seu projeto no GitHub `https://github.com/seu_nome/admin` para concluir a publicação de um plugin.

> **Dica**
> Se houver um conflito ao enviar o plugin no `Packagist`, você pode escolher um novo nome para o fabricante, por exemplo, `foo/admin` pode ser alterado para `meufoo/admin`

Se o código do seu projeto de plugin for atualizado posteriormente, será necessário sincronizar o código com o GitHub e, em seguida, acessar `https://github.com/seu_nome/admin/releases/new` para publicar um novo release. Em seguida, vá para a página `https://packagist.org/packages/foo/admin` e clique no botão `Update` para atualizar a versão.

## Adicionando comandos ao plugin
Às vezes, nosso plugin precisa de alguns comandos personalizados para fornecer funcionalidades auxiliares, por exemplo, após a instalação do plugin `webman/redis-queue`, o projeto automaticamente adicionará um comando `redis-queue:consumer` e o usuário só precisa executar `php webman redis-queue:consumer send-mail` para gerar uma classe consumidora SendMail.php no projeto, o que ajuda no desenvolvimento rápido.

Suponha que o plugin `foo/admin` precise adicionar o comando `foo-admin:add`, siga as etapas abaixo.

#### Criando um novo comando
**Criar o arquivo de comando `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Esta é a descrição do comando';

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
        $output->writeln("Adicionar admin $name");
        return self::SUCCESS;
    }

}
```

> **Observação**
> Para evitar conflitos entre comandos de plugins, o formato do comando deve ser `fabricante-nome_do_plugin:comando_específico`, por exemplo, todos os comandos do plugin `foo/admin` devem ter o prefixo `foo-admin:`, por exemplo, `foo-admin:add`.

#### Adicionando configuração
**Crie a configuração `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ...você pode adicionar várias configurações...
];
```

> **Dica**
> `command.php` é usado para configurar comandos personalizados para o plugin. Cada elemento do array corresponde a um arquivo de classe de comando; cada arquivo de classe corresponde a um comando. Quando um usuário executa um comando de linha de comando, o `webman/console` carrega automaticamente cada comando personalizado configurado em `command.php` de cada plugin. Para obter mais informações sobre comandos de linha de comando, consulte [linha de comando](console.md)

#### Executando a exportação
Execute o comando `php webman plugin:export --name=foo/admin` para exportar o plugin e enviá-lo para o `packagist`. Dessa forma, ao instalar o plugin `foo/admin`, será adicionado um comando `foo-admin:add`. Execute `php webman foo-admin:add jerry` para imprimir `Adicionar admin jerry`.
