# Leistungsprüfung

### Welche Faktoren beeinflussen die Ergebnisse der Leistungsprüfung?
* Netzwerklatenz zwischen dem Testrechner und dem Server (es wird empfohlen, den Test im lokalen Netzwerk oder auf demselben Rechner durchzuführen)
* Bandbreite zwischen dem Testrechner und dem Server (es wird empfohlen, den Test im lokalen Netzwerk oder auf demselben Rechner durchzuführen)
* Aktivierung von HTTP Keep-Alive (es wird empfohlen, dies zu aktivieren)
* Ausreichende Anzahl von gleichzeitigen Verbindungen (bei Tests im externen Netzwerk sollten so viele gleichzeitige Verbindungen wie möglich geöffnet werden)
* Angemessene Anzahl von Serverprozessen (für einfache "Hallo Welt"-Anwendungen wird empfohlen, die Anzahl der Prozesse an die Anzahl der CPUs anzupassen, für datenbankintensive Anwendungen wird eine Anzahl empfohlen, die das Vierfache oder mehr der Anzahl der CPUs beträgt)
* Leistungsfähigkeit der Anwendung selbst (z.B. Verwendung einer externen Datenbank)

### Was ist HTTP Keep-Alive?
Der HTTP Keep-Alive-Mechanismus ermöglicht es, mehrere HTTP-Anfragen und -Antworten über eine einzelne TCP-Verbindung zu senden. Dies hat einen erheblichen Einfluss auf die Leistungstestergebnisse, da die Deaktivierung von Keep-Alive zu einer erheblichen Verringerung der Anfragen pro Sekunde (QPS) führen kann.
Derzeit ist Keep-Alive standardmäßig in Browsern aktiviert, was bedeutet, dass eine Verbindung nach dem Zugriff auf eine URL vorübergehend aufrechterhalten wird und bei der nächsten Anfrage wiederverwendet wird, um die Leistung zu verbessern.
Es wird empfohlen, Keep-Alive während der Leistungsprüfung zu aktivieren.

