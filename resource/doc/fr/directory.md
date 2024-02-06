# Structure des répertoires
```
.
├── app                           Répertoire de l'application
│   ├── controller                Répertoire des contrôleurs
│   ├── model                     Répertoire des modèles
│   ├── view                      Répertoire des vues
│   ├── middleware                Répertoire des middlewares
│   │   └── StaticFile.php        Middleware pour les fichiers statiques inclus
|   └── functions.php             Les fonctions métier personnalisées sont écrites dans ce fichier
|
├── config                        Répertoire de configuration
│   ├── app.php                   Configuration de l'application
│   ├── autoload.php              Les fichiers configurés ici seront automatiquement chargés
│   ├── bootstrap.php             Configuration de rappel exécutée lors du démarrage du processus onWorkerStart
│   ├── container.php             Configuration du conteneur
│   ├── dependence.php            Configuration des dépendances du conteneur
│   ├── database.php              Configuration de la base de données
│   ├── exception.php             Configuration des exceptions
│   ├── log.php                   Configuration des journaux
│   ├── middleware.php            Configuration des middlewares
│   ├── process.php               Configuration des processus personnalisés
│   ├── redis.php                 Configuration de redis
│   ├── route.php                 Configuration des routes
│   ├── server.php                Configuration du serveur (port, nombre de processus, etc.)
│   ├── view.php                  Configuration des vues
│   ├── static.php                Activation des fichiers statiques et configuration des middlewares pour les fichiers statiques
│   ├── translation.php           Configuration du multilinguisme
│   └── session.php               Configuration de la session
├── public                        Répertoire des ressources statiques
├── process                       Répertoire des processus personnalisés
├── runtime                       Répertoire d'exécution de l'application, nécessite des autorisations en écriture
├── start.php                     Fichier de démarrage du service
├── vendor                        Répertoire des bibliothèques tierces installées via composer
└── support                       Adaptation des bibliothèques (y compris les bibliothèques tierces)
    ├── Request.php               Classe de demande
    ├── Response.php              Classe de réponse
    ├── Plugin.php                Scripts d'installation et de désinstallation des plugins
    ├── helpers.php               Fonctions d'aide (les fonctions métier personnalisées doivent être écrites dans app/functions.php)
    └── bootstrap.php             Script d'initialisation après le démarrage du processus
```
