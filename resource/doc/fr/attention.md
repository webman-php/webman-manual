# Notes de programmation

## Système d'exploitation
webman prend en charge les systèmes Linux et Windows. Cependant, en raison du fait que workerman ne prend pas en charge la configuration multi-processus et le processus démon sur Windows, il est recommandé d'utiliser le système Windows uniquement pour le développement et le débogage de l'environnement. Pour l'environnement de production, veuillez utiliser le système Linux.

## Méthodes de démarrage
**Sur le système Linux**, utilisez la commande `php start.php start` (mode de débogage) ou `php start.php start -d` (mode démon) pour démarrer.
**Sur le système Windows**, exécutez `windows.bat` ou utilisez la commande `php windows.php` pour démarrer. Pour arrêter, appuyez sur Ctrl + C. Le système Windows ne prend pas en charge les commandes stop, reload, status, reload connections, etc.

## Résident en mémoire
webman est un framework résident en mémoire. En général, une fois les fichiers PHP chargés en mémoire, ils seront réutilisés et ne seront pas lus à nouveau à partir du disque (à l'exception des fichiers de modèle). Par conséquent, pour que les modifications apportées au code métier ou à la configuration prennent effet dans l'environnement de production, exécutez `php start.php reload`. Si vous modifiez la configuration du processus ou installez de nouveaux packages composer, redémarrez avec `php start.php restart`.

> Pour faciliter le développement, webman est livré avec un processus monitor personnalisé pour surveiller les mises à jour des fichiers métier. Lorsqu'un fichier métier est mis à jour, le rechargement est automatiquement effectué. Cette fonctionnalité est uniquement activée lorsque workerman est exécuté en mode de débogage (sans `-d` au démarrage). Les utilisateurs de Windows doivent utiliser `windows.bat` ou `php windows.php` pour activer cette fonctionnalité.

## À propos des instructions de sortie
Dans les projets traditionnels PHP-FPM, l'utilisation des fonctions `echo`, `var_dump`, etc., pour afficher des données les affiche directement sur la page. Cependant, dans webman, ces sorties sont généralement affichées dans le terminal et ne s'affichent pas sur la page (à l'exception des sorties dans les fichiers de modèle).

## Ne pas utiliser les instructions `exit` `die`
L'exécution de `die` ou `exit` entraînera la sortie du processus et le redémarrage, ce qui empêchera la réponse correcte à la requête en cours.

## Ne pas utiliser la fonction `pcntl_fork`
L'utilisation de `pcntl_fork` pour créer un processus n'est pas autorisée dans webman.
