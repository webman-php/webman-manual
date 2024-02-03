# About Memory Leaks
webman is a resident memory framework, so we need to pay some attention to the situation of memory leaks. However, developers do not need to worry too much, because memory leaks occur under very extreme conditions and are easily avoided. The development experience of webman is basically the same as that of traditional framework development, and there is no need to perform unnecessary operations for memory management.

> **Note**
> The monitor process built into webman will monitor the memory usage of all processes. If the memory usage of a process is about to reach the value set in `memory_limit` in php.ini, the corresponding process will be automatically safely restarted to release memory. This process will not affect the business.

## Definition of Memory Leaks
As the number of requests increases, the memory occupied by webman also increases infinitely (note that it increases infinitely), reaching several hundred megabytes or even more. This is considered memory leaks. If the memory grows but does not increase further, it is not considered a memory leak.

It is quite normal for a process to occupy tens of megabytes of memory. When a process handles very large requests or maintains a massive number of connections, it is common for the memory usage of a single process to reach over a hundred megabytes. After using this part of the memory, PHP may not return all of it to the operating system immediately for reuse, so it may occur that the memory usage becomes larger after processing a large request and does not release the memory. This is a normal phenomenon. (Calling the `gc_mem_caches()` method can release some idle memory.)

## How Memory Leaks Occur
**Memory leaks must satisfy the following two conditions:**
1. There is an array with a **long lifespan** (note that it is a **long lifespan** array, ordinary arrays are not a problem).
2. And this **long lifespan** array will expand infinitely (the business infinitely inserts data into it without clearing the data).

If both conditions 1 and 2 are **met at the same time** (note that it is met at the same time), a memory leak will occur. Otherwise, not meeting the above conditions or meeting only one of the conditions will not cause a memory leak.

## Arrays with Long Lifespan
Arrays with long lifespans in webman include:
1. Arrays with the `static` keyword
2. Arrays as singleton properties
3. Arrays with the `global` keyword

> **Note**
> In webman, it is allowed to use data with long lifespans, but it is necessary to ensure that the data inside the data structure is limited in number, and the number of elements will not expand infinitely.

The following are examples for illustration.

#### Infinitely expanding static array
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

The `$data` array defined with the `static` keyword has a long lifespan, and in the example, the `$data` array expands continuously with each request, leading to memory leaks.

#### Infinitely expanding singleton array property
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

Calling code:
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

`Cache::instance()` returns a Cache singleton, which is a class instance with a long lifespan. Although its `$data` property does not use the `static` keyword, because the class itself has a long lifespan, `$data` is also a long lifespan array. As different keys are continuously added to the `$data` array, the program's memory consumption becomes larger and larger, resulting in memory leaks.

> **Note**
> If the keys added by `Cache::instance()->set(key, value)` are of a limited quantity, there will be no memory leaks because the `$data` array does not expand infinitely.

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

An array defined with the `global` keyword will not be reclaimed after the function or method is executed, so it is a long lifespan array. The above code will cause memory leaks as the requests continue to increase. Similarly, an array defined with the `static` keyword within a function or method is also a long lifespan array. If the array expands infinitely, it will cause memory leaks, for example:
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
It is recommended that developers do not need to pay special attention to memory leaks because they rarely occur. If, by chance, a memory leak occurs, we can use stress testing to find which segment of the code is causing the leak, and thus locate the problem. Even if the developer is unable to find the point of the leak, webman's built-in monitoring service will timely and safely restart the process that experiences memory leaks to release memory.

If you still want to avoid memory leaks as much as possible, you can consider the following suggestions:
1. Try to avoid using arrays with the `global` and `static` keywords. If used, make sure they do not expand infinitely.
2. For unfamiliar classes, try not to use a singleton but instead initialize with the `new` keyword. If a singleton is necessary, check whether it has array properties that expand infinitely.