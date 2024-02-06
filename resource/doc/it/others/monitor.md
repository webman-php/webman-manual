# Monitoraggio dei processi
Webman è dotato di un processo di monitoraggio integrato che supporta due funzionalità:
1. Monitora l'aggiornamento dei file e ricarica automaticamente il nuovo codice di business (di solito utilizzato durante lo sviluppo)
2. Monitora l'utilizzo della memoria da parte di tutti i processi e, se un processo sta per superare il limite di `memory_limit` impostato in `php.ini`, ne avvia automaticamente il riavvio sicuro (senza influire sul business)

### Configurazione del monitoraggio
Nel file di configurazione `config/process.php`, è presente la configurazione `monitor`
```php
global $argv;

return [
    // Rilevamento dell'aggiornamento dei file e ricarica automatica
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitora queste directory
            'monitorDir' => array_merge([    // Directory dei file da monitorare
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Verranno monitorati i file con queste estensioni
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
`monitorDir` è utilizzato per configurare quali directory vengono monitorate per gli aggiornamenti (non è consigliabile monitorare troppe directory).
`monitorExtensions` è utilizzato per configurare quali estensioni di file presenti nelle directory di `monitorDir` devono essere monitorate.
Il valore di `options.enable_file_monitor` impostato su `true` abilita il monitoraggio degli aggiornamenti dei file (in esecuzione sul sistema Linux in modalità di debug, il monitoraggio dei file è abilitato per impostazione predefinita).
Il valore di `options.enable_memory_monitor` impostato su `true` abilita il monitoraggio dell'utilizzo della memoria (il monitoraggio dell'utilizzo della memoria non è supportato dal sistema Windows).

> **Nota**
> Sotto il sistema Windows, il monitoraggio degli aggiornamenti dei file è attivato solo quando si esegue `windows.bat` o `php windows.php`.
