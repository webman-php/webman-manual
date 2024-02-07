# Erstellen von Anwendungs-Plugins

## Eindeutige Kennung

Jedes Plugin hat eine eindeutige Kennung. Bevor Entwickler mit der Entwicklung beginnen, müssen sie eine Kennung wählen und überprüfen, ob diese noch nicht verwendet wird. Die Überprüfung kann unter folgender Adresse durchgeführt werden: [Anwendungs-Kennung überprüfen](https://www.workerman.net/app/check)

## Erstellung

Führen Sie `composer require webman/console` aus, um die webman-Befehlszeile zu installieren.

Mit dem Befehl `php webman app-plugin:create {Plugin-Kennung}` können Sie ein Anwendungs-Plugin lokal erstellen.

Zum Beispiel `php webman app-plugin:create foo`

Starten Sie webman neu.

Rufen Sie `http://127.0.0.1:8787/app/foo` auf. Wenn eine Antwort zurückgegeben wird, bedeutet das, dass das Plugin erfolgreich erstellt wurde.
