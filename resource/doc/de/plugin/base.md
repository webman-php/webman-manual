# Grundlegende Plugins

Grundlegende Plugins sind in der Regel allgemeine Komponenten, die normalerweise mit Composer installiert und im Vendor-Verzeichnis abgelegt werden. Beim Installieren können einige benutzerdefinierte Konfigurationen (Middleware, Prozesse, Routen usw.) automatisch in das Verzeichnis `{Hauptprojekt}config/plugin` kopiert werden. Webman erkennt dieses Verzeichnis automatisch und fusioniert die Konfiguration in die Hauptkonfiguration, sodass die Plugins in jeden Lebenszyklus von Webman eingreifen können.

Weitere Informationen finden Sie unter [Erstellen von grundlegenden Plugins](create.md)
