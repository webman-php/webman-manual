# Erstellen von App-Plugins

## Eindeutige Kennung

Jedes Plugin verfügt über eine eindeutige Anwendungs-Kennung, die Entwickler vor der Entwicklung festlegen und prüfen müssen, ob die Kennung bereits verwendet wird.
Überprüfen Sie die Kennung unter [Anwendungs-Kennung-Prüfung](https://www.workerman.net/app/check)

## Erstellen

Führen Sie `composer require webman/console` aus, um die webman-Befehlszeile zu installieren.

Verwenden Sie den Befehl `php webman app-plugin:create {Plugin-Kennung}`, um ein Anwendungs-Plugin lokal zu erstellen.

Beispiel: `php webman app-plugin:create foo`

Starten Sie webman neu.

Öffnen Sie `http://127.0.0.1:8787/app/foo`. Wenn eine Rückgabe erfolgt, bedeutet dies, dass das Erstellen erfolgreich war.
