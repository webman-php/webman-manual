# Konfigurationsdatei

## Position
Die Konfigurationsdatei von webman befindet sich im Verzeichnis `config/` und kann im Projekt durch Verwendung der Funktion `config()` abgerufen werden.

## Abrufen von Konfigurationen

Alle Konfigurationen abrufen
```php
config();
```

Alle Konfigurationen aus `config/app.php` abrufen
```php
config('app');
```

Die Konfiguration `debug` aus `config/app.php` abrufen
```php
config('app.debug');
```

Wenn die Konfiguration ein Array ist, können die Werte der Array-Elemente über `.` abgerufen werden, zum Beispiel
```php
config('file.key1.key2');
```

## Standardwert
```php
config($key, $default);
```
Durch den Parameter default in config wird ein Standardwert übergeben. Wenn die Konfiguration nicht existiert, wird der Standardwert zurückgegeben. Wenn kein Standardwert festgelegt ist und die Konfiguration nicht existiert, wird null zurückgegeben.

## Benutzerdefinierte Konfiguration
Entwickler können ihre eigenen Konfigurationsdateien im Verzeichnis `config/` hinzufügen, beispielsweise

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Verwendung beim Abrufen der Konfiguration**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Konfiguration ändern
webman unterstützt keine dynamische Änderung der Konfiguration. Alle Konfigurationen müssen manuell in der entsprechenden Konfigurationsdatei geändert und dann neu geladen oder neu gestartet werden.

> **Hinweis**
> Die Serverkonfiguration `config/server.php` und Prozesskonfiguration `config/process.php` unterstützen kein neu Laden. Es ist ein Neustart erforderlich, um die Änderungen wirksam zu machen.
