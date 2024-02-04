# Systemanforderungen

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. Projekt erstellen

```php
composer create-project workerman/webman
```

### 2. Ausführung

Gehe in das Webman-Verzeichnis

#### Benutzer von Windows
Doppelklick auf `windows.bat` oder Ausführung von `php windows.php` zum Starten

> **Hinweis**
> Falls Fehler auftreten, besteht wahrscheinlich eine Einschränkung von Funktionen. Siehe [Funktions-Einschränkungsprüfung](others/disable-function-check.md) zur Aufhebung der Einschränkungen.

#### Benutzer von Linux
Im `debug`-Modus ausführen (für Entwicklung und Debugging)

```php
php start.php start
```

Im `daemon`-Modus ausführen (für den produktiven Einsatz)

```php
php start.php start -d
```

> **Hinweis**
> Falls Fehler auftreten, besteht wahrscheinlich eine Einschränkung von Funktionen. Siehe [Funktions-Einschränkungsprüfung](others/disable-function-check.md) zur Aufhebung der Einschränkungen.

### 3. Zugriff

Öffne den Webbrowser und gehe zu `http://ip-adresse:8787`
