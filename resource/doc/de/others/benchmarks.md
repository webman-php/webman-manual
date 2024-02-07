# Leistungsprüfung

### Welche Faktoren beeinflussen die Leistungsprüfungsergebnisse?
* Netzwerkverzögerung zwischen dem Leistungsrechner und dem Server (Empfohlen für Leistungsprüfung im lokalen Netzwerk oder auf dem lokalen Computer)
* Bandbreite zwischen dem Leistungsrechner und dem Server (Empfohlen für Leistungsprüfung im lokalen Netzwerk oder auf dem lokalen Computer)
* Ob HTTP Keep-Alive aktiviert ist (Empfohlen, dies zu aktivieren)
* Sind ausreichend viele gleichzeitige Verbindungen vorhanden? (Für Leistungsprüfung im externen Netzwerk sollten so viele gleichzeitige Verbindungen wie möglich geöffnet werden)
* Sind die Prozessanzahl auf dem Server angemessen? (Für die "Hello World"-Geschäftsprozesse wird empfohlen, die Prozessanzahl mit der Anzahl der CPUs zu synchronisieren, und für Datenbank-Geschäftsprozesse wird empfohlen, die Prozessanzahl auf das Vierfache oder mehr der Anzahl der CPUs zu erhöhen)
* Die Leistung des Geschäfts selbst (z.B. ob eine externe Datenbank verwendet wird)

### Was ist HTTP Keep-Alive?
Der HTTP Keep-Alive-Mechanismus wird verwendet, um mehrere HTTP-Anfragen und -Antworten über eine einzige TCP-Verbindung zu senden. Dies hat einen großen Einfluss auf die Leistungstestergebnisse. Wenn Keep-Alive deaktiviert wird, kann die QPS um ein Vielfaches sinken.
Derzeit sind Browser standardmäßig so eingestellt, dass Keep-Alive aktiviert ist. Wenn ein Browser also eine bestimmte HTTP-Adresse aufruft, wird die Verbindung vorübergehend offengehalten und nicht geschlossen, um sie bei der nächsten Anfrage wiederzuverwenden und die Leistung zu verbessern.
Es wird empfohlen, Keep-Alive während der Leistungsprüfung zu aktivieren.

