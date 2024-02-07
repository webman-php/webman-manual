# webman Leistung

### Traditioneller Framework-Anfrageverarbeitungsprozess

1. Nginx/Apache empfängt die Anfrage
2. Nginx/Apache leitet die Anfrage an php-fpm weiter
3. PHP-FPM initialisiert die Umgebung, wie das Erstellen einer Variablenliste
4. PHP-FPM ruft RINIT von verschiedenen Erweiterungen/Modulen auf
5. PHP-FPM liest PHP-Dateien von der Festplatte (durch Verwendung von OpCache vermeidbar)
6. PHP-FPM führt lexikale und syntaktische Analyse durch und kompiliert sie in Opcode (durch Verwendung von OpCache vermeidbar)
7. PHP-FPM führt Opcode aus, einschließlich 8, 9, 10, 11
8. Framework-Initialisierung, wie die Instanziierung verschiedener Klassen wie Container, Controller, Routing, Middleware usw.
9. Framework stellt eine Verbindung zur Datenbank her und führt Berechtigungsprüfungen durch, stellt Verbindung zu Redis her
10. Framework führt Geschäftslogik aus
11. Framework schließt die Datenbank- und Redis-Verbindung
12. PHP-FPM gibt Ressourcen frei, zerstört alle Klassendefinitionen, Instanzen, löst den Symboltabelle auf usw.
13. PHP-FPM ruft die RSHUTDOWN-Methoden der verschiedenen Erweiterungen/Module in Reihenfolge auf
14. PHP-FPM leitet das Ergebnis an Nginx/Apache weiter
15. Nginx/Apache gibt das Ergebnis an den Client zurück

### Webmans Anfrageverarbeitungsprozess
1. Das Framework empfängt die Anfrage
2. Das Framework führt die Geschäftslogik aus
3. Das Framework gibt das Ergebnis an den Client zurück

Richtig, in Abwesenheit einer nginx-Front-End-Proxylösung hat das Framework nur diese 3 Schritte. Man kann sagen, dass dies bereits das Maximum an Leistung für ein PHP-Framework ist, was die Leistung von webman um das Mehrfache oder sogar Zehnfache im Vergleich zu traditionellen Frameworks steigert.

Weitere Informationen siehe [Leistungstests](benchmarks.md)
