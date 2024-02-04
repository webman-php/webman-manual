# Verpacken

Wenn Sie das Plugin "foo" verpacken möchten, gehen Sie bitte wie folgt vor:

- Setzen Sie die Versionsnummer in der Datei `plugin/foo/config/app.php` (**wichtig**)
- Löschen Sie unnötige Dateien im Verzeichnis `plugin/foo`, insbesondere temporäre Dateien für den Test-Upload in `plugin/foo/public`
- Löschen Sie die Datenbank- und Redis-Konfiguration. Falls Ihr Projekt über eigene Datenbank- und Redis-Konfigurationen verfügt, sollten diese Konfigurationen beim Erstzugriff auf die Anwendung über ein Installations-Tool (muss selbst implementiert werden) vom Administrator manuell eingegeben und generiert werden.
- Stellen Sie alle anderen Dateien wieder her, die in ihren ursprünglichen Zustand versetzt werden müssen.
- Nach Abschluss dieser Schritte wechseln Sie in das Verzeichnis `{Hauptprojekt}/plugin/` und verwenden Sie den Befehl `zip -r foo.zip foo`, um die Datei "foo.zip" zu erstellen.
