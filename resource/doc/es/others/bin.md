# Empaquetado binario

webman es compatible con la creación de un proyecto en un archivo binario, lo que permite que webman se ejecute en sistemas Linux sin necesidad de entorno PHP.

> **Nota**
> El archivo empaquetado solo es compatible con sistemas Linux de arquitectura x86_64, no es compatible con sistemas Mac.
> Se requiere desactivar la opción de configuración 'phar' en `php.ini`, es decir, establecer `phar.readonly = 0`.

## Instalación de la herramienta de línea de comandos
`composer require webman/console ^1.2.24`

## Configuración
Abrir el archivo `config/plugin/webman/console/app.php` y configurar 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Esto se utiliza para excluir algunos directorios y archivos innecesarios durante el empaquetado, evitando que el tamaño del paquete sea demasiado grande.

## Empaquetado
Ejecutar el comando
```bash
php webman build:bin
```
También se puede especificar la versión de PHP con la que se realiza el empaquetado, por ejemplo
```bash
php webman build:bin 8.1
```

Tras el empaquetado, se generará un archivo `webman.bin` en el directorio de construcción (build).

## Inicio
Subir el archivo webman.bin al servidor Linux, ejecutar `./webman.bin start` o `./webman.bin start -d` para iniciar.

## Principio
* Primero se empaqueta el proyecto webman local en un archivo phar.
* Luego se descarga a distancia php8.x.micro.sfx al local.
* Se concatenan php8.x.micro.sfx y el archivo phar en un archivo binario.

## Consideraciones
* Se puede ejecutar el comando de empaquetado con una versión de PHP local >= 7.2.
* Sin embargo, solo se puede empaquetar en un archivo binario de PHP8.
* Se recomienda encarecidamente que la versión local de PHP y la versión de empaquetado sean compatibles, es decir, si el local es PHP8.0, el empaquetado también debe ser con PHP8.0, para evitar problemas de compatibilidad.
* El empaquetado descargará el código fuente de PHP8, pero no se instalará localmente, por lo que no afectará al entorno PHP local.
* En la actualidad, webman.bin solo es compatible con sistemas Linux de arquitectura x86_64, no es compatible con sistemas Mac.
* Por defecto, el archivo env no se empaqueta (`config/plugin/webman/console/app.php` controla exclude_files), por lo que al iniciar, el archivo env debe colocarse en el mismo directorio que webman.bin.
* Durante la ejecución, se generará un directorio de runtime en el directorio donde se encuentra webman.bin, que se utiliza para almacenar archivos de registro.
* Actualmente, webman.bin no leerá archivos php.ini externos, si se desea personalizar php.ini, se debe configurar en el archivo `/config/plugin/webman/console/app.php` en custom_ini.

## Descarga de PHP estático por separado
A veces, solo se necesita un archivo ejecutable de PHP sin necesidad de implementar un entorno PHP, haz clic [aquí](https://www.workerman.net/download) para descargar PHP estático.

> **Consejo**
> Si es necesario especificar un archivo php.ini para PHP estático, se debe utilizar el siguiente comando `php -c /your/path/php.ini start.php start -d`.

## Extensiones compatibles
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

## Origen del Proyecto
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
