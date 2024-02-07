# Empaquetado phar

Phar es un tipo de archivo de empaquetado en PHP similar a JAR, que te permite empaquetar tu proyecto webman en un único archivo phar para facilitar su implementación.

**Un agradecimiento especial a [fuzqing](https://github.com/fuzqing) por su PR.**

> **Nota**
> Es necesario desactivar la configuración de phar en `php.ini`, configurando `phar.readonly = 0`

## Instalación de la herramienta de línea de comandos
`composer require webman/console`

## Configuración
Abre el archivo `config/plugin/webman/console/app.php` y establece `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` para excluir ciertos directorios y archivos inútiles al empaquetar, evitando que el tamaño del paquete sea excesivo.

## Empaquetado
Ejecuta el comando `php webman phar:pack` en el directorio raíz del proyecto webman. Esto generará un archivo `webman.phar` en el directorio de construcción (build).

> La configuración relacionada con el empaquetado se encuentra en `config/plugin/webman/console/app.php`

## Comandos de inicio y detención
**Inicio**
`php webman.phar start` o `php webman.phar start -d`

**Detención**
`php webman.phar stop`

**Ver estado**
`php webman.phar status`

**Ver estado de las conexiones**
`php webman.phar connections`

**Reinicio**
`php webman.phar restart` o `php webman.phar restart -d`

## Notas
* Al ejecutar webman.phar, se generará un directorio `runtime` en el mismo directorio que webman.phar, utilizado para almacenar archivos temporales como registros.

* Si tu proyecto utiliza un archivo .env, deberás colocar el archivo .env en el mismo directorio que webman.phar.

* Si tu aplicación necesita cargar archivos en el directorio público, también debes separar el directorio público en el mismo directorio que webman.phar, y en este caso deberás configurar `config/app.php`.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Tu aplicación puede usar la función de ayuda `public_path()` para encontrar la ubicación real del directorio público.

* webman.phar no es compatible con la ejecución de procesos personalizados en Windows.
