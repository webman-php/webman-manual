# Empaquetage binaire

webman prend en charge l'emballage d'un projet dans un fichier binaire, ce qui permet à webman de s'exécuter sur un système Linux sans environnement PHP.

> **Remarque** Lorsque le fichier est empaqueté, il ne prend actuellement en charge que l'exécution sur des systèmes Linux x86_64 et ne prend pas en charge les systèmes Mac. Vous devez désactiver l'option de configuration phar dans `php.ini`, en définissant ainsi `phar.readonly = 0`.

## Installation de l'outil en ligne de commande
`composer require webman/console ^1.2.24`

## Configuration
Ouvrez le fichier `config/plugin/webman/console/app.php` et définissez 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Cela permet d'exclure certains répertoires et fichiers inutiles lors de l'emballage, afin d'éviter une taille excessive du paquet.

## Empaquetage
Exécutez la commande
```bash
php webman build:bin
```
Vous pouvez également spécifier la version de PHP à utiliser pour l'emballage, par exemple
```bash
php webman build:bin 8.1
```

Après l'emballage, un fichier `webman.bin` sera généré dans le répertoire `build`.

## Démarrage
Téléchargez le fichier webman.bin sur le serveur Linux, exécutez `./webman.bin start` ou `./webman.bin start -d` pour démarrer.

## Mécanisme
* Tout d'abord, le projet webman local est empaqueté dans un fichier phar
* Ensuite, le fichier php8.x.micro.sfx est téléchargé à distance sur l'ordinateur local
* Le fichier php8.x.micro.sfx et le fichier phar sont concaténés pour former un fichier binaire

## Points à retenir
* Toutes les versions de PHP locales supérieures ou égales à 7.2 peuvent exécuter la commande d'empaquetage
* Cependant, seul un fichier binaire PHP 8 peut être empaqueté
* Il est fortement recommandé que la version de PHP locale soit identique à la version d'empaquetage, par exemple si PHP local est 8.0, l'empaquetage doit également utiliser PHP 8.0 pour éviter les problèmes de compatibilité
* L'emballage télécharge le code source de PHP 8, mais ne l'installe pas localement, ce qui n'affectera pas l'environnement PHP local
* webman.bin ne fonctionne actuellement que sur des systèmes Linux x86_64 et ne fonctionne pas sur les systèmes Mac
* Par défaut, le fichier env n'est pas empaqueté (contrôlé par exclude_files dans `config/plugin/webman/console/app.php`), donc au démarrage, le fichier env doit être placé dans le même répertoire que webman.bin
* Un répertoire runtime est généré dans le répertoire où se trouve webman.bin pendant l'exécution, pour stocker les fichiers journaux
* Actuellement, webman.bin ne lit pas de fichier php.ini externe. Si vous devez personnaliser php.ini, veuillez utiliser le paramètre custom_ini dans le fichier `/config/plugin/webman/console/app.php`

## Téléchargement séparé de PHP statique
Parfois, vous ne voulez que déployer l'environnement PHP, vous avez juste besoin d'un fichier exécutable PHP. Cliquez ici pour télécharger le [téléchargement de PHP statique](https://www.workerman.net/download)

> **Remarque**
> Pour spécifier un fichier php.ini pour PHP statique, veuillez utiliser la commande suivante: `php -c /your/path/php.ini start.php start -d`

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
