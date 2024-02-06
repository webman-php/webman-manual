# Empaquetar en phar

Phar es un tipo de archivo de empaquetado en PHP similar a JAR que te permite empaquetar tu proyecto webman en un único archivo phar para facilitar su despliegue.

**Agradecimientos especiales a [fuzqing](https://github.com/fuzqing) por su PR.**

> **Nota**
> Es necesario desactivar la configuración de phar en `php.ini`, es decir, establecer `phar.readonly = 0`

## Instalar la herramienta de línea de comandos
`composer require webman/console`

## Configuración
Abre el archivo `config/plugin/webman/console/app.php` y configura `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`. Esto permite excluir algunos directorios y archivos inútiles al empacar, evitando que el tamaño del paquete sea demasiado grande.

## Empaquetar
Ejecuta el comando `php webman phar:pack` en el directorio raíz del proyecto webman. Se generará un archivo `webman.phar` en el directorio de construcción.

> La configuración relacionada con el empaquetado se encuentra en `config/plugin/webman/console/app.php`

## Comandos para iniciar y detener
**Iniciar**
`php webman.phar start` o `php webman.phar start -d`

**Detener**
`php webman.phar stop`

**Ver estado**
`php webman.phar status`

**Ver estado de las conexiones**
`php webman.phar connections`

**Reiniciar**
`php webman.phar restart` o `php webman.phar restart -d`

## Notas
* Al ejecutar webman.phar, se creará un directorio `runtime` en el directorio donde se encuentra webman.phar, utilizado para almacenar archivos temporales como registros.

* Si tu proyecto utiliza un archivo .env, debes colocar el archivo .env en el directorio donde se encuentra webman.phar.

* Si tu negocio requiere cargar archivos en el directorio público, también debes separar el directorio `public` y colocarlo en el mismo directorio que webman.phar. En este caso, necesitarás configurar `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
El negocio puede utilizar la función auxiliar `public_path()` para encontrar la ubicación real del directorio público.

* webman.phar no es compatible con la creación de procesos personalizados en Windows.
