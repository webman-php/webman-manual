# Crear un complemento de la aplicación

## Identificación única

Cada complemento tiene una identificación única de la aplicación. Antes de que los desarrolladores comiencen a desarrollar, necesitan pensar en una identificación y verificar que no esté ocupada. Verifique la identificación en la dirección [Comprobación de identificación de la aplicación](https://www.workerman.net/app/check)

## Creación

Ejecute `composer require webman/console` para instalar la línea de comandos de webman.

Utilice el comando `php webman app-plugin:create {identificación del complemento}` para crear un complemento de la aplicación localmente.

Por ejemplo, `php webman app-plugin:create foo`

Reinicie webman.

Visite `http://127.0.0.1:8787/app/foo`. Si hay contenido devuelto, significa que se ha creado con éxito.
