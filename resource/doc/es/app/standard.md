# Normas de desarrollo de complementos de aplicaciones

## Requisitos de los complementos de la aplicación
* Los complementos no pueden contener código, iconos, imágenes u otros elementos que infrinjan derechos de autor.
* El código fuente de los complementos debe ser completo y no puede estar encriptado.
* Los complementos deben tener funcionalidades completas y no pueden ser simples.
* Deben incluir una introducción completa de sus funcionalidades y documentación.
* Los complementos no pueden incluir submercados.
* No pueden contener textos o enlaces de promoción.

## Identificación de complementos de la aplicación
Cada complemento de la aplicación tiene una identificación única, la cual está compuesta por letras. Esta identificación afecta al nombre del directorio donde se encuentra el código fuente del complemento, al espacio de nombres de la clase y al prefijo de la tabla de la base de datos del complemento.

Supongamos que el desarrollador usa "foo" como identificación del complemento. Entonces, el directorio donde se encuentra el código fuente del complemento sería `{proyecto_principial}/plugin/foo`, el espacio de nombres correspondiente sería `plugin\foo`, y el prefijo de la tabla de la base de datos sería `foo_`.

Dado que la identificación es única en toda la red, el desarrollador debe verificar su disponibilidad antes de comenzar el desarrollo. Puede hacerlo en el siguiente enlace: [Verificación de identificación de la aplicación](https://www.workerman.net/app/check).

## Base de datos
* Los nombres de las tablas deben contener letras minúsculas de la "a" a la "z" y guiones bajos "_".
* Las tablas de datos del complemento deben comenzar con el prefijo de la identificación del complemento. Por ejemplo, la tabla de artículos del complemento "foo" debería ser `foo_article`.
* La clave principal de la tabla debe ser "id".
* Se debe utilizar el motor de almacenamiento InnoDB de forma uniforme.
* Se debe utilizar el juego de caracteres utf8mb4_general_ci de forma uniforme.
* Para el mapeo objeto-relacional de la base de datos, se puede usar Laravel o Think-ORM.
* Se recomienda utilizar el campo de tiempo DateTime.

## Normas de codificación

#### Norma PSR
El código debe cumplir con la norma de cargado PSR4.

#### Nombres de clases en estilo de caja alta y notación camelCase
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Nombres de propiedades y métodos de clases en notación camelCase inicial en minúscula
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Métodos sin necesidad de autenticación
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
Las propiedades y métodos de las clases deben incluir comentarios, que detallen su resumen, parámetros y tipo de retorno.

#### Sangría
Se debe utilizar un espacio de 4 espacios para la sangría en el código, en lugar de usar tabulaciones.

#### Control de flujo
Después de las palabras clave de control de flujo (if, for, while, foreach, etc.), debe haber un espacio en blanco, y las llaves de apertura y cierre deben estar en la misma línea.
```php
foreach ($users as $uid => $user) {

}
```

#### Nombres de variables temporales
Se recomienda utilizar la notación camelCase inicial en minúscula para los nombres de las variables temporales (no es obligatorio).
```php
$articleCount = 100;
```
