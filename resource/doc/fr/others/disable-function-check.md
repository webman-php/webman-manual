# Désactiver la vérification des fonctions

Utilisez ce script pour vérifier si des fonctions sont désactivées. Exécutez la commande suivante dans le terminal : ```curl -Ss https://www.workerman.net/webman/check | php```.

Si vous voyez le message ```La fonction nom_de_fonction a été désactivée. Veuillez vérifier disable_functions dans php.ini```, cela signifie que les fonctions dont webman a besoin sont désactivées et doivent être activées dans le fichier php.ini pour pouvoir utiliser webman correctement.

Pour lever l'interdiction, suivez l'une des méthodes ci-dessous.

## Méthode un
Installez `webman/console` :
```bash
composer require webman/console ^v1.2.35
```

Exécutez la commande :
```bash
php webman fix-disable-functions
```

## Méthode deux
Exécutez le script suivant pour lever l'interdiction :
```bash
curl -Ss https://www.workerman.net/webman/fix-disable-functions | php
```

## Méthode trois
Exécutez `php --ini` pour trouver l'emplacement du fichier php.ini utilisé par php cli.

Ouvrez php.ini et trouvez `disable_functions`, puis supprimez l'appel des fonctions suivantes :
```plaintext
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
