# Inyección automática de dependencias

En webman, la inyección automática de dependencias es una característica opcional que está deshabilitada de forma predeterminada. Si necesitas utilizar la inyección automática de dependencias, se recomienda usar [php-di](https://php-di.org/doc/getting-started.html). A continuación se muestra cómo se utiliza `php-di` en combinación con webman.

## Instalación
```composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14```

Modifica el archivo de configuración `config/container.php` de la siguiente manera:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> El archivo `config/container.php` debe devolver una instancia de contenedor que cumpla con la especificación `PSR-11`. Si no deseas utilizar `php-di`, puedes crear y devolver otra instancia que cumpla con la especificación `PSR-11`.

## Inyección a través de constructores
Crea el archivo `app/service/Mailer.php` (crea el directorio si no existe) con el siguiente contenido:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Código para enviar correo electrónico omitido
    }
}
```

El contenido del archivo `app/controller/UserController.php` sería:
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
        $this->mailer->mail('hello@webman.com', '¡Hola y bienvenido!');
        return response('ok');
    }
}
```
Normalmente, para instanciar `app\controller\UserController`, se necesitaría el siguiente código:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Sin embargo, al utilizar `php-di`, el desarrollador no necesita instanciar manualmente `Mailer` en el controlador, ya que webman lo hace automáticamente. Además, si durante la instancia de `Mailer` existe alguna dependencia, webman la instanciará e inyectará automáticamente. El desarrollador no necesita realizar ningún trabajo adicional de inicialización.

> **Nota**
> Solo las instancias creadas por el framework o `php-di` pueden implementar la inyección automática de dependencias. Las instancias creadas manualmente con `new` no pueden utilizar la inyección automática de dependencias. Si es necesario la inyección, se debe utilizar la interfaz `support\Container` en lugar de la declaración `new`, por ejemplo:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Las instancias creadas con la palabra clave 'new' no pueden ser inyectadas
$user_service = new UserService;
// Las instancias creadas con la palabra clave 'new' no pueden ser inyectadas
$log_service = new LogService($path, $name);

// Las instancias creadas con el contenedor pueden ser inyectadas
$user_service = Container::get(UserService::class);
// Las instancias creadas con el contenedor pueden ser inyectadas
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Inyección a través de anotaciones
Además de la inyección de dependencias a través de constructores, también se puede utilizar la inyección a través de anotaciones. Continuando con el ejemplo anterior, el archivo `app/controller/UserController` sería modificado de la siguiente manera:
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
        $this->mailer->mail('hello@webman.com', '¡Hola y bienvenido!');
        return response('ok');
    }
}
```
En este ejemplo, se utiliza la inyección a través de la anotación `@Inject`, junto con la anotación `@var` para declarar el tipo de objeto. El efecto de este ejemplo es similar a la inyección a través de constructores, pero el código es más conciso.

> **Nota**
> Antes de la versión 1.4.6, webman no admitía la inyección de parámetros del controlador, por lo tanto, el siguiente código no sería compatible con webman <= 1.4.6:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Antes de la versión 1.4.6, no se admitía la inyección de parámetros del controlador
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', '¡Hola y bienvenido!');
        return response('ok');
    }
}
```
## Inyección personalizada a través de constructores

En ocasiones, los parámetros pasados al constructor pueden ser de tipos como cadenas, números, arreglos, etc. Por ejemplo, el constructor de la clase `Mailer` requiere la dirección IP y el puerto del servidor SMTP:
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
        // Código para enviar correo electrónico omitido
    }
}
```
En este caso, no es posible utilizar la inyección automática de constructores mencionada anteriormente, ya que `php-di` no puede determinar los valores de `$smtp_host` y `$smtp_port`. En este caso, se puede intentar utilizar una inyección personalizada.

Agrega el siguiente contenido al archivo `config/dependence.php` (crea el archivo si no existe):
```php
return [
    // ... Se omitieron otras configuraciones

    app\service\Mailer::class => new app\service\Mailer('192.168.1.11', 25);
];
```
De esta manera, cuando la inyección de dependencias necesita obtener una instancia de `app\service\Mailer`, se utilizará automáticamente la instancia de `app\service\Mailer` creada en esta configuración.

Observa que en `config/dependence.php` se usó `new` para instanciar la clase `Mailer`. En este ejemplo no hay problema, pero si la clase `Mailer` tiene dependencias de otras clases o utiliza la inyección a través de anotaciones, la inicialización con `new` no permitirá la inyección automática de dependencias. La solución es utilizar la inyección personalizada a través de la interfaz `Container::get(class_name)` o el método `Container::make(class_name, [constructor_parameters])` en lugar de la declaración `new`.

## Inyección personalizada a través de interfaces

En proyectos reales, es preferible programar orientado a interfaces en lugar de a clases concretas. Por ejemplo, en `app\controller\UserController`, se debería hacer referencia a `app\service\MailerInterface` en lugar de `app\service\Mailer`.

Definimos la interfaz `MailerInterface` de la siguiente manera:
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```
Implementamos la interfaz `MailerInterface` de la siguiente manera:
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
        // Código para enviar correo electrónico omitido
    }
}
```
En el archivo `app\controller\UserController`, hacemos referencia a la interfaz `MailerInterface` en lugar de la implementación concreta:
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
        $this->mailer->mail('hello@webman.com', '¡Hola y bienvenido!');
        return response('ok');
    }
}
```
En el archivo `config/dependence.php`, se define la implementación de la interfaz `MailerInterface` de la siguiente manera:
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```
De esta manera, cuando se necesite utilizar la interfaz `MailerInterface` en el proyecto, se utilizará automáticamente la implementación de `Mailer`. Una de las ventajas de programar orientado a interfaces es que, al cambiar un componente, no es necesario modificar el código del proyecto, simplemente se debe cambiar la implementación concreta en `config/dependence.php`. Esto es muy útil también para realizar pruebas unitarias.
## Otras inyecciones personalizadas
Además de definir las dependencias de clases, `config/dependence.php` también puede definir otros valores, como cadenas de texto, números, matrices, etc.

Por ejemplo, si definimos lo siguiente en `config/dependence.php`:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

En este caso, podemos inyectar `smtp_host` y `smtp_port` en las propiedades de la clase utilizando `@Inject`.
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
        // Código para enviar correos electrónicos omitido
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Imprimirá 192.168.1.11:25
    }
}
```

> Nota: Dentro de `@Inject("clave")`, se utilizan comillas dobles


## Más información
Consulta el [manual de php-di](https://php-di.org/doc/getting-started.html) para obtener más detalles.
