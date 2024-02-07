# Estructura de directorios

```plaintext
plugin/
└── foo
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
    ├── public
    └── api
```

Observamos una estructura de directorios y archivos de configuración similar a la de webman dentro de un complemento de la aplicación. De hecho, la experiencia de desarrollo es casi idéntica a la de desarrollar una aplicación webman normal.
Los nombres de los directorios y los complementos siguen la especificación PSR4. Debido a que los complementos se almacenan en el directorio `plugin`, todos los espacios de nombres comienzan con `plugin`, por ejemplo, `plugin\foo\app\controller\UserController`.

## Acerca del directorio api
Cada complemento tiene un directorio llamado `api`. Si su aplicación proporciona algunas interfaces internas para que otras aplicaciones las utilicen, es necesario colocar esas interfaces en el directorio `api`.
Es importante destacar que estas interfaces hacen referencia a llamadas de funciones, no a llamadas de red. Por ejemplo, el complemento de `correo electrónico` proporciona una interfaz `Email::send()` en `plugin/email/api/Email.php` para que otras aplicaciones envíen correos electrónicos.
Además, `plugin/email/api/Install.php` se genera automáticamente para permitir que webman-admin llame a la tienda de complementos para realizar operaciones de instalación o desinstalación.
