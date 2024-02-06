# Empaquetage phar

Phar est un type de fichier d'empaquetage similaire à JAR dans PHP, vous pouvez utiliser phar pour empaqueter votre projet webman dans un seul fichier phar pour faciliter le déploiement.

**Nous tenons à remercier [fuzqing](https://github.com/fuzqing) pour sa PR.**

> **Remarque**
> Vous devez désactiver l'option de configuration phar dans `php.ini`, en définissant `phar.readonly = 0`

## Installation de l'outil en ligne de commande
`composer require webman/console`

## Configuration
Ouvrez le fichier `config/plugin/webman/console/app.php` et définissez `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`, pour exclure certains répertoires et fichiers inutiles lors de l'empaquetage afin d'éviter une taille de paquet trop grande.

## Empaquetage
Exécutez la commande `php webman phar:pack` dans le répertoire racine du projet webman.
Cela générera un fichier `webman.phar` dans le répertoire de construction.

> Les configurations d'empaquetage se trouvent dans `config/plugin/webman/console/app.php`.

## Commandes de démarrage et d'arrêt
**Démarrer**
`php webman.phar start` ou `php webman.phar start -d`

**Arrêter**
`php webman.phar stop`

**Vérifier l'état**
`php webman.phar status`

**Vérifier les connexions**
`php webman.phar connections`

**Redémarrer**
`php webman.phar restart` ou `php webman.phar restart -d`

## Remarque
* Lorsque vous exécutez webman.phar, un répertoire runtime sera généré dans le répertoire où se trouve webman.phar, pour stocker les fichiers temporaires tels que les journaux.

* Si votre projet utilise un fichier .env, vous devez placer le fichier .env dans le répertoire où se trouve webman.phar.

* Si votre application a besoin de téléverser des fichiers dans le répertoire public, vous devez également séparer le répertoire public et le placer dans le répertoire où se trouve webman.phar, et ensuite configurer `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
L'application peut utiliser la fonction d'aide `public_path()` pour trouver l'emplacement réel du répertoire public.

* webman.phar ne prend pas en charge l'activation de processus personnalisés sous Windows.
