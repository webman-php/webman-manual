# Sécurité

## Utilisateur d'exécution
Il est recommandé de définir l'utilisateur d'exécution comme un utilisateur avec des autorisations plus basses, par exemple le même utilisateur que nginx. L'utilisateur d'exécution est configuré dans `config/server.php` dans les sections `user` et `group`. De même, l'utilisateur des processus personnalisés est spécifié via `user` et `group` dans `config/process.php`. Il est à noter que le processus de surveillance ne doit pas avoir d'utilisateur d'exécution spécifié, car il a besoin de permissions élevées pour fonctionner correctement.

## Normes des contrôleurs
Le répertoire `controller` ou ses sous-répertoires ne doivent contenir que des fichiers de contrôleurs, les autres types de fichiers étant interdits. Sinon, en l'absence de [suffixe de contrôleur](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), il se peut que des fichiers de classe soient accédés de manière non autorisée via l'URL, ce qui pourrait entraîner des conséquences imprévisibles. Par exemple, le fichier `app/controller/model/User.php` est en réalité une classe Model, mais il a été accidentellement placé dans le répertoire `controller`. En l'absence de [suffixe de contrôleur](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), cela permettrait aux utilisateurs d'accéder à n'importe quelle méthode de `User.php` via des URL telles que `/model/user/xxx`. Pour éviter complètement ce genre de situation, il est fortement recommandé d'utiliser [le suffixe de contrôleur](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) pour marquer clairement les fichiers de contrôleurs.

## Filtrage XSS
Pour des raisons de généralité, webman ne filtre pas les requêtes XSS. webman recommande fortement de réaliser le filtrage XSS lors du rendu, plutôt que lors de l'entrée des données. De plus, les modèles tels que Twig, Blade, think-template effectuent automatiquement le filtrage XSS, ce qui rend la tâche très pratique.

> **Conseil**
> Si vous effectuez le filtrage XSS avant l'entrée des données, cela risque de causer des problèmes d'incompatibilité avec certains plugins de l'application.

## Prévention des injections SQL
Pour prévenir les injections SQL, il est fortement recommandé d'utiliser des ORM, tels que [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) ou [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), et d'éviter de composer vous-même des requêtes SQL.

## Proxy nginx
Lorsque votre application doit être exposée aux utilisateurs externes, il est fortement recommandé d'ajouter un proxy nginx en amont de webman, ce qui permet de filtrer certaines requêtes HTTP non autorisées et d'améliorer la sécurité. Pour plus de détails, veuillez consulter le document sur [le proxy nginx](nginx-proxy.md).
