# Richtlinien für die Entwicklung von Anwendungsplugins

## Anforderungen an Anwendungsplugins
- Plugins dürfen keine urheberrechtlich geschützten Codes, Symbole, Bilder usw. enthalten
- Der Quellcode des Plugins muss vollständig und unverschlüsselt sein
- Ein Plugin muss eine vollständige Funktionalität haben und darf keine einfachen Funktionen enthalten
- Es muss eine vollständige Funktionsbeschreibung und Dokumentation bereitgestellt werden
- Ein Plugin darf keine Untermärkte enthalten
- Das Plugin darf keine Texte oder Werbelinks enthalten

## Kennzeichnung von Anwendungsplugins
Jedes Anwendungsplugin hat eine eindeutige Kennzeichnung, die aus Buchstaben besteht. Diese Kennzeichnung beeinflusst den Verzeichnisnamen des Plugins, den Namensraum der Klasse und das Präfix der Plugin-Datenbanktabelle.

Wenn ein Entwickler das Plugin mit der Kennzeichnung 'foo' erstellt, befindet sich der Quellcode des Plugins im Verzeichnis `/{Hauptprojekt}/plugin/foo`, der entsprechende Namensraum des Plugins lautet `plugin\foo`, und das Tabellenpräfix des Plugins lautet `foo_`.

Da die Kennzeichnung weltweit eindeutig ist, muss der Entwickler vor der Entwicklung überprüfen, ob die Kennzeichnung verfügbar ist. Die Überprüfung erfolgt unter [Anwendungs-Kennzeichnungsüberprüfung](https://www.workerman.net/app/check).

## Datenbank
- Tabellennamen bestehen aus Kleinbuchstaben `a-z` und Unterstrichen `_`
- Die Tabellen des Plugins sollten mit der Kennzeichnung des Plugins als Präfix versehen sein, z.B. die Tabelle 'article' des Plugins 'foo' wäre `foo_article`
- Die Primärschlüssel sollten als 'id' indiziert werden
- Es sollte der InnoDB-Motor für die Speicher-Engine verwendet werden
- Die Zeichenkodierung sollte als utf8mb4_general_ci verwendet werden
- Die Datenbank-ORM kann Laravel oder auch Think-ORM verwenden
- Zeitfelder sollten als DateTime verwendet werden

## Code-Konventionen

#### PSR-Spezifikationen
Der Code muss der PSR4-Ladekonvention entsprechen

#### Klassenbenennung im CamelCase mit großem Anfangsbuchstaben
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Eigenschaften und Methoden der Klasse im CamelCase mit kleinem Anfangsbuchstaben
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Methoden, die keine Autorisierung benötigen
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Kommentare abrufen
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Kommentare
Eigenschaften und Funktionen der Klasse müssen Kommentare beinhalten, einschließlich einer Zusammenfassung, der Parameter und des Rückgabetyps

#### Einrückung
Der Code sollte mit 4 Leerzeichen eingerückt werden, anstatt Tabulatoren zu verwenden

#### Flusskontrolle
Schlüsselwörter für die Flusskontrolle (if, for, while, foreach etc.) werden von einem Leerzeichen gefolgt, und die geschweiften Klammern des Anfangs der Flusskontrollanweisung sollten in derselben Zeile wie die schließende runde Klammer stehen.
```php
foreach ($users as $uid => $user) {

}
```

#### Namen temporärer Variablen
Es wird empfohlen, temporäre Variablen im CamelCase mit kleinem Anfangsbuchstaben zu benennen (nicht zwingend erforderlich)
```php
$articleCount = 100;
```
