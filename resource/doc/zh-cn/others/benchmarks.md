# 压力测试

## 压测结果受到哪些因素影响？
* 压力机到服务器的网络延迟 (建议内网或本机压测)
* 压力机到服务器的带宽 (建议内网或本机压测)
* 是否开启HTTP keep-alive (建议开启)
* 并发数是否足够 (外网压测要尽量开启更大的并发)
* 服务端进程数是否合理 (helloworld业务进程数建议与cpu数相同，数据库业务进程数建议为cpu的四倍及以上)
* 业务自身性能 (例如是否使用了外网数据库)


## HTTP keep-alive是什么？
HTTP Keep-Alive机制是一种用于在单个TCP连接上发送多个HTTP请求和响应的技术，它对于性能测试结果影响很大，关闭keep-alive后QPS可能成倍下降。
目前浏览器都是默认开启keep-alive的，也就是浏览器访问某一个http地址后会将连接暂时保留不关闭，下一次请求时复用这个连接，用来提高性能。
压测时建议开启keep-alive。

## 压测时如何开启HTTP keep-alive？
如果是用的ab程序压测需要加-k参数，例如 `ab -n100000 -c200 -k http://127.0.0.1:8787/`。
apipost需要在返回头中返回gzip头才能开启keep-alive(apipost的bug，参考下面)。
其它压测程序一般会默认开启。

## 为什么通过外网压测QPS很低？
外网延迟很大导致QPS很低，是正常现象。例如压测baidu页面QPS可能只有几十。
建议内网或者本机压测，排除网络延迟影响。
如果一定要在外网压测，可以通过增加并发数来增加吞吐量(需保证带宽充足)。

## 为什么经过nginx代理后性能下降？
nginx运行需要消耗系统资源。同时，nginx和webman之间的通讯也需要消耗一定的资源。
然而，系统的资源是有限的，webman无法获取到所有的系统资源，因此，整个系统的性能可能会有所下降是正常现象。
为了尽可能减少nginx代理带来的性能影响，可以考虑关闭nginx日志(`access_log off;`)，
开启nginx到webman之间的keep-alive，参考[nginx代理](nginx-proxy.md)。

另外https和http相比会损耗更多资源，因为https需要进行SSL/TLS握手，数据加密解密，包的尺寸变大占用更多带宽，这些会导致性能下降。
压测如果用的是短链接(不开启HTTP keep-alive)，每次请求都需要额外的SSL/TLS握手通讯，性能会大幅降低。建议压测https开启HTTP keep-alive。


## 如何知道系统已经达到性能极限？
一般来说CPU达到100%时说明系统性能已经达到极限。如果CPU还有空闲说明还没达到极限，这时候可以适当增加并发提高QPS。
如果增加并发无法提高QPS则可能是webman进程数不够，请适当增加webman进程。如果仍然无法提高考虑带宽是否足够。

## 为什么我压测结果是webman性能低于go的gin框架？
[techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2)压测显示webman不管在纯文本、数据库查询、数据库更新等所有指标都高于gin近一倍左右。
如果你的结果不一样，可能是因为你在webman中使用了ORM带来了较大的性能损失，可尝试 webman+原生PDO 与 gin+原生SQL 比较。

## webman中使用ORM性能会损失多少？
以下是一组压测数据

**环境**
服务器阿里云4核 4G，本地MySQL数据库，从10万条记录中随机查询一条数据json返回，本机压测。

**如果使用原生PDO**
webman QPS为1.78万

**如果使用laravel的Db::table()**
webman QPS降到 0.94万QPS

**如果使用laravel的Model**
webmanQPS降到 0.72万QPS

thinkORM结果类似，区别不大。

> **提示**
> 虽然使用ORM性能会有所下降，但是对于99%的业务来说性能已经严重溢出，如果你刚好是那1%也可以通过增加cpu或者服务器轻松解决。
> 我们应该在开发效率、可维护性、性能等多个指标中找到一个平衡点，而不是一味追求性能。

## 为什么用apipost压测QPS很低？
apipost的压力测试模块有bug，如果服务端不返回gzip头则无法保持keep-alive，导致性能大幅下降。
解决办法返回时将数据压缩并添加gzip头，例如
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
除此之外，apipost一些情况下无法打出满意的压力，这表现为同样的并发，使用apipost要比ab低50%左右的QPS。
压测建议用ab、wrk或其它专业的压测软件而不是apipost。

## 设置合适的进程数
webman默认开启cpu*4的进程数。实际上无网路IO的helloworld业务压测进程数开成与cpu核数一致性能最优，因为可以减少进程切换开销。
如果是带数据库、redis等阻塞IO业务，进程数可设置为cpu的3-8倍，因为这时需要更多的进程提高并发，而进程切换开销相对与阻塞IO则基本可以忽略。


## 压力测试一些参考范围

**云服务器 4核 4G 16进程 本机/内网压测**

| - | 开启keep-alive | 未开启keep-alive |
|--|-----|-----|
| hello world | 8-16万QPS | 1-3万QPS |
| 数据库单查询 | 1-2万QPS | 1万QPS |

[**第三方techempower压测数据**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


## 压测命令示例

**ab**
```
# 100000请求 200并发 开启keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100000请求 200并发 未开启keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# 200 并发压测10秒 开启keep-alive(默认)
wrk -c 200 -d 10s http://example.com
```
