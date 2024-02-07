# Monitoraggio dei processi
webman è dotato di un processo di monitoraggio predefinito che supporta due funzionalità:

1. Monitoraggio dell'aggiornamento dei file e ricarica automatica del nuovo codice di business (generalmente utilizzato durante lo sviluppo)
2. Monitoraggio della memoria utilizzata da tutti i processi; se la memoria utilizzata da un processo sta per superare il limite impostato in `php.ini` come `memory_limit`, il processo viene riavviato in modo sicuro (senza influenzare il business)

## Configurazione del monitoraggio
Il file di configurazione è `config/process.php` all'interno della configurazione `monitor`
```php
global $argv;

return [
    // Rilevamento dell'aggiornamento dei file e ricarica automatica
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitorare queste directory
            'monitorDir' => array_merge([    // Quali directory devono essere monitorate
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // I file con queste estensioni verranno monitorati
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Abilita il monitoraggio dei file
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Abilita il monitoraggio della memoria
            ]
        ]
    ]
];
```

`monitorDir` serve per configurare quali directory monitorare (non è consigliabile monitorare troppe file nelle directory).
`monitorExtensions` serve per configurare le estensioni dei file all'interno di `monitorDir` che devono essere monitorate.
Il valore di `options.enable_file_monitor` impostato su `true` abilita il monitoraggio degli aggiornamenti dei file (in esecuzione sul sistema Linux con l'opzione debug abilitata, il monitoraggio dei file è abilitato per impostazione predefinita).
Il valore di `options.enable_memory_monitor` impostato su `true` abilita il monitoraggio della memoria utilizzata (il monitoraggio della memoria non è supportato dal sistema Windows).

> **Nota**
> Nel sistema Windows, il monitoraggio degli aggiornamenti dei file può essere abilitato solo quando si esegue `windows.bat` o `php windows.php`.
