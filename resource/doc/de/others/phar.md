# Phar-Packung

Phar ist eine Art von Paketdatei in PHP, ähnlich wie JAR. Sie können Phar verwenden, um Ihr Webman-Projekt in eine einzelne Phar-Datei zu verpacken und bequem zu deployen.

**Ein großes Dankeschön an [fuzqing](https://github.com/fuzqing) für den Pull Request.**

> **Hinweis**
> Sie müssen die Pharo-Konfigurationsoptionen in `php.ini` deaktivieren, indem Sie `phar.readonly = 0` setzen.

## Installation des Befehlszeilenwerkzeugs
`composer require webman/console`

## Konfiguration einrichten
Öffnen Sie die Datei `config/plugin/webman/console/app.php` und setzen Sie `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`, um beim Packen einige unnötige Verzeichnisse und Dateien auszuschließen und ein zu großes Packvolumen zu vermeiden.

## Packen
Führen Sie den Befehl `php webman phar:pack` im Stammverzeichnis des Webman-Projekts aus, um eine `webman.phar`-Datei im Build-Verzeichnis zu erstellen.

> Die Konfiguration für das Packen befindet sich in `config/plugin/webman/console/app.php`.

## Befehle zum Starten und Beenden
**Starten**
`php webman.phar start` oder `php webman.phar start -d`

**Stoppen**
`php webman.phar stop`

**Status anzeigen**
`php webman.phar status`

**Verbindungsstatus anzeigen**
`php webman.phar connections`

**Neustarten**
`php webman.phar restart` oder `php webman.phar restart -d`

## Hinweis
* Wenn Sie webman.phar ausführen, wird im Verzeichnis von webman.phar ein Laufzeitverzeichnis erstellt, in dem temporäre Dateien wie Protokolle abgelegt werden.

* Wenn Sie in Ihrem Projekt eine .env-Datei verwenden, müssen Sie die .env-Datei im Verzeichnis von webman.phar platzieren.

* Wenn Ihr Geschäftsvorgang das Hochladen von Dateien in das öffentliche Verzeichnis erfordert, müssen Sie das öffentliche Verzeichnis aus dem Verzeichnis von webman.phar herausnehmen und im Verzeichnis von webman.phar platzieren. In diesem Fall müssen Sie auch `config/app.php` konfigurieren.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Das Geschäft kann die Hilfsfunktion `public_path()` verwenden, um den tatsächlichen Speicherort des öffentlichen Verzeichnisses zu finden.

* webman.phar unterstützt keine benutzerdefinierten Prozesse unter Windows.
