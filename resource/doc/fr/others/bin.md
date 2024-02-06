# Emballage binaire

Webman prend en charge la possibilité de rassembler un projet dans un fichier binaire, ce qui permet à Webman de s'exécuter sur un système Linux sans l'environnement PHP.

> **Remarque**
> Le fichier emballé ne peut actuellement être exécuté que sur un système Linux x86_64, et ne prend pas en charge le système Mac
> Vous devez désactiver l'option de configuration phar dans `php.ini`, soit en définissant `phar.readonly = 0`

## Installation de l'outil en ligne de commande
`composer require webman/console ^1.2.24`

## Configuration
Ouvrez le fichier `config/plugin/webman/console/app.php` et définissez 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
pour exclure certains répertoires et fichiers inutiles lors de l'emballage, afin d'éviter une taille de fichier trop importante.

## Emballage
Exécutez la commande
```
php webman build:bin
```
Vous pouvez également spécifier la version de PHP avec laquelle emballer, par exemple
```
php webman build:bin 8.1
```

Un fichier `webman.bin` sera généré dans le répertoire de construction une fois l'emballage terminé.

## Démarrage
Téléchargez le fichier webman.bin sur le serveur Linux, et exécutez `./webman.bin start` ou `./webman.bin start -d` pour démarrer.

## Principe
* D'abord, le projet Webman local est compilé en un fichier phar
* Ensuite, le fichier php8.x.micro.sfx est téléchargé depuis un serveur distant et ajouté localement
* Enfin, php8.x.micro.sfx et le fichier phar sont concaténés en un fichier binaire

## Points d'attention
* Les versions locales de PHP> = 7.2 peuvent exécuter la commande d'emballage
* Seule la version binaire de PHP 8 peut être créée
* Il est fortement recommandé de conserver la cohérence entre la version locale de PHP et la version d'emballage pour éviter les problèmes de compatibilité
* L'emballage téléchargera le code source de PHP 8, mais ne l'installera pas localement, ce qui n'affecte pas l'environnement PHP local
* webman.bin est actuellement pris en charge uniquement sur les systèmes Linux x86_64, et non sur les systèmes Mac
* Par défaut, le fichier env n'est pas inclus dans l'emballage (`config/plugin/webman/console/app.php` contrôle exclude_files), donc le fichier env doit être placé dans le même répertoire que webman.bin au démarrage
* Un répertoire runtime sera généré dans le répertoire webman.bin lors de l'exécution, pour stocker les fichiers journal
* Actuellement, webman.bin ne lit pas de fichier php.ini externe; pour personnaliser php.ini, veuillez configurer dans le fichier `/config/plugin/webman/console/app.php`

## Téléchargement de PHP statique séparément
Parfois, vous n'avez pas besoin de déployer l'environnement PHP, vous avez seulement besoin d'un fichier exécutable PHP. Cliquez ici pour télécharger [PHP statique](https://www.workerman.net/download).

> **Note**
> Si vous devez spécifier un fichier php.ini pour PHP statique, veuillez utiliser la commande suivante `php -c /your/path/php.ini start.php start -d`

## Extensions prises en charge
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

## Source du projet

https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
