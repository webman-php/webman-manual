# Auto-Loading

## Laden von Dateien im PSR-0-Format mit Composer
Webman folgt der automatischen Laden-Spezifikation `PSR-4`. Wenn Sie Bibliotheken im Format `PSR-0` laden müssen, befolgen Sie die folgenden Schritte.

- Erstellen Sie das Verzeichnis `extend`, um Bibliotheken im Format `PSR-0` zu speichern.
- Bearbeiten Sie die Datei `composer.json` und fügen Sie unter `autoload` den folgenden Inhalt hinzu:

```js
"psr-0" : {
    "": "extend/"
}
```
Das Endergebnis ähnelt folgendem:
![](../../assets/img/psr0.png)

- Führen Sie `composer dumpautoload` aus.
- Führen Sie `php start.php restart` aus, um Webman neu zu starten (Hinweis: Ein Neustart ist erforderlich, damit die Änderungen wirksam werden).

## Laden bestimmter Dateien mit Composer

- Bearbeiten Sie die Datei `composer.json` und fügen Sie unter `autoload.files` die zu ladenden Dateien hinzu:
```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```
- Führen Sie `composer dumpautoload` aus.
- Führen Sie `php start.php restart` aus, um Webman neu zu starten (Hinweis: Ein Neustart ist erforderlich, damit die Änderungen wirksam werden).

> **Hinweis**
> Die Dateien, die in der `autoload.files`-Konfiguration von `composer.json` aufgeführt sind, werden vor dem Start von Webman geladen. Dateien, die mit dem Framework `config/autoload.php` geladen werden, werden nach dem Start von Webman geladen.
> Wenn sich die mit `autoload.files` konfigurierten Dateien ändern, müssen Sie Webman neu starten, damit die Änderungen wirksam werden. Ein Neuladen reicht nicht aus. Dateien, die mit `config/autoload.php` geladen werden, unterstützen das Hot-Loading und Änderungen werden sofort nach einem Neuladen wirksam.

## Laden bestimmter Dateien mit dem Framework
Einige Dateien entsprechen möglicherweise nicht dem PSR-Standard und können daher nicht automatisch geladen werden. In solchen Fällen können wir die Dateien mithilfe der Konfiguration von `config/autoload.php` laden, beispielsweise:

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
> In der `autoload.php`-Konfiguration sehen wir, dass die Dateien `support/Request.php` und `support/Response.php` zum Laden festgelegt sind. Dies liegt daran, dass sich im Verzeichnis `vendor/workerman/webman-framework/src/support/` ebenfalls zwei identische Dateien befinden. Durch `autoload.php` werden die Dateien `support/Request.php` und `support/Response.php` im Stammverzeichnis des Projekts priorisiert geladen, was es ermöglicht, den Inhalt dieser beiden Dateien anzupassen, ohne die Dateien im `vendor`-Ordner ändern zu müssen. Wenn Sie diese nicht anpassen müssen, können Sie diese Konfigurationen ignorieren.
