# Über Speicherverluste
Webman ist ein residentes Speicherframework, daher müssen wir ein wenig auf mögliche Speicherverluste achten. Entwickler müssen sich jedoch keine allzu großen Sorgen machen, da Speicherverluste unter extremen Bedingungen auftreten und leicht vermieden werden können. Die Entwicklung mit Webman ist im Wesentlichen genauso wie bei herkömmlichen Frameworks, es sind keine zusätzlichen Maßnahmen für das Speichermanagement erforderlich.

> **Hinweis**
> Der mitgelieferte Monitorprozess von Webman überwacht den Speicherverbrauch aller Prozesse. Wenn der Speicherverbrauch eines Prozesses den im `php.ini` definierten `memory_limit` erreicht, wird der entsprechende Prozess automatisch sicher neu gestartet, um Speicher freizugeben. In dieser Zeit hat dies keinen Einfluss auf das Geschäft.

## Definition von Speicherverlusten
Mit zunehmenden Anfragen steigt der Speicherverbrauch von Webman **unbegrenzt** (beachten Sie, dass er **unbegrenzt** steigt) und erreicht einige hundert Megabyte oder mehr. Dies wird als Speicherverlust bezeichnet. Wenn der Speicherverbrauch zunimmt, aber später nicht weiter ansteigt, handelt es sich nicht um einen Speicherverlust.

Es ist normal, dass ein Prozess mehrere zehn Megabyte Speicherplatz einnimmt. Wenn ein Prozess jedoch eine extrem große Anfrage verarbeitet oder eine große Anzahl von Verbindungen verwaltet, kann der Speicherverbrauch eines einzelnen Prozesses möglicherweise auf über hundert Megabyte steigen. Nach der Verwendung gibt PHP möglicherweise nicht den gesamten Speicherplatz an das Betriebssystem zurück, sondern behält diesen zur Wiederverwendung bei. Daher kann es zu einer Situation kommen, in der der Speicherverbrauch nach der Verarbeitung einer großen Anfrage steigt und der Speicher nicht freigegeben wird. Dies ist ein normaler Vorgang. (Das Aufrufen der Methode `gc_mem_caches()` kann einen Teil des ungenutzten Speichers freigeben.)

## Wie entstehen Speicherverluste?
**Speicherverluste treten nur unter folgenden beiden Bedingungen auf:**
1. Es gibt ein **langfristiges** Array (Achten Sie auf das Wort "langfristig", normale Arrays sind unbedenklich).
2. Und dieses **langfristige** Array dehnt sich unbegrenzt aus (Geschäft fügt unbegrenzt Daten hinzu, ohne sie zu bereinigen).

Wenn beide Bedingungen 1 und 2 **gleichzeitig erfüllt** sind, tritt ein Speicherverlust auf. Wenn eine der Bedingungen nicht erfüllt ist oder nur eine Bedingung erfüllt ist, liegt kein Speicherverlust vor.

## Langfristige Arrays
In Webman umfassen langfristige Arrays:
1. Arrays mit dem Schlüsselwort `static`
2. Arrays mit Eigenschaften von Singletons
3. Arrays mit dem Schlüsselwort `global`

> **Beachten**
> In Webman ist es erlaubt, langfristige Daten zu verwenden, es muss jedoch sichergestellt werden, dass die Daten im Array begrenzt sind und die Anzahl der Elemente nicht unbegrenzt ansteigt.

Im Folgenden werden jeweils Beispiele erläutert.

#### Unbegrenztes Wachstum des statischen Arrays
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hallo');
    }
}
```
Das mit dem Schlüsselwort `static` definierte `$data` Array ist ein langfristiges Array, und in diesem Beispiel wird das `$data` Array mit jeder Anfrage unbegrenzt erweitert, was zu Speicherverlusten führt.

#### Unbegrenztes Wachstum von Singleton-Array-Eigenschaften
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```
Aufrufcode
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hallo');
    }
}
```
`Cache::instance()` gibt eine Cache-Singleton-Instanz zurück, die eine langfristige Klasseninstanz ist. Obwohl ihr `$data` Attribut kein Schlüsselwort `static` verwendet, ist es aufgrund der langen Lebensdauer der Klasse selbst ein langfristiges Array. Mit jeder Hinzufügung eines neuen Schlüssels zum `$data` Array verbraucht das Programm immer mehr Speicher, was zu Speicherverlusten führt.

> **Beachten**
> Wenn der Schlüssel, den `Cache::instance()->set(key, value)` hinzufügt, in begrenzter Anzahl vorhanden ist, wird keine Speicherverschwendung auftreten, da das `$data` Array nicht unbegrenzt ansteigt.

#### Unbegrenztes Wachstum von globalen Arrays
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
Arrays, die mit dem Schlüsselwort `global` definiert sind, werden nach dem Abschluss einer Funktion oder einer Methoden nicht freigegeben, daher sind sie langfristige Arrays. Der oben genannten Code führt zu Speicherverlusten, da der Speicherverbrauch mit jedem Anstieg der Anfragen steigt. Ebenso sind in Funktionen oder Methoden mit dem Schlüsselwort `static` definierte Arrays langfristige Arrays, und wenn das Array unbegrenzt wächst, tritt ebenfalls ein Speicherverlust auf, z.B:
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```

## Empfehlungen
Entwickler sollten sich in der Regel keine große Sorgen um Speicherverluste machen, da sie selten auftreten. Wenn sie dennoch auftreten, können wir durch Stresstesting den Codeabschnitt finden, der den Speicherverlust verursacht, und das Problem lokalisieren. Selbst wenn Entwickler den Leckagepunkt nicht finden, wird der mitgelieferte Überwachungsdienst von Webman den Prozess rechtzeitig sicher neu starten, um den Speicher freizugeben.

Wenn Sie dennoch versuchen möchten, Speicherverluste zu vermeiden, können Sie die folgenden Empfehlungen beachten:
1. Vermeiden Sie global oder statische Arrays und stellen Sie sicher, dass sie nicht unbegrenzt wachsen.
2. Verwenden Sie für unbekannte Klassen möglichst keine Singleton-Instanzen. Wenn ein Singleton erforderlich ist, überprüfen Sie, ob es Eigenschaften mit unbegrenztem Wachstum gibt. Verwenden Sie die `new`-Anweisung zur Initialisierung, wenn ein Singleton nicht erforderlich ist.
