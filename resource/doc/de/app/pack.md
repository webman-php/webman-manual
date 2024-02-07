# Paketierung

Beispielweise das Paketieren des Foo-Plug-ins:

- Setzen Sie die Versionsnummer in `plugin/foo/config/app.php` (**wichtig**).
- Löschen Sie die nicht benötigten Dateien im Verzeichnis `plugin/foo`, insbesondere temporäre Dateien für Testuploads unter `plugin/foo/public`.
- Löschen Sie die Datenbank- und Redis-Konfiguration. Falls Ihr Projekt eigene, dedizierte Datenbank- und Redis-Konfigurationen hat, sollten diese während des ersten Zugriffs auf die Anwendung durch ein Installationsleitprogramm ausgelöst werden (muss selbst implementiert werden). Hierbei muss der Administrator die Konfiguration manuell ausfüllen und generieren.
- Stellen Sie alle anderen Dateien wieder her, die in ihren ursprünglichen Zustand zurückversetzt werden müssen.
- Nach Abschluss der oben genannten Operationen wechseln Sie in das Verzeichnis `{Hauptprojekt}/plugin/` und verwenden Sie den Befehl `zip -r foo.zip foo`, um die Datei `foo.zip` zu erstellen.
