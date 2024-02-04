# Konfigurationsdatei

## Standort
Die Konfigurationsdatei von webman befindet sich im Verzeichnis `config/`, und im Projekt kann über die Funktion `config()` auf die entsprechende Konfiguration zugegriffen werden.

## Konfiguration abrufen

Alle Konfigurationen abrufen
```php
config();
```

Alle Konfigurationen in `config/app.php` abrufen
```php
config('app');
```

Die Konfiguration `debug` in `config/app.php` abrufen
```php
config('app.debug');
```

Wenn die Konfiguration ein Array ist, können Sie mit einem Punkt `.` auf die Elemente im Array zugreifen, zum Beispiel
```php
config('file.key1.key2');
```

## Standardwert
```php
config($key, $default);
```
Durch das Übergeben des zweiten Parameters an `config()` kann ein Standardwert übergeben werden. Wenn die Konfiguration nicht vorhanden ist, wird der Standardwert zurückgegeben. Wenn keine Konfiguration vorhanden ist und kein Standardwert festgelegt wurde, wird `null` zurückgegeben.

## Benutzerdefinierte Konfiguration
Entwickler können ihre eigenen Konfigurationsdateien im Verzeichnis `config/` hinzufügen, zum Beispiel

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Verwendung bei der Konfigurationsabfrage**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Ändern der Konfiguration
webman unterstützt keine dynamische Änderung der Konfiguration. Alle Konfigurationen müssen manuell in den entsprechenden Konfigurationsdateien geändert werden, und ein Neustart (`reload` oder `restart`) ist erforderlich.

> **Hinweis**
> Die Serverkonfiguration `config/server.php` und die Prozesskonfiguration `config/process.php` unterstützen kein `reload`. Ein `restart` ist erforderlich, um die Konfiguration wirksam zu machen.
