# Estructura de directorios

```
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

Observamos que un complemento de la aplicación tiene la misma estructura de directorios y archivos de configuración que webman. De hecho, la experiencia de desarrollo es prácticamente igual a la de desarrollar una aplicación común en webman.
Los nombres de directorios y complementos siguen la especificación PSR4. Dado que los complementos se colocan en el directorio plugin, los espacios de nombres comienzan con plugin, por ejemplo `plugin\foo\app\controller\UserController`.

## Acerca del directorio api
Cada complemento tiene un directorio api. Si tu aplicación proporciona algunas interfaces internas para que otras aplicaciones las utilicen, es necesario colocar esas interfaces en el directorio api.
Es importante mencionar que estas interfaces se refieren a interfaces de llamada de funciones, no a interfaces de llamada de red.
Por ejemplo, el plugin `Email` proporciona una interfaz `Email::send()` en el archivo `plugin/email/api/Email.php`, que se utiliza para enviar correos electrónicos desde otras aplicaciones.
Además, `plugin/email/api/Install.php` es generado automáticamente y se utiliza para permitir que el mercado de complementos webman-admin realice operaciones de instalación o desinstalación.
