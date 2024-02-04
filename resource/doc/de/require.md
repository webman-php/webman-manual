# Benötigte Umgebung


## Linux-System
Das Linux-System benötigt die Erweiterungen `posix` und `pcntl`, die normalerweise in PHP integriert sind und in der Regel keine zusätzliche Installation erfordern.

Für Benutzer von Baota empfiehlt es sich, nur die Funktionen zu deaktivieren oder zu löschen, die mit `pnctl_` beginnen.

Die Erweiterung `event` ist nicht zwingend erforderlich, wird jedoch für bessere Leistung empfohlen.

## Windows-System
Webman kann auf Windows-Systemen ausgeführt werden, allerdings wird aufgrund der Unmöglichkeit, mehrere Prozesse und Hintergrundprozesse einzurichten, empfohlen, Windows nur als Entwicklungsplattform zu verwenden. In Produktionsumgebungen sollte ein Linux-System genutzt werden.

Hinweis: Unter Windows sind die Erweiterungen `posix` und `pcntl` nicht erforderlich.
