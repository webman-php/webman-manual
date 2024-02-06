# Sécurité

## Utilisateur d'exécution
Il est recommandé de définir l'utilisateur d'exécution comme un utilisateur de bas niveau, par exemple identique à l'utilisateur d'exécution de nginx. L'utilisateur d'exécution est configuré dans `config/server.php` sous les paramètres `user` et `group`. De même, l'utilisateur pour les processus personnalisés est spécifié dans `config/process.php` sous les paramètres `user` et `group`. Il est important de noter que le processus de surveillance ne doit pas avoir d'utilisateur d'exécution défini, car il nécessite des privilèges élevés pour fonctionner correctement.

## Normes des contrôleurs
Le répertoire `controller` ou ses sous-répertoires ne doivent contenir que des fichiers de contrôleur. Il est interdit d'y placer d'autres types de fichiers, sinon, en l'absence de [suffixe de contrôleur](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), il est possible que des fichiers de classe soient illégalement accessibles via l'URL, entraînant des conséquences imprévisibles. Par exemple, le fichier `app/controller/model/User.php` est en réalité une classe de modèle, mais s'il est accidentellement placé dans le répertoire `controller`, sans activer le [suffixe de contrôleur](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), cela permettrait aux utilisateurs d'accéder à n'importe quelle méthode de `User.php` via des URL de type `/model/user/xxx`. Pour éviter totalement cette situation, il est fortement recommandé d'utiliser le [suffixe de contrôleur](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) pour marquer clairement quels fichiers sont des fichiers de contrôleur.

## Filtrage XSS
Pour des raisons de généralité, webman ne réalise pas d'échappement XSS des requêtes. webman recommande fortement de réaliser l'échappement XSS lors du rendu, plutôt que lors de l'insertion en base de données. De plus, des modèles tels que twig, blade, think-template, etc., effectueront automatiquement l'échappement XSS, ce qui est très pratique et ne nécessite aucune opération manuelle.

> **Conseil**
> Si vous réalisez l'échappement XSS avant l'insertion en base de données, il est fort probable que cela entraînera des problèmes d'incompatibilité avec certains plugins d'application.

## Prévention des injections SQL
Pour prévenir les injections SQL, il est fortement recommandé d'utiliser des ORM autant que possible, tels que [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) ou [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), et d'éviter d'assembler soi-même des requêtes SQL.

## Proxy nginx
Lorsque votre application doit être exposée aux utilisateurs externes, il est fortement recommandé d'ajouter un proxy nginx en amont de webman. Cela permet de filtrer certaines requêtes HTTP illégales et d'améliorer la sécurité. Pour plus de détails, veuillez consulter [le proxy nginx](nginx-proxy.md).