### Wie aktiviert man HTTP Keep-Alive während der Leistungsprüfung?
Wenn Sie `ab` zur Durchführung des Tests verwenden, müssen Sie das Flag `-k` setzen, z.B. `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Zusätzlich muss apipost beim Zurückgeben von Daten einen "gzip" Header zurückgeben, um Keep-Alive zu aktivieren (ein bekannter Fehler in apipost, siehe unten).
Andere Leistungstestprogramme aktivieren Keep-Alive normalerweise standardmäßig.

### Warum ist die QPS bei externen Leistungstests so niedrig?
Die hohe Latenz des externen Netzwerks führt zu einer niedrigen QPS, was ein normales Phänomen ist. Wenn Sie beispielsweise die Seite von Baidu testen, könnte die QPS nur bei einigen Dutzend liegen.
Es wird empfohlen, den Test im lokalen Netzwerk oder auf demselben Rechner durchzuführen, um die Auswirkungen der Netzwerklatenz zu eliminieren.
Wenn Sie dennoch externe Leistungstests durchführen möchten, können Sie die Anzahl der gleichzeitigen Verbindungen erhöhen, um die Durchsatzrate zu steigern (vorbehaltlich ausreichender Bandbreite).

### Warum ist die Leistung nach der Verwendung eines Nginx-Proxys gesunken?
Der Betrieb von Nginx erfordert Ressourcen des Systems. Darüber hinaus verbraucht die Kommunikation zwischen Nginx und webman ebenfalls Ressourcen.
Da die Systemressourcen begrenzt sind und webman nicht alle Systemressourcen abrufen kann, ist ein geringfügiger Leistungsabfall des gesamten Systems ein normales Phänomen.
Um die Auswirkungen des Nginx-Proxys auf die Leistung so gering wie möglich zu halten, sollten Sie in Betracht ziehen, die Nginx-Protokollierung zu deaktivieren (`access_log off;`) und die Keep-Alive zwischen Nginx und webman zu aktivieren. Siehe [Nginx-Proxy](nginx-proxy.md).

Außerdem verursacht HTTPS im Vergleich zu HTTP einen größeren Ressourcenverbrauch, da für HTTPS ein SSL/TLS-Handshake, Datenverschlüsselung/-entschlüsselung und größere Paketgrößen für die Bandbreite erforderlich sind, was zu einer Leistungsminderung führt.
Bei Leistungstests mit nicht aktiviertem HTTP Keep-Alive (kurze Verbindungen) führt jede Anfrage zu zusätzlicher SSL/TLS-Handshakes und einer erheblichen Leistungsminderung. Es wird empfohlen, beim Testen von HTTPS HTTP Keep-Alive zu aktivieren.

### Wie kann man herausfinden, dass das System das Leistungslimit erreicht hat?
Im Allgemeinen bedeutet eine CPU-Auslastung von 100 %, dass das System die Leistungsgrenze erreicht hat. Wenn die CPU noch freie Kapazitäten hat, bedeutet dies, dass die Leistungsgrenze noch nicht erreicht ist. In diesem Fall können Sie die Anzahl der gleichzeitigen Verbindungen erhöhen, um die QPS zu steigern.
Wenn das Hinzufügen von gleichzeitigen Verbindungen die QPS nicht steigern kann, liegt möglicherweise ein Mangel an webman-Prozessen vor. In diesem Fall sollten Sie die Anzahl der webman-Prozesse entsprechend erhöhen. Wenn die QPS immer noch nicht steigt, sollten Sie prüfen, ob die Bandbreite ausreichend ist.

### Warum sind die Leistungstestergebnisse von webman im Vergleich zum go-Framework gin schlechter?
Die [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2)-Leistungstests zeigen, dass webman in allen Kategorien, wie z.B. reinem Text, Datenbankabfragen und Datenbankaktualisierungen, um etwa das Doppelte besser abschneidet als gin.
Wenn Ihre Ergebnisse abweichen, liegt dies möglicherweise daran, dass Sie in webman ein ORM verwenden, was zu einem erheblichen Leistungsverlust führt. Sie können versuchen, webman mit dem nativen PDO und gin mit native SQL zu vergleichen.

### Wie stark wird die Leistung durch die Verwendung von ORM in webman beeinträchtigt?
Hier sind einige Leistungstest-Daten:

**Umgebung**
4 Core 4G Alibaba Cloud Server, zufällige Abfrage von einem Datensatz mit 100.000 Einträgen, Rückgabe als JSON.

**Wenn PDO verwendet wird**
Webman erreicht 17.800 QPS

**Wenn das Laravel Db::table() verwendet wird**
Der QPS von Webman sinkt auf 9.400

**Wenn das Laravel Model verwendet wird**
Der QPS von Webman sinkt auf 7.200

Ähnliche Ergebnisse wurden auch mit ThinkORM erzielt.

> **Hinweis**
> Obwohl die Verwendung von ORM zu einem Leistungsverlust führen kann, reicht dies für die meisten Anwendungen aus. Wir sollten einen Mittelweg zwischen Entwicklungseffizienz, Wartbarkeit und Leistung finden, anstatt nur die Leistung zu maximieren.

### Warum ist die QPS bei Verwendung von apipost für Leistungstests so niedrig?
Das Leistungstestmodul von apipost hat einen Fehler: Wenn der Server keinen "gzip" Header zurückgibt, wird Keep-Alive nicht aufrechterhalten, was zu einer erheblichen Leistungsminderung führt.
Um dies zu beheben, sollten die Daten vor dem Zurücksenden komprimiert und ein "gzip" Header hinzugefügt werden, z.B.
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
Abgesehen davon kann es vorkommen, dass apipost unter bestimmten Bedingungen nicht die erwartete Leistung erbringt, was sich darin äußert, dass die QPS mit apipost etwa 50 % niedriger ist als mit ab und anderen professionellen Leistungstest-Softwares. Es wird empfohlen, ab, wrk oder andere spezialisierte Leistungstest-Softwares anstelle von apipost zu verwenden.

### Festlegung der geeigneten Anzahl von Prozessen
Webman öffnet standardmäßig die vierfache Anzahl von CPU-Prozessen. In der Praxis ist die optimale Anzahl von Prozessen für eine helloworld-Anwendung ohne Netzwerk-I/O gleich der Anzahl der CPU-Kerne, da dadurch der Prozesswechselaufwand reduziert werden kann.
Wenn es sich um blockierende I/O-Anwendungen wie solche mit Datenbanken oder Redis handelt, kann die Anzahl der Prozesse das 3- bis 8-fache der Anzahl der CPUs betragen, da in diesem Fall mehr Prozesse für eine höhere Anzahl von gleichzeitigen Verbindungen benötigt werden, und der Prozesswechselaufwand im Vergleich zur blockierenden I/O vernachlässigbar ist.

### Einige Referenzbereiche für Leistungstests

**Cloud-Server mit 4 Cores, 4G, 16 Prozesse, lokaler Test/Leistungstest im lokalen Netzwerk**

| - | Keep-Alive aktiviert | Keep-Alive deaktiviert |
|--|-----|-----|
| Hello World | 80.000-160.000 QPS | 10.000-30.000 QPS |
| Einzelnabfrage der Datenbank | 10.000-20.000 QPS | 10.000 QPS |

[**Leistungstestdaten von Dritten, techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Beispiele für Leistungstestbefehle

**ab**
```
# 100.000 Anfragen, 200 gleichzeitige Verbindungen, Keep-Alive aktiviert
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100.000 Anfragen, 200 gleichzeitige Verbindungen, Keep-Alive deaktiviert
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# Leistungsprüfung mit 200 gleichzeitigen Verbindungen für 10 Sekunden, Keep-Alive aktiviert (Standard)
wrk -c 200 -d 10s http://example.com
```
