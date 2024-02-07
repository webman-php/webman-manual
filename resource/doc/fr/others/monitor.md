# Surveillance des processus
Webman est livré avec un processus de surveillance appelé "monitor" qui prend en charge deux fonctionnalités :
1. Surveillance des mises à jour de fichiers et rechargement automatique du nouveau code métier (généralement utilisé pendant le développement).
2. Surveillance de l'utilisation de la mémoire par tous les processus. Si la consommation mémoire d'un processus dépasse bientôt la limite définie dans `php.ini` (`memory_limit`), le processus sera redémarré en toute sécurité sans affecter l'activité métier.

### Configuration de surveillance
Dans le fichier de configuration `config/process.php`, sous la configuration `monitor` :

```php
global $argv;

return [
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            'monitorDir' => array_merge([
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];
```

Le paramètre `monitorDir` est utilisé pour configurer les répertoires à surveiller (il est déconseillé de surveiller un grand nombre de fichiers dans ces répertoires).
Le paramètre `monitorExtensions` est utilisé pour spécifier les extensions des fichiers à surveiller dans les répertoires définis dans `monitorDir`.
La valeur `options.enable_file_monitor` est définie sur `true` pour activer la surveillance des mises à jour de fichiers (par défaut, la surveillance des fichiers est activée lors de l'exécution en mode debug sous Linux).
La valeur `options.enable_memory_monitor` est définie sur `true` pour activer la surveillance de l'utilisation de la mémoire (la surveillance de la mémoire n'est pas prise en charge par Windows).

> **Remarque**
> Sous Windows, la surveillance des mises à jour de fichiers n'est activée que lorsque vous exécutez `windows.bat` ou `php windows.php`.
