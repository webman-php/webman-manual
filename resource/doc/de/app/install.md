# Installation

Die Anwendungserweiterungen können auf zwei Arten installiert werden:

## Installation über den Plugin-Marktplatz
Gehen Sie zur Seite für Anwendungserweiterungen im [offiziellen Verwaltungstool webman-admin](https://www.workerman.net/plugin/82) und klicken Sie auf die Schaltfläche "Installieren", um die entsprechende Anwendungserweiterung zu installieren.

## Installation aus dem Quellcode-Paket
Laden Sie das Paket für die Anwendungserweiterung aus dem Anwendungsmarktplatz herunter, entpacken Sie es und laden Sie das entpackte Verzeichnis in das Verzeichnis `{Hauptprojekt}/plugin/` hoch (erstellen Sie das Plugin-Verzeichnis manuell, wenn es nicht vorhanden ist). Führen Sie dann `php webman app-plugin:install plugin-name` aus, um die Installation abzuschließen.

Wenn zum Beispiel das heruntergeladene Archiv den Namen `ai.zip` hat und im Verzeichnis `{Hauptprojekt}/plugin/ai` entpackt wird, führen Sie `php webman app-plugin:install ai` aus, um die Installation abzuschließen.


# Deinstallation

Auch die Deinstallation von Anwendungserweiterungen erfolgt auf zwei Arten:

## Deinstallation über den Plugin-Marktplatz
Gehen Sie zur Seite für Anwendungserweiterungen im [offiziellen Verwaltungstool webman-admin](https://www.workerman.net/plugin/82) und klicken Sie auf die Schaltfläche "Deinstallieren", um die entsprechende Anwendungserweiterung zu deinstallieren.

## Deinstallation aus dem Quellcode-Paket
Führen Sie `php webman app-plugin:uninstall plugin-name` aus, um die Deinstallation abzuschließen. Löschen Sie anschließend manuell das Verzeichnis der entsprechenden Erweiterung im Verzeichnis `{Hauptprojekt}/plugin/`.
