# Konfigurationsdatei

Die Konfiguration von Plugins ist genauso wie bei normalen Webman-Projekten, jedoch betrifft die Konfiguration von Plugins in der Regel nur das aktuelle Plugin und hat in der Regel keinen Einfluss auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.foo.app.controller_suffix` nur das Suffix des Controllers des Plugins und hat keinen Einfluss auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.foo.app.controller_reuse` nur die Wiederverwendung von Controllern des Plugins und hat keinen Einfluss auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.foo.middleware` nur die Middleware des Plugins und hat keinen Einfluss auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.foo.view` nur die verwendete Ansicht des Plugins und hat keinen Einfluss auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.foo.container` nur den verwendeten Container des Plugins und hat keinen Einfluss auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.foo.exception` nur die Ausnahmeverarbeitungsklasse des Plugins und hat keinen Einfluss auf das Hauptprojekt.

Da die Routen jedoch global sind, beeinflusst die konfigurierte Route eines Plugins auch global.

## Konfiguration abrufen
Die Methode zum Abrufen der Konfiguration eines bestimmten Plugins lautet `config('plugin.{Plugin}.{Spezifische Konfiguration}')`; zum Beispiel das Abrufen aller Konfigurationen von `plugin/foo/config/app.php` erfolgt über die Methode `config('plugin.foo.app')`.
Ebenso können das Hauptprojekt oder andere Plugins alle 'config('plugin.foo.xxx')' verwenden, um die Konfiguration des Plugins 'foo' zu erhalten.

## Nicht unterstützte Konfigurationen
Das Anwendungsplugin unterstützt keine server.php- und session.php-Konfigurationen, unterstützt keine `app.request_class`- und `app.public_path`-Konfigurationen, und auch keine `app.runtime_path`-Konfiguration.
