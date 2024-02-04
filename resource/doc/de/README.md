# webman ist

webman ist ein High-Performance-HTTP-Service-Framework, das auf [workerman](https://www.workerman.net) basiert. Es ersetzt die herkömmliche PHP-FPM-Architektur und bietet eine skalierbare und hoch performante HTTP-Service-Lösung. Mit webman können Websites, HTTP-Schnittstellen oder Mikroservices entwickelt werden.

Zusätzlich unterstützt webman die Erstellung benutzerdefinierter Prozesse und kann somit alles ermöglichen, was auch mit workerman möglich ist, zum Beispiel WebSocket-Services, das Internet der Dinge, Spiele, TCP-Services, UDP-Services, Unix-Socket-Services, und vieles mehr.

# Grundgedanke von webman
**Die maximale Erweiterbarkeit und stärkste Leistung bei minimalem Kern bieten.**

webman bietet nur die grundlegenden Funktionen (Routing, Middleware, Sitzungen, benutzerdefinierte Prozess-Schnittstelle) und nutzt alle weiteren Funktionen aus dem Composer-Ökosystem. Dies bedeutet, dass Sie in webman die vertrautesten Funktionskomponenten nutzen können. Zum Beispiel können Entwickler im Bereich der Datenbank wahlweise auf `illuminate/database` von Laravel, `ThinkORM` von ThinkPHP oder andere Komponenten wie `Medoo` zurückgreifen. Die Integration dieser Komponenten in webman ist sehr einfach.

# Merkmale von webman
1. Hohe Stabilität: webman basiert auf workerman, das als High-Stability-Socket-Framework in der Branche bekannt ist.
2. Höchste Leistung: Die Leistung von webman ist etwa 10-100 Mal höher als die herkömmlicher PHP-FPM-Frameworks und etwa doppelt so hoch wie die von Frameworks wie Gin und Echo in Go.
3. Hohe Wiederverwendbarkeit: Die meisten Composer-Komponenten und Bibliotheken können ohne Änderungen wiederverwendet werden.
4. Hohe Erweiterbarkeit: Unterstützt die Erstellung benutzerdefinierter Prozesse, die alles ermöglichen, was mit workerman möglich ist.
5. Einfach und benutzerfreundlich: Die Lernkurve ist sehr flach und der Code ist ähnlich zu herkömmlichen Frameworks.
6. Verwendet die sehr flexible und freundliche MIT Open Source-Lizenz.

# Projektadressen
GitHub: https://github.com/walkor/webman **Bitte zögern Sie nicht, einen Stern zu setzen**

Gitee: https://gitee.com/walkor/webman **Bitte zögern Sie nicht, einen Stern zu setzen**

# Leistungsdaten von Drittanbietern

![](../assets/img/benchmark1.png)

Mit Datenbankabfragen erreicht webman eine Einzelserver-Durchsatzleistung von 390.000 QPS, was etwa 80 Mal höher ist als die des Laravel-Frameworks in der herkömmlichen PHP-FPM-Architektur.

![](../assets/img/benchmarks-go.png)

Mit Datenbankabfragen bietet webman etwa doppelt so hohe Leistung wie vergleichbare Web-Frameworks in der Go-Sprache.

Die oben genannten Daten stammen von [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf).
