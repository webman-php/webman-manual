# Was ist webman

Webman ist ein Hochleistungs-HTTP-Service-Framework, das auf [workerman](https://www.workerman.net) basiert. Es wurde entwickelt, um die herkömmliche php-fpm-Architektur zu ersetzen und eine hochperformante, skalierbare HTTP-Servicebereitstellung zu ermöglichen. Mit webman können Websites, HTTP-Schnittstellen oder Microservices entwickelt werden.

Zusätzlich unterstützt webman benutzerdefinierte Prozesse, um alles zu tun, was auch mit workerman möglich ist, wie WebSocket-Services, das Internet der Dinge, Spiele, TCP-Services, UDP-Services, Unix-Socket-Services und vieles mehr.

# Die Philosophie von webman
**Bietet maximale Erweiterbarkeit und beste Leistung mit einem minimalen Kern.**

Webman bietet nur die grundlegenden Funktionen (Routing, Middleware, Session, benutzerdefinierte Prozessschnittstelle). Alle anderen Funktionen werden aus dem Composer-Ökosystem wiederverwendet, was bedeutet, dass Sie die vertrauten Funktionskomponenten in webman verwenden können, z. B. in Bezug auf Datenbanken können Entwickler zwischen Laravel's `illuminate/database`, ThinkPHP's `ThinkORM` oder anderen Komponenten wie `Medoo` wählen. Ihre Integration in webman ist sehr einfach.

# Merkmale von webman

1. Hohe Stabilität: Webman basiert auf der Entwicklung von workerman, das seit jeher als äußerst stabiles Socket-Framework mit sehr wenigen Fehlern in der Branche gilt.

2. Hohe Leistung: Die Leistung von webman ist um das 10- bis 100-fache höher als die herkömmlicher php-fpm-Frameworks und etwa doppelt so hoch wie die von Frameworks wie gin und echo in go.

3. Hohe Wiederverwendung: Die meisten Composer-Komponenten und Bibliotheken können ohne Änderungen wiederverwendet werden.

4. Hohe Erweiterbarkeit: Unterstützt benutzerdefinierte Prozesse, um alles zu tun, was mit workerman möglich ist.

5. Einfache Bedienung: Sehr einfache Bedienung und geringe Lernkurve, der Code unterscheidet sich nicht wesentlich von herkömmlichen Frameworks.

6. Verwendung der freundlichen MIT Open-Source-Lizenz.

# Projektlage
GitHub: https://github.com/walkor/webman **Vergessen Sie nicht, Ihren kleinen Stern zu geben**

Gitee: https://gitee.com/walkor/webman **Vergessen Sie nicht, Ihren kleinen Stern zu geben**

# Benchmark-Daten von Drittanbietern

![benchmarks-php](../assets/img/benchmark1.png)

Mit Datenbankabfragen erreicht webman eine einzelne Durchsatzleistung von 390.000 QPS, was fast 80 Mal höher ist als das Laravel-Framework der herkömmlichen php-fpm-Architektur.

![benchmarks-go](../assets/img/benchmarks-go.png)

Mit Datenbankabfragen hat webman eine etwa doppelt so hohe Leistung wie ein ähnliches go-Sprachen-Web-Framework.

Die obigen Daten stammen von [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
