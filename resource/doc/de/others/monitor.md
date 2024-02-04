# Prozessüberwachung
Webman verfügt über einen integrierten Monitor-Prozess, der zwei Funktionen unterstützt:
1. Überwachung von Dateiupdates und automatisches Neuladen des neuen Geschäftscode (normalerweise während der Entwicklung verwendet)
2. Überwachung des Speicherbedarfs aller Prozesse. Wenn der Speicherbedarf eines Prozesses kurz davor steht, die Grenze von `memory_limit` in der `php.ini` zu überschreiten, wird der Prozess automatisch sicher neu gestartet (ohne den Geschäftsbetrieb zu beeinträchtigen).

### Überwachungskonfiguration
Konfigurationsdatei `config/process.php` mit der Konfiguration für `monitor`:
```php
global $argv;

return [
    // Dateiupdate-Erkennung und automatisches Neuladen
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Überwachen dieser Verzeichnisse
            'monitorDir' => array_merge([    // Welche Verzeichnisse sollen überwacht werden
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Dateien mit diesen Endungen werden überwacht
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Soll die Dateiüberwachung aktiviert werden
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Soll die Speichernutzungsüberwachung aktiviert werden
            ]
        ]
    ]
];
```
`monitorDir` wird konfiguriert, um zu überwachen, welchen Verzeichnissen (es sollten nicht zu viele Dateien in den überwachten Verzeichnissen vorhanden sein).
`monitorExtensions` wird konfiguriert, um festzulegen, welche Dateiendungen im Verzeichnis `monitorDir` überwacht werden sollen.
Wenn `options.enable_file_monitor` den Wert `true` hat, wird die Überwachung von Dateiupdates aktiviert (standardmäßig auf Linux-Systemen im Debug-Modus aktiviert).
Wenn `options.enable_memory_monitor` den Wert `true` hat, wird die Überwachung des Speicherbedarfs aktiviert (Speicherüberwachung wird auf Windows-Systemen nicht unterstützt).

> **Hinweis**
> Unter Windows muss die Dateiupdate-Überwachung gestartet werden, wenn `windows.bat` oder `php windows.php` ausgeführt werden müssen.
