# Phar-Paketierung

Phar ist eine Art von Paketierungsdatei in PHP, ähnlich wie JAR. Sie können webman-Projekte mit Phar in eine einzelne Phar-Datei verpacken, um die Bereitstellung zu erleichtern.

**Hier möchten wir uns bei [fuzqing](https://github.com/fuzqing) für das Einreichen von PR bedanken.**

> **Hinweis**
> Sie müssen die phar-Einstellungsoptionen in der `php.ini` deaktivieren, indem Sie `phar.readonly = 0` setzen.

## Installation des Befehlszeilenwerkzeugs
Führen Sie `composer require webman/console` aus.

## Konfigurationseinstellungen
Öffnen Sie die Datei `config/plugin/webman/console/app.php` und setzen Sie `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`, um einige unnötige Verzeichnisse und Dateien bei der Paketierung zu ignorieren und ein übermäßiges Paketvolumen zu vermeiden.

## Paketierung
Führen Sie im Stammverzeichnis des webman-Projekts den Befehl `php webman phar:pack` aus.
Es wird eine Datei `webman.phar` im Build-Verzeichnis erstellt.

> Die Konfiguration für die Paketierung befindet sich in `config/plugin/webman/console/app.php`.

## Start- und Stoppbefehle
**Starten**
`php webman.phar start` oder `php webman.phar start -d`

**Stoppen**
`php webman.phar stop`

**Status überprüfen**
`php webman.phar status`

**Verbindungsstatus anzeigen**
`php webman.phar connections`

**Neustarten**
`php webman.phar restart` oder `php webman.phar restart -d`

## Hinweise
* Wenn Sie webman.phar ausführen, wird im Verzeichnis, in dem sich webman.phar befindet, ein Verzeichnis "runtime" erstellt, in dem temporäre Dateien wie Protokolle gespeichert werden.

* Wenn Ihr Projekt eine .env-Datei verwendet, muss diese im selben Verzeichnis wie webman.phar platziert werden.

* Wenn Ihr Geschäftsbetrieb Dateien in das öffentliche Verzeichnis hochladen muss, müssen Sie das öffentliche Verzeichnis auslagern und im selben Verzeichnis wie webman.phar platzieren. In diesem Fall ist eine Konfiguration in `config/app.php` erforderlich.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Das Geschäft kann die Hilfsfunktion `public_path()` verwenden, um den tatsächlichen Speicherort des öffentlichen Verzeichnisses zu finden.

* webman.phar unterstützt keine benutzerdefinierten Prozesse unter Windows.
