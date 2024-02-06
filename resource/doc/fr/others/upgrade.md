# Méthode de mise à niveau

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **Remarque**
> En raison de l'arrêt de la synchronisation des données depuis la source officielle de Composer vers le proxy d'Alibaba Cloud, il est actuellement impossible de mettre à niveau vers la dernière version de webman en utilisant le proxy d'Alibaba Cloud. Veuillez utiliser la commande suivante `composer config -g --unset repos.packagist` pour rétablir l'utilisation de la source de données officielle de Composer.
