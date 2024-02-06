# Inyección automática de dependencias

En webman, la inyección automática de dependencias es una característica opcional que está desactivada de forma predeterminada. Si necesitas la inyección automática de dependencias, se recomienda usar [php-di](https://php-di.org/doc/getting-started.html). A continuación se muestra cómo utilizar `php-di` en combinación con webman.

## Instalación
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Modifica la configuración en `config/container.php`, y el contenido final será el siguiente:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> El archivo `config/container.php` debe devolver una instancia de contenedor que cumpla con la especificación de `PSR-11`. Si no deseas utilizar `php-di`, puedes crear y devolver una instancia de contenedor que cumpla con la especificación de `PSR-11` aquí.

## Inyección de constructor
Crea un archivo `app/service/Mailer.php` (si el directorio no existe, créalo tú mismo) con el siguiente contenido:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Código de envío de correo electrónico omitido
    }
}
```

El contenido de `app/controller/UserController.php` es el siguiente:

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
Normalmente, se necesitarían las siguientes líneas de código para instanciar `app\controller\UserController`:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Sin embargo, al utilizar `php-di`, los desarrolladores no necesitan instanciar manualmente `Mailer` en el controlador, ya que webman lo hará automáticamente por ellos. Si en el proceso de instanciación de `Mailer` hay otras dependencias de clases, webman también las instanciará e inyectará automáticamente. Los desarrolladores no necesitan realizar ningún trabajo de inicialización.

> **Nota**
> Solo se puede completar la inyección automática de dependencias con instancias creadas por el marco o `php-di`. Las instancias creadas manualmente con `new` no podrán completar la inyección automática de dependencias. Si es necesario inyectar, hay que utilizar la interfaz `support\Container` en lugar de la declaración `new`, por ejemplo:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// La instancia creada con la palabra clave "new" no puede ser inyectada
$user_service = new UserService;
// La instancia creada con la palabra clave "new" no puede ser inyectada
$log_service = new LogService($path, $name);

// La instancia creada con Container puede ser inyectada
$user_service = Container::get(UserService::class);
// La instancia creada con Container puede ser inyectada
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Inyección de anotaciones
Además de la inyección automática de dependencias mediante constructores, también se puede utilizar la inyección de anotaciones. Continuando con el ejemplo anterior, el contenido de `app\controller\UserController` se modificaría de la siguiente manera:
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
En este ejemplo, se realiza la inyección mediante anotaciones `@Inject` y se declara el tipo de objeto mediante `@var`. Aunque este ejemplo logra el mismo efecto que la inyección a través de constructores, el código es más conciso.

> **Nota**
> Antes de la versión 1.4.6, webman no admitía la inyección de parámetros de controlador, por lo que el siguiente código no sería compatible con webman <= 1.4.6.

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Antes de la versión 1.4.6, el controlador de parámetros de inyección no es compatible
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', '¡Hola y bienvenido!');
        return response('ok');
    }
}
```

## Inyección personalizada en constructores

A veces, los parámetros pasados al constructor pueden no ser instancias de clases, sino cadenas, números, arreglos, u otros tipos de datos. Por ejemplo, el constructor de Mailer necesita pasar la dirección IP y el puerto del servidor SMTP:
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
        // Código de envío de correo electrónico omitido
    }
}
```
En este caso, no se puede utilizar la inyección automática de constructores mencionada anteriormente, ya que `php-di` no puede determinar los valores de `$smtp_host` y `$smtp_port`. En este caso, se puede intentar con una inyección personalizada.

Agrega el siguiente código al archivo `config/dependence.php` (crea el archivo si no existe):
```php
return [
    // ... Otros ajustes se omiten aquí
    app\service\Mailer::class => new app\service\Mailer('192.168.1.11', 25);
];
```
De esta manera, cuando se necesite obtener una instancia de `app\service\Mailer` mediante inyección de dependencias, se utilizará automáticamente la instancia de `app\service\Mailer` creada en esta configuración.

Observamos que en `config/dependence.php` se utilizó `new` para instanciar la clase `Mailer`, lo cual no presenta ningún problema en este ejemplo. Sin embargo, si Mailer depende de otras clases o utiliza inyecciones de anotaciones en su interior, la inicialización con `new` no permitirá la inyección automática de dependencias. La solución es utilizar la inyección de interfaces personalizadas, utilizando el método `Container::get(ClassName)` o `Container::make(ClassName, [construct_params])` para inicializar la clase.

## Inyección de interfaces personalizadas
En proyectos reales, preferimos programar orientándonos a interfaces en lugar de clases concretas. Por ejemplo, en `app\controller\UserController` deberíamos hacer referencia a `app\service\MailerInterface` en lugar de `app\service\Mailer`.

Define la interfaz `MailerInterface`.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Define la implementación de la interfaz `MailerInterface`.
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
        // Código de envío de correo electrónico omitido
    }
}
```

Haciendo referencia a la interfaz `MailerInterface`, en lugar de la implementación concreta.
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

Definir la implementación de la interfaz `MailerInterface` en `config/dependence.php` de la siguiente manera.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

De esta manera, cuando el sistema necesite utilizar la interfaz `MailerInterface`, automáticamente utilizará la implementación de `Mailer` especificada.

> La ventaja de programar orientándose a interfaces es que, al necesitar reemplazar algún componente, no es necesario modificar el código de negocio, solo es necesario cambiar la implementación concreta en `config/dependencia.php`. Esto es muy útil también en pruebas unitarias.

## Otras inyecciones personalizadas
En `config/dependence.php`, además de definir las dependencias de las clases, también se pueden definir otros valores, como cadenas, números, arreglos, etc.

Por ejemplo, `config/dependence.php` se puede definir como:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

De esta manera, podemos inyectar `smtp_host` y `smtp_port` en las propiedades de la clase a través de `@Inject`.
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
        // Código de envío de correo electrónico omitido
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Mostrará 192.168.1.11:25
    }
}
```

> Nota: `@Inject("key")` debe usarse con comillas dobles.

## Más información
Por favor, consulta el [manual de php-di](https://php-di.org/doc/getting-started.html) para obtener más información.
