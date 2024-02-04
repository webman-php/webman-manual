# Leistung von webman

### Verarbeitungsprozess bei herkömmlichen Frameworks

1. Nginx / Apache empfängt die Anfrage.
2. Nginx / Apache leitet die Anfrage an php-fpm weiter.
3. Php-fpm initialisiert die Umgebung, z. B. durch das Erstellen einer Variablenliste.
4. Php-fpm ruft das RINIT jedes Erweiterungsmoduls auf.
5. Php-fpm liest die PHP-Datei von der Festplatte (kann durch Verwendung von Opcache vermieden werden).
6. Php-fpm führt die lexikalische, syntaktische und Opcode-Kompilierung durch (kann durch Verwendung von Opcache vermieden werden).
7. Php-fpm führt den Opcode aus, einschließlich 8., 9., 10. und 11.
8. Das Framework initialisiert, wie zum Beispiel die Instanziierung verschiedener Klassen, einschließlich Container, Controller, Routen, Middleware usw.
9. Das Framework stellt eine Verbindung zur Datenbank her und überprüft Berechtigungen, verbindet sich mit Redis.
10. Das Framework führt die Geschäftslogik aus.
11. Das Framework beendet die Verbindung zur Datenbank und Redis.
12. Php-fpm gibt Ressourcen frei, zerstört alle Klassendefinitionen und Instanzen, zerstört den Symboltisch usw.
13. Php-fpm ruft die RSHUTDOWN-Methode jedes Erweiterungsmoduls in der Reihenfolge auf.
14. Php-fpm leitet das Ergebnis an Nginx / Apache weiter.
15. Nginx / Apache gibt das Ergebnis an den Client zurück.

### Verarbeitungsprozess bei webman

1. Das Framework empfängt die Anfrage.
2. Das Framework führt die Geschäftslogik aus.
3. Das Framework gibt das Ergebnis an den Client zurück.

Ja, ohne Nginx-Reverse-Proxys gibt es in diesem Fall tatsächlich nur diese 3 Schritte. Man kann sagen, dass dies bereits das ultimative Ziel für ein PHP-Framework ist, was die Leistung von webman um ein Vielfaches oder sogar um das Zehnfache gegenüber herkömmlichen Frameworks verbessert.

Weitere Informationen finden Sie unter [Leistungsprüfungen](benchmarks.md)
