# Processus d'exécution

## Processus de démarrage du programme

Après avoir exécuté php start.php start, le processus d'exécution est le suivant :

1. Chargement des configurations dans le répertoire config/
2. Configuration des paramètres pertinents du Worker tels que `pid_file`, `stdout_file`, `log_file`, `max_package_size`, etc.
3. Création du processus webman et écoute du port (par défaut 8787)
4. Création de processus personnalisés en fonction des configurations
5. Une fois les processus webman et personnalisés démarrés, les opérations suivantes sont exécutées (toutes dans la méthode onWorkerStart) :
   ① Chargement des fichiers définis dans `config/autoload.php`, tels que `app/functions.php`
   ② Chargement des middlewares définis dans `config/middleware.php` (y compris `config/plugin/*/*/middleware.php`)
   ③ Exécution de la méthode start des classes définies dans `config/bootstrap.php` (y compris `config/plugin/*/*/bootstrap.php`), utilisée pour initialiser certains modules comme la connexion à la base de données Laravel
   ④ Chargement des routes définies dans `config/route.php` (y compris `config/plugin/*/*/route.php`)

## Processus de traitement des requêtes

1. Vérification si l'URL de la requête correspond à un fichier statique dans le répertoire public. Si oui, renvoi du fichier (fin de la requête). Sinon, passage à l'étape 2.
2. Vérification si l'URL correspond à une route existante. Si non, passage à l'étape 3. Sinon, passage à l'étape 4.
3. Vérification si les routes par défaut sont désactivées. Si oui, renvoi de l'erreur 404 (fin de la requête). Sinon, passage à l'étape 4.
4. Recherche et exécution des middlewares du contrôleur correspondant à la requête, exécution des opérations préalables des middlewares dans l'ordre (phase de requête du modèle de l'oignon), exécution de la logique métier du contrôleur, exécution des opérations postérieures des middlewares (phase de réponse du modèle de l'oignon), puis fin de la requête. (Voir le [modèle de l'oignon des middlewares](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
