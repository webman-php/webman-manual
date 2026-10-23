# Empaquetage binaire

webman permet d’empaqueter le projet en un fichier binaire unique, afin de l’exécuter sur Linux sans environnement PHP.

> **Remarque**
> Le fichier empaqueté ne fonctionne actuellement que sur des systèmes Linux x86_64. Windows et macOS ne sont pas pris en charge.
> Désactivez l’option phar dans `php.ini` en définissant `phar.readonly = 0`.

## Installer l’outil en ligne de commande
`composer require webman/console`

## Empaquetage
Exécutez la commande
```
php webman build:bin
```
Vous pouvez préciser la version de PHP utilisée pour l’empaquetage, par exemple
```
php webman build:bin 8.3
```

Après l’empaquetage, un fichier `webman.bin` est généré dans le répertoire build.

## Démarrage
Uploadez webman.bin sur votre serveur Linux et exécutez `./webman.bin start` ou `./webman.bin start -d` pour démarrer.

## Principe
* Le projet webman local est d’abord empaqueté dans un fichier phar
* Puis php8.x.micro.sfx est téléchargé à distance
* php8.x.micro.sfx et le fichier phar sont concaténés en un fichier binaire unique

## Points à retenir
* Il est fortement recommandé d’utiliser la même version de PHP en local et pour l’empaquetage (ex. PHP 8.3 pour les deux) afin d’éviter les problèmes de compatibilité
* L’empaquetage télécharge le code source de PHP 8 mais ne l’installe pas localement, donc sans impact sur l’environnement PHP local
* webman.bin ne fonctionne actuellement que sur Linux x86_64 et ne supporte pas macOS
* Les projets empaquetés ne supportent pas reload ; les mises à jour du code nécessitent un redémarrage
* Par défaut, le fichier env n’est pas empaqueté (contrôlé par exclude_files dans `config/plugin/webman/console/app.php`). Au démarrage, le fichier env doit être dans le même répertoire que webman.bin
* Un répertoire runtime est créé dans le répertoire de webman.bin pendant l’exécution pour stocker les logs
* webman.bin ne lit pas les fichiers php.ini externes. Pour personnaliser php.ini, configurez custom_ini dans `config/plugin/webman/console/app.php`
* Certains fichiers n’ont pas besoin d’être empaquetés ; configurez les exclusions dans `config/plugin/webman/console/app.php` pour éviter un paquet trop volumineux
* L’empaquetage binaire ne supporte pas les coroutines Swoole
* Ne stockez jamais les fichiers uploadés par les utilisateurs dans le paquet binaire. Opérer dessus via le protocole `phar://` est très risqué (vulnérabilité de désérialisation phar). Les fichiers uploadés doivent être stockés séparément sur disque, en dehors du paquet
* Si votre application doit envoyer des fichiers vers le répertoire public, placez le répertoire public à côté de webman.bin, configurez `config/app.php` ainsi et ré-empaquetez :
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Télécharger PHP statique séparément
Si vous avez seulement besoin d’un exécutable PHP sans déployer un environnement PHP complet, [téléchargez PHP statique ici](https://www.workerman.net/download).

> **Astuce**
> Pour spécifier un fichier php.ini pour PHP statique : `php -c /your/path/php.ini start.php start -d`

## Extensions prises en charge
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Source du projet

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
