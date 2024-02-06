# Performance de webman

### Processus de traitement des requêtes avec des frameworks traditionnels

1. Le serveur nginx/apache reçoit la requête.
2. Le serveur nginx/apache transmet la requête à php-fpm.
3. Php-fpm initialise l'environnement, tel que la création d'une liste de variables.
4. Php-fpm appelle RINIT des différentes extensions/modules.
5. Php-fpm lit le fichier php sur le disque (évitable avec opcache).
6. Php-fpm analyse lexicale, analyse syntaxique, compile en opcodes (évitable avec opcache).
7. Php-fpm exécute les opcodes, incluant 8, 9, 10, 11.
8. Initialisation du framework, tel que l'instanciation de différentes classes comme des conteneurs, des contrôleurs, des routes, des middlewares, etc.
9. Le framework se connecte à la base de données et effectue une vérification des permissions, se connecte à redis.
10. Le framework exécute la logique métier.
11. Le framework ferme la connexion à la base de données et à redis.
12. Php-fpm libère les ressources, détruit toutes les définitions de classe, instances, le tableau des symboles, etc.
13. Php-fpm appelle séquentiellement les méthodes RSHUTDOWN des différentes extensions/modules.
14. Php-fpm transmet le résultat à nginx/apache.
15. Le serveur nginx/apache renvoie le résultat au client.

### Processus de traitement des requêtes avec webman
1. Le framework reçoit la requête.
2. Le framework exécute la logique métier.
3. Le framework renvoie le résultat au client.

Oui, sans proxy nginx, le framework n'a que ces 3 étapes. On peut dire que c'est l'ultime pour un framework PHP, ce qui fait que les performances de webman sont plusieurs fois, voire des dizaines de fois, supérieures à celles des frameworks traditionnels.

Pour plus d'informations, consultez [les tests de charge](benchmarks.md).
