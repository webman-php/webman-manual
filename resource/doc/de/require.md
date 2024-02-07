# erforderliche Umgebung

## Linux-System
Für das Linux-System werden die Erweiterungen `posix` und `pcntl` benötigt, welche in der Regel bereits in PHP integriert sind und daher normalerweise keine separate Installation erfordern.

Benutzer von Baota müssen lediglich die Funktionen, die mit "pnctl_" beginnen, in Baota deaktivieren oder löschen.

Die Erweiterung `event` ist nicht zwingend erforderlich, wird aber für eine bessere Leistung empfohlen.

## Windows-System
Webman kann auch auf Windows-Systemen ausgeführt werden, jedoch wird empfohlen, Windows nur als Entwicklungsplattform zu verwenden und für die Produktionsumgebung auf Linux-Systeme zurückzugreifen, da unter Windows keine Konfiguration von Multiprozessen und Hintergrundprozessen möglich ist.

Hinweis: Unter Windows sind die Erweiterungen `posix` und `pcntl` nicht erforderlich.
