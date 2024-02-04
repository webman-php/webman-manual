# Über Memory Leaks
Webman ist ein Framework, das im Speicher verbleibt, daher müssen wir uns ein wenig um mögliche Memory Leaks kümmern. Entwickler müssen sich jedoch keine übermäßigen Sorgen machen, da Memory Leaks unter extremen Bedingungen auftreten und leicht vermieden werden können. Die Entwicklung mit Webman ist im Wesentlichen gleich wie bei herkömmlichen Frameworks und erfordert keine zusätzlichen Operationen für das Memory Management.

> **Hinweis**
> Der mit Webman gelieferte Monitor-Prozess überwacht den Speicherverbrauch aller Prozesse. Wenn der Speicherverbrauch eines Prozesses kurz davor steht, den im php.ini festgelegten Wert von `memory_limit` zu erreichen, wird der entsprechende Prozess automatisch sicher neu gestartet, um Speicher freizugeben. Dies hat keinen Einfluss auf das laufende System.

## Definition eines Memory Leaks
Mit zunehmender Anzahl von Anfragen nimmt der Speicherbedarf von Webman **unbegrenzt zu** (beachten Sie, dass es **unbegrenzt zunimmt**), oft auf mehrere hundert Megabyte oder sogar mehr, was als Memory Leaks bezeichnet wird. Wenn der Speicherbedarf zunimmt, aber dann nicht weiter ansteigt, handelt es sich nicht um einen Memory Leaks.

Ein Prozess, der einige zehn Megabyte Speicher belegt, ist eine normale Situation. Wenn ein Prozess jedoch sehr große Anfragen verarbeitet oder eine große Anzahl von Verbindungen verwaltet, kann der Speicherverbrauch eines einzelnen Prozesses möglicherweise auf mehrere hundert Megabyte ansteigen. Ein Teil dieses Speichers wird möglicherweise von PHP nach der Verwendung nicht vollständig an das Betriebssystem zurückgegeben, sondern für die Wiederverwendung zurückgehalten. Dadurch kann es nach der Verarbeitung einer großen Anfrage zu einer Speichererhöhung kommen, ohne dass der Speicher freigegeben wird. Dies ist ein normaler Vorgang. (Durch den Aufruf der Methode `gc_mem_caches()` kann etwas ungenutzter Speicher freigegeben werden.)

## Wie entstehen Memory Leaks?
**Memory Leaks treten auf, wenn folgende zwei Bedingungen erfüllt sind:**
1. Es gibt ein **Array mit langer Lebensdauer** (beachten Sie, dass es sich um ein Array mit langer Lebensdauer handelt, normale Arrays sind unproblematisch).
2. Dieses **Array mit langer Lebensdauer** wächst unbegrenzt (es werden kontinuierlich Daten in das Array eingefügt, ohne dass diese bereinigt werden).

Wenn beide Bedingungen von Punkt 1 und 2 **gleichzeitig** erfüllt sind, tritt ein Memory Leaks auf. Andernfalls, wenn eine oder gar keine der oben genannten Bedingungen erfüllt ist, liegt kein Memory Leaks vor.

## Arrays mit langer Lebensdauer
In Webman umfassen Arrays mit langer Lebensdauer:
1. Arrays mit dem Schlüsselwort `static`
2. Array-Eigenschaften von Singleton-Instanzen
3. Arrays mit dem Schlüsselwort `global`

> **Hinweis**
> In Webman ist es möglich, Daten mit langer Lebensdauer zu verwenden, jedoch muss sichergestellt werden, dass die Elemente in diesen Daten begrenzt sind und die Anzahl der Elemente nicht unbegrenzt wächst.

Im Folgenden werden Beispiele zu den einzelnen Punkten erläutert.

#### Unbegrenzt wachsendes statisches Array
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

Das mit dem Schlüsselwort `static` definierte Array `$data` ist ein Array mit langer Lebensdauer und wächst in dem Beispiel kontinuierlich mit jeder Anfrage, was zu einem Memory Leaks führt.

#### Unbegrenzt wachsende Array-Eigenschaften eines Singleton
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

Aufruf des Codes
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

`Cache::instance()` gibt eine Singleton-Instanz von `Cache` zurück, die eine lange Lebensdauer hat. Obwohl das `$data`-Attribut kein Schlüsselwort `static` verwendet, ist es aufgrund der langen Lebensdauer der Klasse selbst ebenfalls ein Array mit langer Lebensdauer. Durch kontinuierliches Hinzufügen unterschiedlicher Schlüssel zur `$data`-Eigenschaft nimmt auch der Speicherverbrauch des Programms zu und führt so zu einem Memory Leaks.

> **Hinweis**
> Wenn die von `Cache::instance()->set(key, value)` hinzugefügten Schlüssel eine begrenzte Anzahl haben, tritt kein Memory Leaks auf, da das `$data`-Array nicht unbegrenzt wächst.

#### Unbegrenzt wachsendes globales Array
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

Arrays, die mit dem Schlüsselwort `global` definiert sind, werden nach dem Abschluss der Funktion oder der Methode nicht freigegeben, wodurch sie zu Arrays mit langer Lebensdauer werden. Der obige Code verursacht einen Memory Leaks, da der Speicherverbrauch mit jeder Anfrage kontinuierlich zunimmt. Ebenso sind Arrays, die in Funktionen oder Methoden mit dem Schlüsselwort `static` definiert sind, Arrays mit langer Lebensdauer. Wenn solch ein Array unbegrenzt wächst, kann auch ein Memory Leaks auftreten, etwa wie folgt:
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
Entwickler sollten Memory Leaks nicht übermäßig beachten, da sie äußerst selten auftreten. Sollten sie unglücklicherweise auftreten, können wir durch Belastungstests feststellen, welche Teile des Codes den Memory Leaks verursachen und so das Problem lokalisieren. Selbst wenn Entwickler den Leckagepunkt nicht finden, wird der von Webman bereitgestellte Monitor-Dienst gestartet, um Prozesse mit Memory Leaks rechtzeitig sicher neu zu starten und den Speicher freizugeben.

Wenn Sie Memory Leaks jedoch so weit wie möglich vermeiden möchten, können Sie die folgenden Empfehlungen beachten.
1. Vermeiden Sie die Verwendung von Arrays mit dem Schlüsselwort `global` und `static`, und wenn Sie diese dennoch verwenden, stellen Sie sicher, dass sie nicht unbegrenzt wachsen.
2. Vermeiden Sie die Verwendung von Singleton für Klassen, die Sie nicht kennen, und verwenden Sie stattdessen das `new`-Stichwort zur Initialisierung. Sollten Sie dennoch ein Singleton benötigen, überprüfen Sie, ob es Eigenschaften mit unbegrenztem Wachstum von Arrays gibt.
