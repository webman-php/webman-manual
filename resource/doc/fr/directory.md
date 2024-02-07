# Structure du répertoire
```
.
├── app                           Répertoire des applications
│   ├── controller                Répertoire des contrôleurs
│   ├── model                     Répertoire des modèles
│   ├── view                      Répertoire des vues
│   ├── middleware                Répertoire des middleware
│   │   └── StaticFile.php        Middleware de fichier statique intégré
|   └── functions.php             Les fonctions commerciales personnalisées sont écrites dans ce fichier
|
├── config                        Répertoire de configuration
│   ├── app.php                   Configuration de l'application
│   ├── autoload.php              Les fichiers configurés ici seront chargés automatiquement
│   ├── bootstrap.php             Configuration du callback s'exécutant lors du démarrage du processus onWorkerStart
│   ├── container.php             Configuration du conteneur
│   ├── dependence.php            Configuration des dépendances du conteneur
│   ├── database.php              Configuration de la base de données
│   ├── exception.php             Configuration des exceptions
│   ├── log.php                   Configuration des journaux
│   ├── middleware.php            Configuration des middleware
│   ├── process.php               Configuration du processus personnalisé
│   ├── redis.php                 Configuration de Redis
│   ├── route.php                 Configuration de la route
│   ├── server.php                Configuration du serveur (port, nombre de processus, etc.)
│   ├── view.php                  Configuration de la vue
│   ├── static.php                Activation des fichiers statiques et configuration du middleware de fichiers statiques
│   ├── translation.php           Configuration multilingue
│   └── session.php               Configuration de la session
├── public                        Répertoire des ressources statiques
├── process                       Répertoire du processus personnalisé
├── runtime                       Répertoire d'exécution de l'application, nécessite des autorisations en écriture
├── start.php                     Fichier de démarrage du service
├── vendor                        Répertoire des bibliothèques tierces installées par Composer
└── support                       Adaptation de la bibliothèque (y compris les bibliothèques tierces)
    ├── Request.php               Classe de requête
    ├── Response.php              Classe de réponse
    ├── Plugin.php                Script d'installation et de désinstallation du plugin
    ├── helpers.php               Fonctions d'aide (veuillez écrire les fonctions commerciales personnalisées dans app/functions.php)
    └── bootstrap.php             Script d'initialisation après le démarrage du processus
```
