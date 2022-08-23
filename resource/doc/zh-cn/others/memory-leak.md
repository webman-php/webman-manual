# 关于内存泄漏
webman是常驻内存框架，所以我们需要稍微关注下内存泄漏的情况。不过开发者不必过于担心，因为内存泄漏发生在非常极端的条件下，而且很容易规避。webman开发与传统框架开发体验基本一致，不必为内存管理做多余的操作。

> **提示**
> webman自带的monitor进程会监控所有进程内存使用情况，如果进程使用内存即将达到php.ini里`memory_limit`设定的值时，会自动安全重启对应的进程，达到释放内存的作用，期间对业务没有影响。

## 内存泄漏定义
随着请求的不断增加，webman占用的内存也**无限增加**(注意是**无限增加**)，达到几百M甚至更多，这种是内存泄漏。
如果是内存有增长，但是后面不再增长不算内存泄漏。

一般进程占用几十M内存是很正常的情况，当进程处理超大请求或者维护海量连接时，单个进程内存占用可能会达到上百M也是常有的事。这部分内存使用后php可能并不会全部交还操作系统。而是留着复用，所以可能会出现处理某个大请求后内存占用变大不释放内存的情况，这是正常现象。(调用gc_mem_caches()方法可以释放部分空闲内存)


## 内存泄漏是如何发生的
**内存泄漏发生必须满足以下两个条件：**
1. 存在**长生命周期的**数组(注意是长生命周期的数组，普通数组没事)
2. 并且这个**长生命周期的**数组会无限扩张(业务无限向其插入数据，从不清理数据)

如果1 2条件**同时满足**(注意是同时满足)，那么将会产生内存泄漏。反之不满足以上条件或者只满足其中一个条件则不是内存泄漏。


## 长生命周期的数组

webman里长生命周期的数组包括：
1. static关键字的数组
2. 单例的数组属性
3. global关键字的数组


> **注意**
> webman中允许使用长生命周期的数据，但是需要保证数据内的数据是有限的，元素个数不会无限扩张。


以下分别举例说明

#### 无限膨胀的static数组
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

以`static`关键字定义的`$data`数组是长生命周期的数组，并且示例中`$data`数组随着请求不断增加而不断膨胀，导致内存泄漏。

#### 无限膨胀的单例数组属性
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

调用代码
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

`Cache::instance()`返回一个Cache单例，它是一个长生命周期的类实例，虽然它的`$data`属性虽然没有使用`static`关键字，但是由于类本身是长生命周期，所以`$data`也是长生命周期的数组。随着不断向`$data`数组里添加不同key的数据，程序占用内存也月来越大，造成内存泄漏。

> **注意**
> 如果 Cache::instance()->set(key, value) 添加的key是有限数量的，则不会内存泄漏，因为`$data`数组并没有无限膨胀。

#### 无限膨胀的global数组
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
global 关键字定义的数组并不会在函数或者类方法执行完毕后回收，所以它是长生命周期的数组，以上代码随着请求不断增加会产生内存泄漏。同理在函数或者方法内以static关键字定义的数组也是长生命周期的数组，如果数组无限膨胀也会内存泄漏，例如：
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

## 建议
建议开发者不用特别关注内存泄漏，因为它极少发生，如果不幸发生我们可以通过压测找到哪段代码产生泄漏，从而定位出问题。即使开发者没有找到泄漏点，webman自带的monitor服务会适时安全重启发生内存泄漏的进程，释放内存。

如果你实在想尽量规避内存泄漏，可以参考以下建议。
1. 尽量不使用`global`,`static`关键字的数组，如果使用确保其不会无限膨胀
2. 对于不熟悉的类，尽量不使用单例，用new关键字初始化。如果需要单例，则查看其是否有无限膨胀的数组属性
