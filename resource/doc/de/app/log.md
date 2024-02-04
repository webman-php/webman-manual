# Protokoll
Die Verwendung der Protokollklasse ähnelt der Verwendung der Datenbankklasse.

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Wenn Sie die Protokollkonfiguration des Hauptprojekts wiederverwenden möchten, können Sie einfach verwenden
```php
use support\Log;
Log::info('Protokollinhalt');
// Angenommen, das Hauptprojekt hat eine Protokollkonfiguration namens "test"
Log::channel('test')->info('Protokollinhalt');
```
