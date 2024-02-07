# Umgebungsvoraussetzungen

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Projekt erstellen

```php
composer create-project workerman/webman
```

### 2. Ausführen

Navigieren Sie zum Webman-Verzeichnis

#### Windows-Benutzer
Doppelklicken Sie auf `windows.bat` oder führen Sie `php windows.php` aus, um zu starten

> **Hinweis**
> Wenn Fehler auftreten, liegt dies wahrscheinlich daran, dass bestimmte Funktionen deaktiviert sind. Siehe [Deaktivierungsprüfung von Funktionen](others/disable-function-check.md), um die Deaktivierung aufzuheben.

#### Linux-Benutzer
Im `Debug`-Modus ausführen (für Entwicklungsdebugging)

```php
php start.php start
```

Im `Daemon`-Modus ausführen (für den Produktionsumgebung)

```php
php start.php start -d
```

> **Hinweis**
> Wenn Fehler auftreten, liegt dies wahrscheinlich daran, dass bestimmte Funktionen deaktiviert sind. Siehe [Deaktivierungsprüfung von Funktionen](others/disable-function-check.md), um die Deaktivierung aufzuheben.

### 3. Zugriff

Öffnen Sie den Browser und rufen Sie `http://IP-Adresse:8787` auf
