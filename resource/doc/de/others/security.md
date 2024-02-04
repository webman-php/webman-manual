# Sicherheit

## Benutzer, der das Programm ausführt
Es wird empfohlen, den Benutzer, der das Programm ausführt, auf einen Benutzer mit geringeren Rechten zu setzen, z. B. den gleichen Benutzer wie der nginx-Benutzer. Der Benutzer, der das Programm ausführt, wird in `config/server.php` unter `user` und `group` festgelegt. Der Benutzer für benutzerdefinierte Prozesse wird ebenfalls in `config/process.php` unter `user` und `group` festgelegt. Es ist zu beachten, dass der Monitoring-Prozess nicht als Benutzer festgelegt werden sollte, da er erhöhte Berechtigungen für ordnungsgemäße Funktion benötigt.

## Controller-Spezifikation
Im Verzeichnis `controller` oder seinen Unterverzeichnissen dürfen nur Controller-Dateien abgelegt werden. Das Platzieren von anderen Klassen-Dateien ist untersagt, da andernfalls bei nicht aktivierter [Controller-Suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)-Funktion die Klassen-Dateien möglicherweise illegal von der URL aufgerufen werden können, was zu unvorhersehbaren Konsequenzen führen kann. Zum Beispiel befindet sich die Datei `app/controller/model/User.php` tatsächlich in der Model-Klasse, wird jedoch fälschlicherweise im `controller`-Verzeichnis abgelegt. Bei nicht aktivierter [Controller-Suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)-Funktion kann dies dazu führen, dass Benutzer auf beliebige Methoden in `User.php` durch ähnliche URLs wie `/model/user/xxx` zugreifen. Um diese Situation vollständig zu vermeiden, wird dringend empfohlen, das [Controller-Suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) zu verwenden, um deutlich zu kennzeichnen, welche Dateien Controller-Dateien sind.

## XSS-Filterung
Aus Gründen der Benutzerfreundlichkeit führt webman keine XSS-Escapes für Anfragen durch. webman empfiehlt dringend, beim Rendern XSS-Escapes durchzuführen, anstatt vor dem Speichern in die Datenbank zu escapen. Darüber hinaus führen Twig, Blade, think-tmplate und andere Templates automatisch XSS-Escapes durch, sodass manuelles Escapen nicht erforderlich ist.

> **Hinweis**
> Wenn Sie vor dem Speichern in die Datenbank Escapes durchführen, besteht die Möglichkeit, dass einige Anwendungs-Plugins nicht kompatibel sind.

## Schutz vor SQL-Injektionen
Um SQL-Injektionen zu verhindern, verwenden Sie bitte nach Möglichkeit ORM wie [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) und [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html) und vermeiden Sie das manuelle Zusammenstellen von SQL-Abfragen.

## Nginx-Proxy
Wenn Ihre Anwendung für externe Benutzer zugänglich ist, wird dringend empfohlen, einen Nginx-Proxy vor webman zu implementieren, um einige illegale HTTP-Anfragen zu filtern und die Sicherheit zu erhöhen. Weitere Informationen finden Sie unter [Nginx Proxy](nginx-proxy.md).
