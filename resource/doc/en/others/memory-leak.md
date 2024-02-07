# About Memory Leaks
webman is a resident memory framework, so we need to pay attention to memory leaks. However, developers do not need to worry too much, because memory leaks occur under very extreme conditions and are easy to avoid. The development experience with webman is basically the same as that with traditional frameworks, and there is no need to perform unnecessary operations for memory management.

> **Tip**
> The monitor process built into webman monitors the memory usage of all processes. If a process's memory usage is about to reach the value set in `memory_limit` in php.ini, the corresponding process will be automatically safely restarted, leading to the release of memory. This process has no impact on the business. 

## Memory Leak Definition
As the number of requests increases, the memory used by webman also **increases infinitely** (note that it is **infinitely increasing**), reaching several hundred megabytes or even more, which is a memory leak. If the memory usage increases but then stops increasing, it is not considered a memory leak.

It is quite normal for a process to use tens of megabytes of memory. When a process is handling super-large requests or maintaining a massive number of connections, it is common for a single process's memory usage to reach over a hundred megabytes. After this, PHP may not immediately return all of the used memory to the operating system. Instead, it may retain it for reuse, so it is normal for the memory usage to increase after processing a large request and not release memory. This is a normal phenomenon. (Calling the gc_mem_caches() method can release some idle memory)

## How Memory Leaks Occur
**Memory leaks occur when the following two conditions are met:**
1. There is an array with a **long life cycle** (note that it is a long life cycle array, ordinary arrays are fine)
2. And this array with a **long life cycle** expands infinitely (the business infinitely inserts data into it and never cleans it up)

If conditions 1 and 2 are **simultaneously met** (note that this is simultaneously met), a memory leak will occur. If either of the above conditions is not met or only one of the conditions is met, it is not a memory leak.

## Arrays with Long Life Cycle
In webman, arrays with long life cycles include:
1. Arrays with the static keyword
2. Arrays as properties in singletons
3. Arrays with the global keyword

> **Note**
> webman allows the use of data with long life cycles, but it needs to be ensured that the data within the data is finite and the number of elements will not infinitely expand.

Here are specific examples:

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

The `$data` array defined with the `static` keyword is an array with a long life cycle. In the example, the `$data` array constantly expands with each request, leading to a memory leak.

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

`Cache::instance()` returns a Cache singleton, which is an instance with a long life cycle. Although its `$data` property does not use the `static` keyword, because the class itself has a long life cycle, `$data` is also an array with a long life cycle. As different keys are constantly added to the `$data` array, the memory used by the program also increases, leading to a memory leak.

> **Note**
> If the keys added by `Cache::instance()->set(key, value)` are limited in number, there will be no memory leak because the `$data` array does not infinitely expand.

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
Arrays defined with the global keyword do not get recycled after a function or class method completes, so they have a long life cycle. In the above code, a memory leak occurs as the requests continue to increase. Likewise, arrays defined within functions or methods with the static keyword are also long life cycle arrays, and if the array infinitely expands, there will be a memory leak. For example:
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
It is recommended that developers do not focus too much on memory leaks because they rarely occur. If they unfortunately occur, we can use stress testing to find the code causing the leak and determine the problem. Even if developers cannot locate the leak, webman's built-in monitor service will timely and safely restart the process experiencing the memory leak to release memory.

If you really want to avoid memory leaks as much as possible, you can consider the following suggestions:
1. Try not to use arrays with the `global` and `static` keywords, and if used, ensure that they do not infinitely expand.
2. For unfamiliar classes, try not to use singletons; instead, initialize with the `new` keyword. If a singleton is necessary, check if it has array properties that infinitely expand.