### Wie aktiviert man HTTP Keep-Alive während der Leistungsprüfung?
Wenn Sie das ab-Programm für die Leistungsprüfung verwenden, müssen Sie das -k-Argument verwenden, z.B. `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Apipost muss den Gzip-Header in der Antwort zurücksenden, um Keep-Alive zu aktivieren (ein Fehler in Apipost, siehe unten).
Andere Leistungsprüfprogramme haben dies normalerweise standardmäßig aktiviert.

### Warum ist die QPS bei externen Leistungstests so niedrig?
Die hohe Netzwerkverzögerung im externen Netzwerk führt zu einer niedrigen QPS, was ein normaler Zustand ist. Zum Beispiel können beim Testen der Baidu-Seite die QPS nur einige Dutzend betragen.
Es wird empfohlen, die Leistungsprüfung im lokalen Netzwerk oder auf dem lokalen Computer durchzuführen, um den Einfluss der Netzwerkverzögerung auszuschließen.
Wenn Sie dennoch einen externen Leistungstest durchführen müssen, können Sie die Leistungsfähigkeit durch Erhöhung der Anzahl der gleichzeitigen Verbindungen verbessern (vorausgesetzt, die Bandbreite ist ausreichend).

### Warum sinkt die Leistung nach der Umleitung durch Nginx?
Der Betrieb von Nginx erfordert Ressourcen des Systems. Gleichzeitig benötigt die Kommunikation zwischen Nginx und Webman ebenfalls Ressourcen.
Da die Systemressourcen begrenzt sind und Webman nicht alle Systemressourcen nutzen kann, ist es ein normaler Zustand, dass die Leistung des gesamten Systems möglicherweise sinkt.
Um den Leistungseinfluss der Nginx-Umleitung so weit wie möglich zu minimieren, können Sie in Betracht ziehen, die Nginx-Protokollierung zu deaktivieren (`access_log off;`) und Keep-Alive zwischen Nginx und Webman zu aktivieren, siehe [Nginx-Umleitung](nginx-proxy.md).
Außerdem benötigt HTTPS im Vergleich zu HTTP mehr Ressourcen, da für HTTPS ein SSL/TLS-Handshake, die Verschlüsselung und Entschlüsselung von Daten sowie die Vergrößerung der Paketgröße zur Nutzung zusätzlicher Bandbreite erforderlich ist, was zu einer Leistungsminderung führen kann.
Bei Leistungstests, die keine HTTP Keep-Alive nutzen (d.h. mit Kurzverbindungen), ist bei HTTPS bei jeder Anforderung ein zusätzlicher SSL/TLS-Handshake erforderlich, was zu erheblicher Leistungsminderung führen kann. Es wird empfohlen, bei Leistungstests mit HTTPS HTTP Keep-Alive zu aktivieren.

### Wie kann man feststellen, dass das System die Leistungsgrenze erreicht hat?
In der Regel bedeutet eine CPU-Auslastung von 100%, dass die Leistung des Systems an ihre Grenzen stößt. Wenn die CPU noch Ressourcen frei hat, ist die Leistungsgrenze noch nicht erreicht, und in einem solchen Fall können Sie die Anzahl der gleichzeitigen Verbindungen erhöhen, um die QPS zu verbessern.
Wenn das Erhöhen der Anzahl der gleichzeitigen Verbindungen nicht zu einer Verbesserung der QPS führt, liegt möglicherweise ein Mangel an Webman-Prozessen vor, und Sie sollten die Anzahl der Webman-Prozesse entsprechend erhöhen. Wenn auch dies keine Verbesserung bringt, prüfen Sie, ob die Bandbreite ausreichend ist.

### Warum ist die Leistung von Webman in meinem Leistungstest schlechter als die des Go-Gin-Frameworks?
[Techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) Leistungstests zeigen, dass Webman in allen Indikatoren, einschließlich der reinen Textleistung, Datenbankabfragen und Datenbankaktualisierungen, etwa doppelt so gut abschneidet wie Gin.
Wenn Ihre Ergebnisse abweichen, liegt dies möglicherweise daran, dass Sie in Webman ein ORM verwenden, das zu einem erheblichen Leistungsverlust führt. Versuchen Sie es mit Webman+Native PDO im Vergleich zu Gin+Native SQL.

### Um wie viel wird die Leistung durch die Verwendung von ORM in Webman beeinträchtigt?
Hier sind einige Leistungstests:

**Umgebung**
Alibaba Cloud Server mit 4 Kernen und 4 GB RAM. Eine zufällige Abfrage eines Datensatzes aus 100.000 Datensätzen und Rückgabe als JSON.

**Bei Verwendung von Native PDO**
Webman QPS beträgt 17.800

**Bei Verwendung von Laravel's Db::table()**
Webman QPS sinkt auf 9.400 QPS

**Bei Verwendung des Laravel-Modells**
Webman QPS sinkt auf 7.200 QPS

Das Ergebnis ist ähnlich für ThinkORM und weist keine großen Unterschiede auf.

> **Hinweis**
> Obwohl die Verwendung von ORM zu einer gewissen Leistungsminderung führen kann, reicht dies für die meisten Geschäfte aus. Wir sollten einen Ausgleich zwischen Entwicklungszeit, Wartbarkeit und Leistung sowie anderen Kriterien finden, anstatt nur auf die Leistung zu setzen.

### Warum ist die QPS in Apipost-Leistungstests so niedrig?
Das Leistungstestmodul von Apipost hat einen Fehler. Wenn der Server keinen Gzip-Header zurückschickt, kann keine Keep-Alive-Verbindung aufrechterhalten werden, was zu erheblicher Leistungsminderung führt.
Die Lösung besteht darin, die Daten bei der Rücksendung zu komprimieren und einen Gzip-Header hinzuzufügen, z.B.
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
Darüber hinaus kann Apipost in einigen Fällen nicht zufriedenstellende Leistung bringen, was sich in etwa 50% weniger QPS im Vergleich zu ab bei gleicher Anzahl von gleichzeitigen Verbindungen zeigt.
Es wird empfohlen, für Leistungsprüfungen ab, wrk oder andere spezialisierte Leistungstestprogramme anstelle von Apipost zu verwenden.

### Einstellung der geeigneten Prozessanzahl
Webman beginnt standardmäßig mit der vierfachen Anzahl von Prozessen der CPU. Tatsächlich ist die optimale Anzahl von Prozessen für Geschäftsprozesse ohne Netzwerk-IO, wie "Hello World", gleich der Anzahl der CPU-Kerne, um den Prozesswechselaufwand zu minimieren.
Wenn jedoch blockierende IO-Geschäftsprozesse wie Datenbanken oder Redis vorhanden sind, kann die Prozessanzahl auf das 3- bis 8-fache der Anzahl der CPUs erhöht werden, da in diesem Fall mehr Prozesse erforderlich sind, um die Anzahl der gleichzeitigen Verbindungen zu erhöhen. Der Prozesswechselaufwand ist im Vergleich zu blockierenden IO-Geschäftsprozessen vernachlässigbar.

### Einige Referenzbereiche für Leistungsprüfungen

**Cloud-Server, 4 Kerne, 4 GB RAM, 16 Prozesse, Leistungstest im lokalen Netzwerk/lokalen Computer**

| - | Keep-Alive aktiviert | Keep-Alive deaktiviert |
|--|-----|-----|
| Hello World | 80.000-160.000 QPS | 10.000-30.000 QPS |
| Einzelne Datenbankabfrage | 10.000-20.000 QPS | 10.000 QPS |

[**Leistungstextdaten von Dritten Techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Beispiele für Leistungsprüfungsbefehle

**ab**
```plaintext
# 100.000 Anfragen, 200 gleichzeitige Verbindungen, Keep-Alive aktiviert
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100.000 Anfragen, 200 gleichzeitige Verbindungen, Keep-Alive deaktiviert
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```plaintext
# Leistungsprüfung für 10 Sekunden mit 200 gleichzeitigen Verbindungen, Keep-Alive aktiviert (standardmäßig)
wrk -c 200 -d 10s http://example.com
```
