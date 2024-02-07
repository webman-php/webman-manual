# Stress Testing

### What factors affect the stress test results?
* Network latency from the stress machine to the server (it is recommended to perform the test on the internal network or on the local machine)
* Bandwidth from the stress machine to the server (it is recommended to perform the test on the internal network or on the local machine)
* Whether HTTP keep-alive is enabled (it is recommended to enable keep-alive)
* Whether the concurrency is sufficient (for external network stress tests, it is recommended to increase the concurrency as much as possible)
* Whether the number of server processes is reasonable (for helloworld business processes, it is recommended to have the same number of processes as CPU cores; for database business processes, it is recommended to have four times or more processes than CPU cores)
* The performance of the business itself (for example, whether an external network database is used)

### What is HTTP keep-alive?
HTTP Keep-Alive is a technique used to send multiple HTTP requests and responses over a single TCP connection. It has a significant impact on performance test results, and disabling keep-alive may result in a significant decrease in QPS.
Currently, most browsers have keep-alive enabled by default, which means that when a browser accesses an HTTP address, it temporarily keeps the connection open and reuses it for the next request to improve performance.
It is recommended to enable keep-alive during stress testing.

### How to enable HTTP keep-alive during stress testing?
If you are using the ab program for stress testing, you need to add the -k parameter, for example `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Apipost needs to return the gzip header in the response headers to enable keep-alive (a bug in apipost, refer to below).
Most other stress testing programs usually enable keep-alive by default.

### Why is the QPS very low when stress testing from the external network?
The high network latency from the external network causes the QPS to be very low, which is a normal phenomenon. For example, when stress testing the Baidu page, the QPS may be only a few tens.
It is recommended to perform the test on the internal network or on the local machine to eliminate the impact of network latency.
If you must perform the stress test from the external network, you can increase the concurrency to increase the throughput (assuming that the bandwidth is sufficient).

### Why does the performance decrease after nginx reverse proxy is enabled?
Running nginx consumes system resources. At the same time, communication between nginx and webman also consumes a certain amount of resources.
However, system resources are limited, and webman cannot obtain all the system resources, so it is normal for the overall system performance to decrease.
To minimize the performance impact of nginx reverse proxy, you can consider disabling nginx logs (`access_log off;`) and enabling keep-alive between nginx and webman, please refer to [nginx reverse proxy](nginx-proxy.md).
In addition, HTTPS consumes more resources compared to HTTP because HTTPS requires SSL/TLS handshake, data encryption and decryption, and larger packet size occupies more bandwidth, all of which can cause decreased performance.
If short connections are used during stress testing (without enabling HTTP keep-alive), each request requires additional SSL/TLS handshake communication, which greatly reduces performance. It is recommended to enable HTTP keep-alive for stress testing of HTTPS.

### How to know when the system has reached its performance limit?
In general, when the CPU reaches 100%, it means that the system performance has reached its limit. If the CPU still has idle time, it means that the limit has not been reached, and at this time, you can increase the concurrency to improve QPS.
If increasing the concurrency cannot improve QPS, it may be because the number of webman processes is not enough. In this case, you can increase the number of webman processes. If it still cannot be improved, consider whether the bandwidth is sufficient.

### Why is the performance of webman lower than the Golang Gin framework in my stress test results?
According to the [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) stress test, webman performs about twice as well as Gin in terms of all metrics, including plaintext, database query, and database update.
If your results are different, it may be because you are using ORM in webman, which introduces a significant performance loss. You can try comparing webman+native PDO with Gin+native SQL.

### How much performance loss is there when using ORM in webman?
Here is a set of stress test data:

**Environment**
Aliyun server with 4 cores and 4 GB of RAM, randomly select one data from 100,000 records and return it in JSON.

**If using native PDO**
Webman's QPS is 17,800.

**If using laravel's Db::table()**
Webman's QPS drops to 9,400.

**If using laravel's Model**
Webman's QPS drops to 7,200.

thinkORM has similar results, with little difference.

> **Note**
> Although using ORM will result in a performance decline, it is sufficient for most business cases. We should find a balance among development efficiency, maintainability, and performance instead of blindly pursuing performance.

### Why is the QPS very low when using apipost for stress testing?
The stress testing module in apipost has a bug that prevents keep-alive from being maintained if the server does not return the gzip header, resulting in a significant performance drop.
The solution is to compress the data and add the gzip header when returning the response, for example:
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
In addition, apipost may not be able to generate satisfactory pressure in some cases, resulting in a QPS that is about 50% lower than ab under the same concurrency.
It is recommended to use ab, wrk, or other professional stress testing software instead of apipost for stress testing.

### Setting the appropriate number of processes
Webman enables cpu*4 processes by default. In fact, for hello world business processes without network IO, the optimal number of processes for stress testing is the same as the number of CPU cores, as it can reduce the overhead of process switching.
If it is a business with blocking IO such as databases and Redis, the number of processes can be set to 3-8 times the number of CPU cores, as more processes are needed to increase concurrency, and the overhead of process switching can be ignored relative to the blocking IO.

### Some reference ranges for stress testing

**Cloud server with 4 cores and 4 GB RAM, 16 processes, local network/internal network stress testing**

| - | Enable Keep-Alive | Disable Keep-Alive |
|--|-----|-----|
| Hello World | 80,000-160,000 QPS | 10,000-30,000 QPS |
| Database Query | 10,000-20,000 QPS | 10,000 QPS |

[**Third-party techempower stress test data**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Stress test command examples

**ab**
```shell
# 100,000 requests, 200 concurrency, enable keep-alive
ab -n 100000 -c 200 -k http://127.0.0.1:8787/

# 100,000 requests, 200 concurrency, disable keep-alive
ab -n 100000 -c 200 http://127.0.0.1:8787/
```

**wrk**
```shell
# 200 concurrency, 10 seconds stress testing, enable keep-alive (default)
wrk -c 200 -d 10s http://example.com
```
