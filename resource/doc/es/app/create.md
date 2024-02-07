# Crear un complemento de aplicación

## Identificación única

Cada complemento tiene una identificación de aplicación única. Antes de desarrollar un complemento, el desarrollador debe pensar en una identificación y comprobar que no esté ocupada. Puede verificar la identificación en la dirección [Verificación de identificación de la aplicación](https://www.workerman.net/app/check)

## Creación

Ejecutar `composer require webman/console` para instalar la interfaz de comandos de webman.

Usar el comando `php webman app-plugin:create {identificación del complemento}` para crear un complemento de aplicación local.

Por ejemplo, `php webman app-plugin:create foo`

Reiniciar webman

Acceder a `http://127.0.0.1:8787/app/foo`. Si hay contenido devuelto, significa que se ha creado con éxito.
