# Empaquetado binario

webman permite empaquetar el proyecto en un único archivo binario, de modo que puede ejecutarse en Linux sin entorno PHP.

> **Nota**
> El archivo empaquetado solo soporta ejecución en sistemas Linux con arquitectura x86_64. No soporta Windows ni macOS.
> Debe desactivar la opción phar en `php.ini`, estableciendo `phar.readonly = 0`.

## Instalar herramienta de línea de comandos
`composer require webman/console`

## Empaquetado
Ejecute el comando
```
php webman build:bin
```
También puede indicar la versión de PHP con la que empaquetar, por ejemplo
```
php webman build:bin 8.3
```

Tras empaquetar, se generará un archivo `webman.bin` en el directorio build.

## Arranque
Suba webman.bin al servidor Linux y ejecute `./webman.bin start` o `./webman.bin start -d` para iniciar.

## Principio
* Primero se empaqueta el proyecto webman local en un archivo phar
* Después se descarga remotamente php8.x.micro.sfx
* Se concatenan php8.x.micro.sfx y el archivo phar en un único binario

## Consideraciones
* Se recomienda usar la misma versión de PHP local y para empaquetar (p. ej. PHP 8.3 en ambos) para evitar problemas de compatibilidad
* El empaquetado descargará el código fuente de PHP 8 pero no lo instalará localmente, por lo que no afecta al entorno PHP local
* webman.bin solo soporta ejecución en Linux x86_64 y no soporta macOS
* Los proyectos empaquetados no soportan reload; las actualizaciones de código requieren reinicio
* Por defecto no se empaqueta el archivo env (controlado por exclude_files en `config/plugin/webman/console/app.php`). Al arrancar, el archivo env debe estar en el mismo directorio que webman.bin
* Durante la ejecución se crea un directorio runtime en el directorio de webman.bin para almacenar logs
* webman.bin actualmente no lee archivos php.ini externos. Para personalizar php.ini, configúrelo en custom_ini en `config/plugin/webman/console/app.php`
* Algunos archivos no necesitan empaquetarse; puede configurar exclusiones en `config/plugin/webman/console/app.php` para evitar paquetes demasiado grandes
* El empaquetado binario no soporta corrutinas Swoole
* No almacene nunca archivos subidos por usuarios dentro del paquete binario. Operar con ellos vía el protocolo `phar://` es muy peligroso (vulnerabilidad de deserialización phar). Los archivos subidos deben almacenarse aparte en disco fuera del paquete
* Si su aplicación necesita subir archivos al directorio public, coloque el directorio public junto a webman.bin, configure `config/app.php` así y vuelva a empaquetar:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Descargar PHP estático por separado
Si solo necesita un ejecutable PHP sin desplegar un entorno PHP completo, [descargue PHP estático aquí](https://www.workerman.net/download).

> **Consejo**
> Para especificar un archivo php.ini para PHP estático: `php -c /your/path/php.ini start.php start -d`

## Extensiones soportadas
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Origen del proyecto

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
