# Injeção Automática de Dependências

No webman, a injeção automática de dependências é uma funcionalidade opcional, e por padrão está desativada. Se você precisa de injeção automática de dependências, é recomendável usar o [php-di](https://php-di.org/doc/getting-started.html). Abaixo está um exemplo de como utilizar o `php-di` em conjunto com o webman.

## Instalação
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Modifique a configuração `config/container.php`, que deve ter o seguinte conteúdo:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> O arquivo `config/container.php` deve retornar uma instância de contêiner que esteja em conformidade com a especificação `PSR-11`. Caso não deseje utilizar o `php-di`, pode criar e retornar uma outra instância de contêiner em conformidade com a especificação `PSR-11`.

## Injeção por Construtor
Crie um novo arquivo `app/service/Mailer.php` (se o diretório não existir, crie-o) com o seguinte conteúdo:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Código para enviar e-mail omitido
    }
}
```

O conteúdo do arquivo `app/controller/UserController.php` é o seguinte:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Olá e bem-vindo!');
        return response('ok');
    }
}
```
Normalmente, o UserController da classe `app\controller\UserController` teria que ser instanciado da seguinte maneira:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Com o uso do `php-di`, o desenvolvedor não precisa instanciar manualmente o `Mailer` dentro do controlador, pois o webman fará isso automaticamente. Caso a instância do `Mailer` tenha dependências de outras classes, o webman também as instanciará e injetará automaticamente. O desenvolvedor não precisa fazer nenhum trabalho de inicialização.

> **Observação**
> A injeção automática de dependências só pode ser concluída com instâncias criadas pelo fórum ou pelo `php-di`. Instâncias criadas manualmente com o `new` não podem ser injetadas automaticamente e, se necessário, devem ser substituídas por um container de interface `support\Container`, por exemplo:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// A instância criada com a palavra-chave "new" não pode ser injetada
$user_service = new UserService;
// A instância criada com a palavra-chave "new" não pode ser injetada
$log_service = new LogService($path, $name);

// A instância criada com o Container pode ser injetada
$user_service = Container::get(UserService::class);
// A instância criada com o Container pode ser injetada
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Injeção por Anotação
Além da injeção por construtor, é possível utilizar a injeção por anotação. No exemplo anterior, o `app\controller\UserController` seria alterado da seguinte forma:
```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Olá e bem-vindo!');
        return response('ok');
    }
}
```
Neste caso, a injeção é realizada via anotação `@Inject`, e o tipo do objeto é declarado por meio da anotação `@var`. O resultado é o mesmo da injeção por construtor, porém o código fica mais conciso.

> **Observação**
> Antes da versão 1.4.6, o webman não suportava a injeção de parâmetros no controlador. Por exemplo, o código abaixo não é suportado quando webman <= 1.4.6:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Antes da versão 1.4.6, não suporta a injeção de parâmetros no controlador
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Olá e bem-vindo!');
        return response('ok');
    }
}
```

## Injeção Personalizada por Construtor

Às vezes, os parâmetros passados para o construtor não são instâncias de classe, mas sim strings, números, arrays, etc. Por exemplo, o construtor de Mailer precisa receber o IP e a porta do servidor SMTP:
```php
<?php
namespace app\service;

class Mailer
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // Código para enviar e-mail omitido
    }
}
```
Nesse caso, a injeção automática por construtor não será possível, pois o `php-di` não tem informações sobre os valores de `$smtp_host` e `$smtp_port`. Nesse caso, é possível tentar a injeção personalizada.

No arquivo `config/dependence.php` (se o arquivo não existir, crie-o), adicione o código a seguir:
```php
return [
    // ... outros ajustes ignorados

    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Dessa forma, quando a injeção de dependência necessitar de uma instância de `app\service\Mailer`, será automaticamente utilizada a instância de `app\service\Mailer` criada neste arquivo de configuração.

Observamos que o `config/dependence.php` utiliza o `new` para instanciar a classe `Mailer`. Não há problemas neste exemplo, mas imagine se a classe `Mailer` tiver dependências de outras classes ou utiliza injeção por anotações internamente. Nesse caso, o uso do `new` para a inicialização não permitirá a injeção automática de dependências. A solução é utilizar a injeção personalizada por meio da interface `Container`, utilizando os métodos `Container::get(NomeDaClasse)` ou `Container::make(NomeDaClasse, [ParâmetrosDoConstrutor])` para inicializar a classe.

## Injeção Personalizada por Interface
Em projetos reais, é preferível programar para interfaces em vez de classes concretas. Por exemplo, `app\controller\UserController` deveria injetar `app\service\MailerInterface` em vez de `app\service\Mailer`.

Defina a interface `MailerInterface`.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Defina a implementação da interface `MailerInterface`.
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    private $smtpHost;
    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // Código para enviar e-mail omitido
    }
}
```

Injete a interface `MailerInterface` em vez da implementação concreta.
```php
<?php
namespace app\controller;

use support\Request;
use app\service\MailerInterface;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var MailerInterface
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Olá e bem-vindo!');
        return response('ok');
    }
}
```

No arquivo `config/dependence.php`, defina a interface `MailerInterface` para utilizar a implementação concreta da seguinte forma:
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Dessa forma, quando o negócio precisar utilizar a interface `MailerInterface`, será automaticamente utilizada a implementação concreta `Mailer`.

> A vantagem da programação para interfaces é que, ao substituir um componente, não é necessário alterar o código do negócio. Basta alterar a implementação concreta no arquivo `config/dependence.php`. Isso também é muito útil para testes unitários.

## Outras Injeções Personalizadas
O arquivo `config/dependence.php` pode definir não apenas as dependências das classes, mas também outros valores, como strings, números, arrays, etc.

Por exemplo, o `config/dependence.php` pode definir o seguinte:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Desta forma, é possível injetar as propriedades `smtp_host` e `smtp_port` para a classe por meio da anotação `@Inject`.
```php
<?php
namespace app\service;

use DI\Annotation\Inject;

class Mailer
{
    /**
     * @Inject("smtp_host")
     */
    private $smtpHost;

    /**
     * @Inject("smtp_port")
     */
    private $smtpPort;

    public function mail($email, $content)
    {
        // Código para enviar e-mail omitido
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Irá imprimir 192.168.1.11:25
    }
}
```

> Observação: Em `@Inject("chave")`, a chave deve estar entre aspas duplas.

## Mais informações
Consulte o [manual do php-di](https://php-di.org/doc/getting-started.html) para mais informações.
