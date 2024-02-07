# Performance de webman


### Processus de traitement des requêtes des frameworks traditionnels

1. nginx/apache reçoit la demande
2. nginx/apache transmet la demande à php-fpm
3. php-fpm initialise l'environnement, comme la création de la liste des variables
4. php-fpm appelle RINIT des différentes extensions/modules
5. php-fpm lit le fichier php du disque (l'utilisation d'opcache peut être évitée)
6. php-fpm analyse lexicale, analyse syntaxique, compilation en opcodes (l'utilisation d'opcache peut être évitée)
7. php-fpm exécute les opcodes incluant 8, 9, 10, 11
8. Initialisation du framework, comme l'instanciation de plusieurs classes, y compris le conteneur, le contrôleur, le routage, les middlewares, etc.
9. Le framework se connecte à la base de données et effectue une vérification des autorisations, se connecte à redis
10. Le framework exécute la logique métier
11. Le framework ferme les connexions à la base de données et redis
12. php-fpm libère les ressources, détruit toutes les définitions de classe, les instances, détruit le tableau des symboles, etc.
13. php-fpm appelle séquentiellement les méthodes RSHUTDOWN des différentes extensions/modules
14. php-fpm transmet les résultats à nginx/apache
15. nginx/apache renvoie les résultats au client


### Processus de traitement de la demande de webman
1. Le framework reçoit la demande
2. Le framework exécute la logique métier
3. Le framework renvoie les résultats au client

Oui, sans la possibilité de reverse proxy avec nginx, le framework se déroule en seulement ces 3 étapes. On peut dire que c'est déjà l'apogée des frameworks PHP, ce qui fait que les performances de webman sont plusieurs fois voire plusieurs dizaines de fois supérieures à celles des frameworks traditionnels.

Pour plus d'informations, voir [les tests de stress](benchmarks.md)
