# Processus de surveillance
webman est livré avec un processus de surveillance appelé monitor, qui prend en charge deux fonctionnalités :
1. Surveiller les mises à jour des fichiers et recharger automatiquement le nouveau code métier (généralement utilisé pendant le développement).
2. Surveiller l'utilisation de la mémoire par tous les processus ; s'il est constaté qu'un processus utilise presque la limite définie dans `memory_limit` de `php.ini`, il redémarrera automatiquement de manière sécurisée (sans affecter l'activité).

### Configuration de la surveillance
Dans le fichier de configuration `config/process.php`, configurez la surveillance dans la section `monitor` de la manière suivante :
```php
global $argv;

return [
    // Détection des mises à jour des fichiers et rechargement automatique
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Surveiller ces répertoires
            'monitorDir' => array_merge([    // Quels répertoires doivent être surveillés
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Les fichiers avec ces extensions seront surveillés
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Activer la surveillance des fichiers
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Activer la surveillance de la mémoire
            ]
        ]
    ]
];
```
L'option `monitorDir` est utilisée pour configurer les répertoires à surveiller (il est déconseillé de surveiller trop de fichiers dans les répertoires).
L'option `monitorExtensions` est utilisée pour configurer les extensions de fichiers à surveiller dans le répertoire spécifié par `monitorDir`.
Lorsque la valeur de `options.enable_file_monitor` est `true`, la surveillance des mises à jour de fichiers est activée (activée par défaut en mode debug sur les systèmes Linux).
Lorsque la valeur de `options.enable_memory_monitor` est `true`, la surveillance de l'utilisation de la mémoire est activée (la surveillance de l'utilisation de la mémoire n'est pas prise en charge sur les systèmes Windows).

> **Remarque**
> Sur les systèmes Windows, la surveillance des mises à jour de fichiers ne peut être activée que lorsque vous exécutez `windows.bat` ou `php windows.php`.
