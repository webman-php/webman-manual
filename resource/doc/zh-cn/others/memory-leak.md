# 关于内存泄漏
webman是常驻内存框架，所以我们需要稍微关注下内存泄漏的情况。不过开发者不必过于担心，因为内存泄漏发生在非常极端的条件下，而且很容易规避。webman开发与传统框架开发体验基本一致，不必为内存管理做多余的操作。

> **提示**
> webman自带的monitor进程会监控所有进程内存使用情况，如果进程使用内存即将达到php.ini里`memory_limit`设定的值时，会自动安全重启对应的进程，达到释放内存的作用，期间对业务没有影响。

## 内存泄漏定义
随着请求增加，webman内存不断增长是正常现象，一般来说当进程达到一定请求量后(一般百万级别)，内存将停止增长或者偶尔小幅度增长。
大部分业务单个进程内存占用最终会在10M-100M左右保持稳定，单进程内存没超过100M则不用担心。
另外当业务处理大文件，处理大请求，从数据库读取大数据等业务时，PHP会申请大量内存，这部分内存PHP使用后可能会保留下次复用，不会全部交还给操作系统，这时候也会出现内存占用很大的现象，由于内存会被重复利用，所以不用担心。

> **提示**
> 如果是phar打包或者二进制打包项目本包身尺寸很大，打包项目内存占用超过100M也是正常现象。

## 如何确认内存泄露
如果一个进程请求超过百万，内存超过100M，每次请求后内存还在增长，那么有可能发生了内存泄露。

## 如何查找内存泄露
简单的定位方法是通过压测各个接口，找出哪个接口在超过百万次请求后内存还在不断增长。
当发现问题接口后，利用二分法每次注释掉一半的业务代码，直到最终确定哪部分代码有问题。

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

### 无限膨胀的static数组
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

### 无限膨胀的单例数组属性
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

`Cache::instance()`返回一个Cache单例，它是一个长生命周期的类实例，虽然它的`$data`属性虽然没有使用`static`关键字，但是由于类本身是长生命周期，所以`$data`也是长生命周期的数组。随着不断向`$data`数组里添加不同key的数据，程序占用内存也越来越大，造成内存泄漏。

> **注意**
> 如果 Cache::instance()->set(key, value) 添加的key是有限数量的，则不会内存泄漏，因为`$data`数组并没有无限膨胀。

### 无限膨胀的global数组
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
