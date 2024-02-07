# Procédure de mise à niveau

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **Remarque**
> Étant donné que le proxy Composer d'Alibaba Cloud a cessé de synchroniser les données depuis la source officielle de Composer, il n'est actuellement pas possible de mettre à jour Webman vers la dernière version en utilisant le proxy Alibaba Cloud Composer. Veuillez utiliser la commande suivante `composer config -g --unset repos.packagist` pour restaurer l'utilisation de la source de données officielle de Composer.
