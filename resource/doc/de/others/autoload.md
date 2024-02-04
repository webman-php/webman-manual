# Automatische Ladefunktion

## Laden von Dateien gemäß PSR-0-Spezifikation mithilfe von Composer
Webman folgt der automatischen Ladefunktionsspezifikation `PSR-4`. Wenn Ihr Geschäftsvorhaben das Laden von Codebibliotheken gemäß der Spezifikation `PSR-0` erfordert, befolgen Sie die nachstehenden Schritte.

- Erstellen Sie das Verzeichnis `extend`, um Codebibliotheken gemäß der `PSR-0`-Spezifikation zu speichern.
- Bearbeiten Sie die Datei `composer.json` und fügen Sie den folgenden Inhalt unter `autoload` hinzu.

```js
"psr-0" : {
    "": "extend/"
}
```
Das endgültige Ergebnis ähnelt dem folgenden Bild.
![](../../assets/img/psr0.png)

- Führen Sie `composer dumpautoload` aus.
- Führen Sie `php start.php restart` aus, um Webman neu zu starten (Hinweis: ein Neustart ist erforderlich, damit die Änderungen wirksam werden).

## Laden bestimmter Dateien mithilfe von Composer

- Bearbeiten Sie die Datei `composer.json` und fügen Sie unter `autoload.files` die zu ladenden Dateien hinzu.
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Führen Sie `composer dumpautoload` aus.
- Führen Sie `php start.php restart` aus, um Webman neu zu starten (Hinweis: ein Neustart ist erforderlich, damit die Änderungen wirksam werden).

> **Hinweis**
> Die in der Datei `composer.json` konfigurierten `autoload.files` werden vor dem Start von Webman geladen. Die Dateien, die mit Hilfe der Datei `config/autoload.php` des Frameworks geladen werden, werden nach dem Start von Webman geladen. Die durch `autoload.files` geladenen Dateien erfordern einen Neustart, um Änderungen wirksam zu machen; ein Neuladen ist nicht ausreichend. Dateien, die mit Hilfe der Datei `config/autoload.php` des Frameworks geladen werden, unterstützen Hot-Loading und werden nach Änderungen durch ein Neuladen wirksam.

## Laden bestimmter Dateien mithilfe des Frameworks
Einige Dateien entsprechen möglicherweise nicht der SPR-Spezifikation und können nicht automatisch geladen werden. In diesem Fall können wir mithilfe der Konfiguration in `config/autoload.php` diese Dateien laden, beispielsweise:

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **Hinweis**
 > Wie im `autoload.php` zu sehen ist, werden die Dateien `support/Request.php` und `support/Response.php` geladen. Dies liegt daran, dass sich im Verzeichnis `vendor/workerman/webman-framework/src/support/` ebenfalls zwei identische Dateien befinden. Durch `autoload.php` wird die Priorität auf das Laden von `support/Request.php` und `support/Response.php` im Stammverzeichnis des Projekts gelegt. Dadurch können wir den Inhalt dieser beiden Dateien anpassen, ohne die Dateien im `vendor` ändern zu müssen. Falls keine Anpassungen erforderlich sind, können diese beiden Konfigurationen ignoriert werden.
