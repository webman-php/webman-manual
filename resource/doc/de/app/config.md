# Konfigurationsdateien

Die Konfiguration von Plugins erfolgt genauso wie bei normalen Webman-Projekten; jedoch betrifft die Konfiguration von Plugins normalerweise nur das jeweilige Plugin und hat in der Regel keine Auswirkungen auf das Hauptprojekt. Zum Beispiel betrifft der Wert von `plugin.foo.app.controller_suffix` nur das Controller-Suffix des Plugins und hat keine Auswirkungen auf das Hauptprojekt. Ebenso betrifft der Wert von `plugin.foo.app.controller_reuse` nur die Wiederverwendung von Controllern im Plugin und hat keine Auswirkungen auf das Hauptprojekt. Der Wert von `plugin.foo.middleware` betrifft nur die Middleware des Plugins und hat keine Auswirkungen auf das Hauptprojekt. Der Wert von `plugin.foo.view` betrifft nur die vom Plugin verwendete Ansicht und hat keine Auswirkungen auf das Hauptprojekt. Der Wert von `plugin.foo.container` betrifft nur den vom Plugin verwendeten Container und hat keine Auswirkungen auf das Hauptprojekt. Der Wert von `plugin.foo.exception` betrifft nur die Behandlung von Ausnahmen im Plugin und hat keine Auswirkungen auf das Hauptprojekt.

Da die Routen global sind, beeinflusst die Konfiguration der Routen für Plugins auch global.

## Konfiguration abrufen
Um die Konfiguration eines bestimmten Plugins abzurufen, lautet die Methode `config('plugin.{Plugin}.{spezifische Konfiguration}')`, zum Beispiel, um alle Konfigurationen von `plugin/foo/config/app.php` abzurufen, lautet die Methode `config('plugin.foo.app')`. Ebenso können das Hauptprojekt oder andere Plugins die Konfiguration von `plugin.foo.xxx` abrufen, um die Konfiguration des Plugins "foo" zu erhalten.

## Nicht unterstützte Konfigurationen
Die Anwendung von Plugins unterstützt keine server.php- und session.php-Konfigurationen und keine Konfigurationen wie `app.request_class`, `app.public_path`, `app.runtime_path`.
