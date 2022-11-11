# About memory leaks
webman is resident in the memory framework, so we need to be slightly concerned about memory leaks. But developers don't have to worry too much because memory leaks happen under very extreme conditions and are easily circumvented. webman development is basically the same experience as traditional framework development, so there is no need to do extra operations for memory management。

> **hint**
> webmanThe self-contained monitor process monitors the memory usage of all processes, and if a process is about to reach the `memory_limit` value set in php.ini, it will automatically and safely restart the corresponding process to free up memory, with no impact on the business in the meantime。

## Memory Leak Definition
As requests continue to grow，webmanAll requests are reused**infinitely increasing**(Note是**infinitely increasing**)，up to several hundredMor even more，This is a memory leak。
If there is memory growth, but no more growth later is not considered a memory leak。

It is not uncommon for a single process to consume hundreds of megabytes of memory when it is handling large requests or maintaining a large number of connections. After this memory is used, php may not return it all to the operating system. It is normal that this memory may not be released after processing a large request, as it is left for reuse. (Calling the gc_mem_caches() method will free up some of the free memory)


## How memory leaks happen
**A memory leak occurs only if the following two conditions are met：**
1. exist**so performance will be better than**array(Note是Long-lived arrays，requests keep growing)
2. and this**so performance will be better than**Arrays will expand indefinitely(The business inserts data into it infinitely，long-lived)

if1 2condition**Satisfy both**(NotePlease refer to)，then a memory leak will occur。Conversely it is not a memory leak if the above conditions are not met or if only one of the conditions is met。


## Long-lived arrays

webmanThe array of the long life cycle includes：
1. staticArray of keywords
2. Array properties for single instance
3. globalArray of keywords


> **Note**
> webmanThe use of long-lived data is allowed, but you need to make sure that the data within the data is limited and the number of elements does not expand indefinitely。


The following are examples of each

#### Infinitely expanding static arrays
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

以`static`The file is similar to the following`$data`Array isLong-lived arrays，Before using`$data`The array expands as the requests keep growing，occupied memory also。

#### Infinitely Expanded Singleton Array Properties
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

Calling code
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

`Cache::instance()`Return aCachesingle instance，It is an instance of a class with a long lifecycle，Free memory`$data`Attribute not used though`static`keyword，but since the class itself is long-lived，so`$data`alsoLong-lived arrays。it is possible to pass`$data`add different values to the arraykeythe data，The program also uses more and more memory every month，so there is no need to call。

> **Note**
> If Cache::instance()->set(key, value) adds a finite number of keys, there is no memory leak because the `$data` array is not infinitely large。

#### Infinitely expanding global array
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
global keyword-defined arrays are not recycled after a function or class method is executed，compare-referenceLong-lived arrays，The above code generates memory leaks as the requests keep increasing。Similarly within a function or method tostaticThe array defined by the keyword is alsoLong-lived arrays，Memory leaks also occur if the array is infinitely expanded，example：
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

## Suggestions
Suggestionsdevelopers do not have to pay special attention to memory leaks，thus locating the problem，If unfortunately it happens we can find out which piece of code is generating the leak by pressure testing，If the record is not found。even if the developer does not find a leak，webmanself-containedmonitorThe service will safely restart processes that have memory leaks in due time，such as controller。

If you really want to try to avoid memory leaks, you can refer to the following suggestions。
1. Try not to use arrays with the `global`,`static` keyword, and if you do make sure they don't expand infinitely
2. For unfamiliar classes, try not to use a singleton and initialize with the new keyword. If a singleton is needed, check if it has an infinitely expanding array property
