# Anwendungs-Plugin-Entwicklungsnormen

## Anforderungen an Anwendungs-Plugins
* Plugins dürfen keine urheberrechtlich geschützten Codes, Icons, Bilder usw. enthalten.
* Der Quellcode des Plugins muss vollständiger Code sein und darf nicht verschlüsselt sein.
* Das Plugin muss vollständige Funktionen bieten und darf keine einfachen Funktionen enthalten.
* Eine vollständige Funktionsbeschreibung und Dokumentation müssen bereitgestellt werden.
* Das Plugin darf keine Submarktplätze enthalten.
* Im Plugin dürfen keine Texte oder Werbelinks enthalten sein.

## Kennzeichnung von Anwendungs-Plugins
Jedes Anwendungs-Plugin hat eine eindeutige Kennzeichnung, die aus Buchstaben besteht. Diese Kennzeichnung beeinflusst den Verzeichnisnamen des Plugins, den Namensraum der Klasse und das Präfix der Plugin-Datenbanktabelle.

Angenommen, ein Entwickler verwendet "foo" als Plugin-Kennzeichnung, dann befindet sich der Quellcode des Plugins im Verzeichnis `{Hauptprojekt}/plugin/foo`, der entsprechende Namensraum des Plugins ist `plugin\foo`, und das Tabellenpräfix des Plugins ist `foo_`.

Da die Kennzeichnung weltweit eindeutig ist, müssen Entwickler vor der Entwicklung überprüfen, ob die Kennzeichnung verfügbar ist. Überprüfen Sie diese unter [Anwendungs-Kennzeichnungsprüfung](https://www.workerman.net/app/check).

## Datenbank
* Tabellennamen bestehen aus Kleinbuchstaben `a-z` und Unterstrichen `_`.
* Die Tabellen einer Plugin-Datenbank sollten das Plugin-Präfix enthalten. Zum Beispiel wäre die Tabelle "article" des Plugins "foo" als `foo_article` benannt.
* Der Primärschlüssel der Tabelle sollte "id" sein.
* Es wird empfohlen, den InnoDB-Motor für die Speicher-Engine zu verwenden.
* Es wird empfohlen, den Zeichensatz `utf8mb4_general_ci` einheitlich zu verwenden.
* Die Datenbank-ORM kann Laravel oder Think-ORM verwenden.
* Für Zeitfelder wird die Verwendung von DateTime empfohlen.

## Code-Konventionen

#### PSR-Konventionen
Der Code sollte der PSR-4-Ladekonvention entsprechen.

#### Klassennamen beginnen mit einem großen Buchstaben im CamelCase-Stil
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Klasseneigenschaften und -methoden beginnen mit einem kleinen Buchstaben im CamelCase-Stil
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Methoden, die keine Authentifizierung erfordern
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
Klassenattribute und Funktionen müssen Kommentare enthalten, einschließlich einer Zusammenfassung, Parameter und Rückgabetypen.

#### Einrückung
Der Code sollte mit 4 Leerzeichen eingerückt werden, anstatt Tabulatoren zu verwenden.

#### Flusssteuerung
Schlüsselwörter für die Flusssteuerung (if, for, while, foreach usw.) werden von einem Leerzeichen gefolgt, und der Codeblock für die Flusssteuerung sollte auf der gleichen Zeile wie die öffnende Klammer beginnen.
```php
foreach ($users as $uid => $user) {

}
```

#### Namen von temporären Variablen
Es wird empfohlen, temporäre Variablennamen im CamelCase-Stil mit einem Kleinbuchstaben zu verwenden (nicht obligatorisch).
```php
$articleCount = 100;
```
