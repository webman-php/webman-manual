# Empaquetado binario

webman admite empaquetar un proyecto en un archivo binario, lo que permite que webman se ejecute en sistemas Linux sin necesidad de un entorno PHP.

> **Nota**
> El archivo empaquetado solo es compatible con la arquitectura x86_64 en sistemas Linux, no es compatible con sistemas Mac.
> Es necesario deshabilitar la opción de configuración de phar en `php.ini`, es decir, establecer `phar.readonly = 0`.

## Instalación de la herramienta de línea de comandos
Ejecutar `composer require webman/console ^1.2.24`

## Configuración
Abrir el archivo `config/plugin/webman/console/app.php` y configurar 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
para excluir algunos directorios y archivos inútiles durante el empaquetado, evitando un tamaño excesivo del paquete.

## Empaquetado
Ejecutar el comando
```
php webman build:bin
```
También se puede especificar con qué versión de PHP empaquetar, por ejemplo
```
php webman build:bin 8.1
```

Después de empaquetar, se generará un archivo `webman.bin` en el directorio `build`.

## Inicio
Subir el archivo `webman.bin` al servidor Linux y ejecutar `./webman.bin start` o `./webman.bin start -d` para iniciar.

## Principio
- El proyecto de webman local se empaqueta en un archivo phar.
- Luego, se descarga el archivo php8.x.micro.sfx a nivel remoto.
- Finalmente, se concatenan el archivo php8.x.micro.sfx y el archivo phar para obtener un archivo binario.

## Consideraciones
- Se puede ejecutar el comando de empaquetado si la versión local de PHP es >=7.2
- Sin embargo, solo se puede empaquetar en un archivo binario de PHP8
- Se recomienda encarecidamente que la versión local de PHP y la versión de empaquetado coincidan. Es decir, si la versión local es PHP8.0, también se debe utilizar PHP8.0 para el empaquetado, para evitar problemas de compatibilidad.
- Durante el empaquetado se descarga el código fuente de PHP8, pero no se instala localmente, por lo que no afectará el entorno PHP local.
- Actualmente, `webman.bin` solo es compatible con sistemas Linux de arquitectura x86_64 y no es compatible con sistemas Mac.
- Por defecto, no se empaqueta el archivo `env` (controlado por `exclude_files` en `config/plugin/webman/console/app.php`), por lo que el archivo `env` debe colocarse en el mismo directorio que `webman.bin` al iniciar.
- Durante la ejecución, se generará un directorio `runtime` en el mismo directorio que `webman.bin`, utilizado para almacenar archivos de registro.
- Actualmente, `webman.bin` no leerá archivos `php.ini` externos. Si se requiere personalizar `php.ini`, se debe configurar en el archivo `/config/plugin/webman/console/app.php` en `custom_ini`.

## Descargar PHP estático por separado
A veces, solo se desea desplegar un archivo ejecutable de PHP sin configurar un entorno PHP completo. En ese caso, se puede descargar el [PHP estático aquí](https://www.workerman.net/download).

> **Consejo**
> Si se desea especificar un archivo `php.ini` para el PHP estático, se debe usar el siguiente comando: `php -c /tu/ruta/php.ini start.php start -d`

## Extensiones admitidas
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## Fuente del proyecto
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
