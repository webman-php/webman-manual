# Prozessüberwachung
Webman enthält einen eigenen Monitor-Prozess, der zwei Funktionen unterstützt:

1. Überwacht die Aktualisierung von Dateien und lädt automatisch neuen Geschäftscode nach (in der Regel während der Entwicklung).
2. Überwacht den Speicherbedarf aller Prozesse. Wenn der Speicherbedarf eines Prozesses kurz davor ist, die `memory_limit`-Einschränkung in der `php.ini` zu überschreiten, wird der Prozess sicher neu gestartet (ohne den Geschäftsbetrieb zu beeinträchtigen).

### Überwachungskonfiguration
Die Konfigurationsdatei `config/process.php` enthält die Einstellungen für die `monitor`-Konfiguration:
```php
global $argv;

return [
    // Dateiaktualisierungserkennung und automatisches Neuladen
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Diese Verzeichnisse überwachen
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
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Soll Dateiüberwachung aktiviert werden
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Soll Speicherüberwachung aktiviert werden
            ]
        ]
    ]
];
```

`monitorDir` wird konfiguriert, um zu überwachen, welche Verzeichnisse aktualisiert werden sollen (es sollten nicht zu viele Dateien in den überwachten Verzeichnissen vorhanden sein).
`monitorExtensions` wird konfiguriert, um festzulegen, welche Dateiendungen in den überwachten `monitorDir`-Verzeichnissen überwacht werden sollen.
Der Wert von `options.enable_file_monitor` ist `true`, um die Dateiaktualisierungsüberwachung zu aktivieren (standardmäßig ist die Dateiüberwachung auf Linux-Systemen im Debug-Modus standardmäßig aktiviert).
Der Wert von `options.enable_memory_monitor` ist `true`, um die Speichernutzungsüberwachung zu aktivieren (Speichernutzungsüberwachung wird auf Windows-Systemen nicht unterstützt).

> **Hinweis**
> Auf Windows-Systemen muss die Dateiüberwachung gestartet werden, wenn Sie `windows.bat` oder `php windows.php` ausführen müssen.
