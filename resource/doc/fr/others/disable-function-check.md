# Désactivation de la vérification des fonctions

Utilisez ce script pour vérifier si des fonctions ont été désactivées. Exécutez la commande suivante dans le terminal : ```curl -Ss https://www.workerman.net/webman/check | php```

Si vous obtenez le message ```La fonction nom_de_la_fonction a été désactivée. Veuillez vérifier les disable_functions dans php.ini```, cela signifie que les fonctions dont webman a besoin ont été désactivées. Il faut les réactiver dans php.ini pour pouvoir utiliser webman correctement.
Pour réactiver ces fonctions, suivez l'une des méthodes ci-dessous.

## Méthode 1
Installez `webman/console`
```
composer require webman/console ^v1.2.35
```

Exécutez la commande
```
php webman fix-disable-functions
```

## Méthode 2

Exécutez le script `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` pour réactiver les fonctions désactivées.

## Méthode 3

Exécutez `php --ini` pour trouver l'emplacement du fichier php.ini utilisé par php cli.

Ouvrez php.ini, trouvez `disable_functions` et réactivez l'appel des fonctions suivantes :
```
stream_socket_server
stream_socket_client
pcntl_signal_dispatch
pcntl_signal
pcntl_alarm
pcntl_fork
posix_getuid
posix_getpwuid
posix_kill
posix_setsid
posix_getpid
posix_getpwnam
posix_getgrnam
posix_getgid
posix_setgid
posix_initgroups
posix_setuid
posix_isatty
proc_open
proc_get_status
proc_close
shell_exec
```
