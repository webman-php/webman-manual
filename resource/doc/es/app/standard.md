# Especificación de desarrollo de complementos de aplicaciones

## Requerimientos de complementos de aplicaciones
* Los complementos no pueden contener código, iconos, imágenes u otros elementos que infrinjan los derechos de autor.
* El código fuente del complemento debe estar completo y no puede estar encriptado.
* Los complementos deben tener funcionalidades completas y no pueden ofrecer solo funcionalidades simples.
* Deben proporcionar una introducción completa a la funcionalidad y documentación.
* Los complementos no pueden contener submercados.
* No se pueden incluir en el complemento ningún texto o enlaces promocionales.

## Identificación de complementos de aplicaciones
Cada complemento de aplicación tiene una identificación única, la cual está compuesta por letras. Esta identificación afecta al nombre del directorio donde se encuentra el código fuente del complemento, al espacio de nombres de la clase y al prefijo de la tabla de la base de datos del complemento.

Por ejemplo, si el desarrollador utiliza "foo" como identificación del complemento, entonces el directorio donde se encuentra el código fuente del complemento será `{proyectoPrincipal}/plugin/foo`, el espacio de nombres correspondiente será `plugin\foo`, y el prefijo de la tabla será `foo_`.

Dado que la identificación es única en toda la red, los desarrolladores deben verificar la disponibilidad de la identificación antes de comenzar el desarrollo. Pueden verificar la disponibilidad en la siguiente dirección [Verificar Identificación de Aplicación](https://www.workerman.net/app/check).

## Base de datos
* Los nombres de las tablas consistirán en letras minúsculas `a-z` y guiones bajos `_`.
* Las tablas de datos del complemento deben usar el prefijo del complemento, por ejemplo, la tabla de artículos del complemento "foo" será `foo_article`.
* La clave primaria de la tabla debe ser "id".
* Debe utilizarse el motor InnoDB de forma unificada.
* Se debe utilizar el juego de caracteres utf8mb4_general_ci de forma unificada.
* Se puede utilizar el ORM de base de datos Laravel o Think-ORM.
* Se recomienda utilizar el campo de tiempo DateTime.

## Normas de codificación

#### Normas PSR
El código debe cumplir con la especificación PSR4 para la carga de clases.

#### Nomenclatura de clases en estilo CamelCase con mayúscula inicial
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Nombres en estilo CamelCase con minúscula inicial para propiedades y métodos de clase
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Métodos que no requieren autenticación
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Obtener comentarios
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Comentarios
Las propiedades y métodos de la clase deben incluir un comentario que describa la función, los parámetros y el tipo de retorno.

#### Sangría
El código debe tener una sangría de 4 espacios en lugar de tabulaciones.

#### Control de flujo
Después de palabras clave de control de flujo (if, for, while, foreach, etc.) debe haber un espacio. Las llaves que abren y cierran la estructura de control deben estar en la misma línea que la palabra clave de control.
```php
foreach ($users as $uid => $user) {

}
```

#### Nombres de variables temporales
Se sugiere que los nombres se escriban en estilo CamelCase con minúscula inicial (no es obligatorio).
```php
$articleCount = 100;
```
