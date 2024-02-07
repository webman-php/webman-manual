# Créer un plugin d'application

## Identifiant unique

Chaque plugin possède un identifiant d'application unique. Avant de commencer le développement, les développeurs doivent réfléchir à un identifiant et vérifier qu'il n'est pas déjà utilisé. 
Vous pouvez vérifier l'adresse [Vérification de l'identifiant de l'application](https://www.workerman.net/app/check)

## Création

Exécutez `composer require webman/console` pour installer la ligne de commande webman

Utilisez la commande `php webman app-plugin:create {identifiant du plugin}` pour créer un plugin d'application en local

Par exemple `php webman app-plugin:create foo`

Redémarrez webman

Accédez à `http://127.0.0.1:8787/app/foo` Si du contenu est renvoyé, cela indique que la création a réussi.
