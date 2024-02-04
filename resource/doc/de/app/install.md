# Installation

Es gibt zwei Möglichkeiten zur Installation von Anwendungs-Plugins:

## Installation über den Plugin-Marktplatz
Rufen Sie die Anwendungs-Plugin-Seite im [offiziellen webman-admin-Backend](https://www.workerman.net/plugin/82) auf, klicken Sie auf die Schaltfläche "Installieren", um das entsprechende Anwendungs-Plugin zu installieren.

## Installation aus dem Quellcode-Paket
Laden Sie das Anwendungs-Plugin-Zip-Paket aus dem Anwendungsmarkt herunter, entpacken Sie es und laden Sie das Entpackungsverzeichnis in das Verzeichnis `{Hauptprojekt}/plugin/` hoch (falls das Plugin-Verzeichnis nicht vorhanden ist, muss es manuell erstellt werden). Führen Sie dann `php webman app-plugin:install Plugin-Name` aus, um die Installation abzuschließen.

Beispiel: Wenn Sie das Zip-Paket mit dem Namen `ai.zip` heruntergeladen haben und es in `{Hauptprojekt}/plugin/ai` entpackt haben, führen Sie `php webman app-plugin:install ai` aus, um die Installation abzuschließen.

# Deinstallation

Analog zur Installation gibt es zwei Möglichkeiten zur Deinstallation von Anwendungs-Plugins:

## Deinstallation über den Plugin-Marktplatz
Rufen Sie die Anwendungs-Plugin-Seite im [offiziellen webman-admin-Backend](https://www.workerman.net/plugin/82) auf und klicken Sie auf die Schaltfläche "Deinstallieren", um das entsprechende Anwendungs-Plugin zu deinstallieren.

## Deinstallation aus dem Quellcode-Paket
Führen Sie `php webman app-plugin:uninstall Plugin-Name` aus, um die Deinstallation abzuschließen. Nach Abschluss müssen Sie das entsprechende Plugin-Verzeichnis im Verzeichnis `{Hauptprojekt}/plugin/` manuell löschen.
