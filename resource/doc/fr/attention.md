# Informations sur la programmation

## Système d'exploitation
webman prend en charge à la fois les systèmes Linux et Windows. Cependant, étant donné que workerman ne prend pas en charge la configuration multi-processus et le processus en arrière-plan sous Windows, il est recommandé d'utiliser uniquement Windows pour le développement et le débogage en environnement de développement. Pour l'environnement de production, veuillez utiliser le système Linux.

## Méthodes de démarrage
Pour le **système Linux**, utilisez la commande `php start.php start` (mode de débogage) ou `php start.php start -d` (mode de processus en arrière-plan) pour démarrer.
Pour le **système Windows**, exécutez `windows.bat` ou utilisez la commande `php windows.php` pour démarrer, puis appuyez sur Ctrl+C pour arrêter. Le système Windows ne prend pas en charge les commandes stop, reload, status, reload connections, etc.

## Résidence en mémoire
webman est un framework résidant en mémoire. En général, une fois que les fichiers PHP sont chargés en mémoire, ils sont réutilisés et ne sont pas lus à nouveau à partir du disque (à l'exception des fichiers de modèle). Par conséquent, pour que les modifications apportées au code métier ou à la configuration prennent effet en environnement de production, vous devez exécuter `php start.php reload`. Si vous modifiez la configuration liée aux processus ou installez de nouveaux packages composer, vous devez redémarrer avec `php start.php restart`.

> Pour faciliter le développement, webman est livré avec un processus de surveillance personnalisé (monitor) qui vérifie les mises à jour des fichiers métier et exécute automatiquement un rechargement en cas de mise à jour. Cette fonctionnalité n'est activée que lorsque workerman est exécuté en mode de débogage (sans `-d` lors du démarrage). Les utilisateurs de Windows doivent exécuter `windows.bat` ou `php windows.php` pour activer cette fonctionnalité.

## À propos des instructions de sortie
Dans les projets traditionnels PHP-FPM, l'utilisation de fonctions telles que `echo` et `var_dump` pour afficher des données les affiche directement sur la page. Cependant, dans webman, ces sorties s'affichent généralement dans le terminal et ne sont pas visibles sur la page (excepté pour les sorties des fichiers de modèle).

## Ne pas utiliser les instructions `exit` ou `die`
L'exécution de `die` ou `exit` entraînera l'arrêt du processus et le redémarrage, ce qui rendra la réponse de la requête actuelle incorrecte.

## Ne pas utiliser la fonction `pcntl_fork`
L'utilisation de `pcntl_fork` pour créer un processus n'est pas autorisée dans webman.
