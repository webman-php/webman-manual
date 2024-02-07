# Injeção Automática de Dependência

No webman, a injeção automática de dependência é uma funcionalidade opcional que é desativada por padrão. Se você precisa de injeção automática de dependência, é recomendável usar o [php-di](https://php-di.org/doc/getting-started.html). Abaixo está o uso do webman em combinação com o `php-di`.

## Instalação
```sh
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Modifique o arquivo de configuração `config/container.php` com o seguinte conteúdo final:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> O arquivo `config/container.php` deve retornar uma instância de um recipiente que atenda à especificação `PSR-11`. Se você não deseja usar o `php-di`, pode criar e retornar uma outra instância de recipiente que atenda à especificação `PSR-11`.

## Injeção por Construtor
Crie o arquivo `app/service/Mailer.php` (se o diretório não existir, crie-o manualmente) com o seguinte conteúdo:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Código de envio de e-mail omitido
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
Normalmente, as seguintes linhas de código seriam necessárias para instanciar `app\controller\UserController`:

```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Porém, ao usar o `php-di`, o desenvolvedor não precisa instanciar manualmente o `Mailer` dentro do controlador, pois o webman fará isso automaticamente. Se houver dependências de outras classes durante a instanciação do `Mailer`, o webman também as instanciará e injetará automaticamente. O desenvolvedor não precisa realizar nenhum trabalho de inicialização.

> **Nota**
> A injeção automática de dependência só pode ser realizada em instâncias criadas pelo framework ou pelo `php-di`. Instâncias criadas manualmente com `new` não podem ser injetadas automaticamente. Para permitir a injeção, é necessário usar a interface `support\Container` para substituir a declaração `new`. Por exemplo:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Instâncias criadas com a palavra-chave "new" não podem ser injetadas
$user_service = new UserService;
// Instâncias criadas com a palavra-chave "new" não podem ser injetadas
$log_service = new LogService($path, $name);

// As instâncias criadas com Container podem ser injetadas
$user_service = Container::get(UserService::class);
// As instâncias criadas com Container podem ser injetadas
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Injeção por Anotação
Além da injeção por construtor, também é possível usar a injeção por anotação. No exemplo anterior, o `app\controller\UserController` seria alterado da seguinte forma:
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
Neste exemplo, a injeção é realizada através da anotação `@Inject`, e o tipo do objeto é declarado através da anotação `@var`. O efeito é o mesmo da injeção por construtor, mas o código fica mais enxuto.

> **Nota**
> Antes da versão 1.4.6, o webman não suportava a injeção de parâmetros do controlador. Por exemplo, o seguinte código não era suportado se o webman <= 1.4.6:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Antes da versão 1.4.6, a injeção de parâmetros do controlador não é suportada
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Olá e bem-vindo!');
        return response('ok');
    }
}
```

## Injeção Personalizada por Construtor
Às vezes, os parâmetros passados para o construtor não são instâncias de classe, mas sim string, número, array, etc. Por exemplo, o construtor do `Mailer` precisa receber o endereço IP e a porta do servidor SMTP:
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
        // Código de envio de e-mail omitido
    }
}
```
Nesses casos, a injeção automática por construtor não funciona, pois o `php-di` não pode determinar os valores de `$smtp_host` e `$smtp_port`. Nesse ponto, você pode tentar a injeção personalizada.

No arquivo `config/dependence.php` (se não existir, crie-o manualmente), adicione o seguinte código:
```php
return [
    // ... outras configurações omitidas
    
    app\service\Mailer::class => new app\service\Mailer('192.168.1.11', 25);
];
```
Assim, quando a injeção de dependência precisar de uma instância de `app\service\Mailer`, será automaticamente utilizada a instância criada deste modo no arquivo de configuração.

Observa-se que, no arquivo `config/dependence.php`, foi utilizado `new` para instanciar a classe `Mailer`. Isso não apresenta problemas neste exemplo, mas imagine se a classe `Mailer` tivesse dependências de outras classes ou usasse injeção por anotação internamente, a inicialização com `new` não permitiria a injeção automática de dependência. A solução é usar a injeção personalizada por meio das interfaces `Container::get(classname)` ou `Container::make(classname, [construct_params])` para instanciar a classe.
## Injeção de Interface Personalizada

No mundo real, preferimos programar orientados a interfaces em vez de classes concretas. Por exemplo, em `app\controller\UserController`, deveríamos importar `app\service\MailerInterface` em vez de `app\service\Mailer`.

Definindo a interface `MailerInterface`:
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Definindo a implementação da interface `MailerInterface`:
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
        // Código de envio de e-mail OMITIDO
    }
}
```

Importando a interface `MailerInterface` em vez da implementação concreta:
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

Em `config/dependence.php`, define a implementação da interface `MailerInterface` da seguinte forma:
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Dessa forma, quando o negócio precisar usar a interface `MailerInterface`, a implementação `Mailer` será usada automaticamente.

> A vantagem da programação orientada a interfaces é que, quando precisamos mudar algum componente, não precisamos alterar o código do negócio; apenas precisamos alterar a implementação específica em `config/dependence.php`. Isso também é muito útil para fazer testes unitários.

## Outras Injeções Personalizadas

Em `config/dependence.php`, além de poder definir as dependências de classes, também é possível definir outros valores, como strings, números, arrays, etc.

Por exemplo, definindo em `config/dependence.php` da seguinte forma:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Dessa forma, podemos injetar `smtp_host` e `smtp_port` nos atributos da classe usando `@Inject`.
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
        // Código de envio de e-mail OMITIDO
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Irá imprimir 192.168.1.11:25
    }
}
```

> Nota: `@Inject("chave")` deve estar entre aspas duplas.

## Mais Conteúdo

Por favor, consulte o [Manual do PHP-DI](https://php-di.org/doc/getting-started.html) para mais informações.
