# Protokoll
Die Verwendung der Protokollklasse ist ähnlich wie die Verwendung der Datenbankklasse.

```php
use support\Log;
Log::channel('plugin.admin.default')->info('Test');
```

Wenn Sie die Protokollkonfiguration des Hauptprojekts wiederverwenden möchten, verwenden Sie einfach:

```php
use support\Log;
Log::info('Protokollinhalt');
// Angenommen, das Hauptprojekt hat eine Test-Protokollkonfiguration
Log::channel('test')->info('Protokollinhalt');
```
