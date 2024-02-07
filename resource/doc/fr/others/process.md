# Processus d'exécution

## Processus de démarrage du processus

Après avoir exécuté php start.php start, le processus d'exécution est le suivant :
1. Chargement de la configuration dans le répertoire config/
2. Configuration des paramètres du Worker tels que `pid_file`, `stdout_file`, `log_file`, `max_package_size`, etc.
3. Création du processus webman et écoute sur le port (par défaut 8787)
4. Création du processus personnalisé en fonction de la configuration
5. Après le démarrage des processus webman et personnalisés, les opérations suivantes sont effectuées (toutes exécutées dans onWorkerStart) :
   ① Chargement des fichiers définis dans `config/autoload.php`, tels que `app/functions.php`
   ② Chargement des middlewares définis dans `config/middleware.php` (y compris `config/plugin/*/*/middleware.php`)
   ③ Exécution de la méthode `start` de la classe définie dans `config/bootstrap.php` (y compris `config/plugin/*/*/bootstrap.php`) pour initialiser certains modules, comme l'initialisation de la base de données Laravel
   ④ Chargement des routes définies dans `config/route.php` (y compris `config/plugin/*/*/route.php`)

## Processus de traitement des requêtes
1. Vérification si l'URL de la requête correspond à un fichier statique dans le répertoire public. Si oui, renvoi du fichier (fin de la requête). Sinon, passage à l'étape 2.
2. Vérification si l'URL correspond à une certaine route. Si elle ne correspond pas, passe à l'étape 3 ; sinon, passe à l'étape 4.
3. Vérification si les routes par défaut sont désactivées. Si oui, renvoi d'une erreur 404 (fin de la requête). Sinon, passe à l'étape 4.
4. Recherche des middlewares correspondant à la requête, exécution des opérations préalables des middlewares dans l'ordre (phase de requête du modèle oignon), exécution de la logique métier du contrôleur, exécution des opérations ultérieures des middlewares (phase de réponse du modèle oignon), fin de la requête. (Voir le modèle de [middleware oignon](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
