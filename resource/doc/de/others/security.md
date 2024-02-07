# Sicherheit

## Benutzer bei der Ausführung
Es wird empfohlen, den Benutzer, der die Anwendung ausführt, auf einen Benutzer mit geringeren Berechtigungen zu setzen, beispielsweise den gleichen Benutzer wie der Nginx-Benutzer. Der Ausführungsbenutzer wird in `config/server.php` in den Parametern `user` und `group` festgelegt. Ebenso wird der Benutzer für benutzerdefinierte Prozesse in `config/process.php` unter `user` und `group` spezifiziert. Es ist zu beachten, dass der Überwachungsprozess keinen Ausführungsbenutzer haben sollte, da er erhöhte Berechtigungen für ein ordnungsgemäßes Funktionieren benötigt.

## Controller Richtlinien
Im Verzeichnis `controller` oder dessen Unterverzeichnissen sollten nur Controller-Dateien abgelegt werden. Das Platzieren von anderen Klassen-Dateien wird untersagt, da andernfalls bei deaktivierter [Controller-Suffix-Funktion](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) Klassen-Dateien möglicherweise über ungültige URLs zugegriffen werden können, was zu unvorhersehbaren Folgen führt. Zum Beispiel ist `app/controller/model/User.php` eigentlich eine Model-Klasse, wurde jedoch fälschlicherweise im `controller`-Verzeichnis abgelegt. Ohne [Controller-Suffix-Funktion](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) könnten Benutzer auf beliebige Methoden in `User.php` über URLs wie `/model/user/xxx` zugreifen. Um diese Situation vollständig zu verhindern, wird dringend empfohlen, [Controller-Suffix-Funktion](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) zu verwenden, um eindeutig zu kennzeichnen, welche Dateien Controller-Dateien sind.

## XSS-Filterung
Aus Gründen der Allgemeingültigkeit führt webman keine XSS-Escaping für Anfragen durch. Es wird dringend empfohlen, XSS-Escaping beim Rendern durchzuführen, anstatt dies vor der Speicherung in der Datenbank zu tun. Weiterhin führen Twig, Blade, think-tmplate und ähnliche Templates automatisch XSS-Escaping durch, so dass dies nicht manuell erfolgen muss.

> **Hinweis**
> Wenn Sie XSS-Escaping vor der Speicherung in der Datenbank durchführen, kann dies zu Inkompatibilitätsproblemen mit einigen Anwendungsplugins führen.

## Vorbeugung gegen SQL-Injection
Um SQL-Injection zu verhindern, verwenden Sie bitte so weit wie möglich ORM wie [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) oder [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html) und versuchen Sie, SQL nicht manuell zusammenzusetzen.

## Nginx-Proxy
Wenn Ihre Anwendung für externe Benutzer zugänglich sein soll, wird dringend empfohlen, vor webman einen Nginx-Proxy hinzuzufügen, um einige unerlaubte HTTP-Anfragen zu filtern und die Sicherheit zu erhöhen. Weitere Informationen finden Sie unter [Nginx-Proxy](nginx-proxy.md).
