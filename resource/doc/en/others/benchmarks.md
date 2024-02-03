# Load Testing

### What factors influence the load test results?
* Network latency from the stress machine to the server (intranet or local stress testing recommended)
* Bandwidth between the stress machine and the server (intranet or local stress testing recommended)
* Whether HTTP keep-alive is enabled (recommended to enable)
* Adequate concurrency (for external network stress testing, it is advisable to use a larger concurrency)
* Whether the server-side process number is reasonable (for helloworld business processes, it is recommended to match the number of CPUs; for database business processes, it is recommended to be four times or more of the number of CPUs)
* Business performance (e.g., using external network database)

### What is HTTP keep-alive?
HTTP Keep-Alive is a technique used to send multiple HTTP requests and responses over a single TCP connection, which significantly impacts performance test results. Disabling keep-alive may result in a significant decrease in QPS. Currently, browsers default to enabling keep-alive, meaning they temporarily retain the connection after accessing an HTTP address and reuse it for the next request to improve performance. It is recommended to enable keep-alive during load testing.

### How to enable HTTP keep-alive during load testing?
If using the ab program for load testing, the -k parameter should be added, for example, `ab -n100000 -c200 -k http://127.0.0.1:8787/`. For apipost, returning the gzip header in the response header is required to enable keep-alive (a bug in apipost, as mentioned below). Other load testing programs usually have keep-alive enabled by default.

### Why is the QPS very low during external network load testing?
The high external network latency leads to the low QPS, which is a normal phenomenon. For example, load testing the Baidu page may result in only tens of QPS. It is recommended to perform intranet or local load testing to eliminate the effects of network latency. If external network load testing is necessary, increasing the concurrency to improve throughput (ensuring sufficient bandwidth) is recommended.

### Why does performance decrease after passing through the nginx proxy?
Running nginx consumes system resources. Additionally, communication between nginx and webman also requires a certain amount of resources. However, system resources are limited and webman cannot access all of them, so a decrease in overall system performance is normal. To minimize the performance impact brought by the nginx proxy, consider disabling nginx logging (`access_log off;`), and enabling keep-alive between nginx and webman, as referenced in [nginx proxy](nginx-proxy.md).

Furthermore, HTTPS consumes more resources compared to HTTP because it requires SSL/TLS handshake, data encryption/decryption, and larger packet sizes occupying more bandwidth, which can lead to a performance decrease. During load testing, if using a short connection (without enabling HTTP keep-alive), each request requires additional SSL/TLS handshake communication, resulting in a significant performance decrease. It is recommended to enable HTTP keep-alive when load testing HTTPS.

### How to know if the system has reached its performance limit?
Generally, when the CPU reaches 100%, it indicates that the system's performance has reached its limit. If the CPU is still idle, it means the limit has not been reached yet, and increasing concurrency can improve QPS. If increasing concurrency does not improve QPS, it may be because the number of webman processes is insufficient. In this case, consider increasing the number of webman processes. If performance still cannot be improved, consider whether there is sufficient bandwidth.

### Why are my load test results showing that webman's performance is lower than the Go's Gin framework?
[Techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) load testing indicates that webman's performance in all indicators, such as pure text, database query, and database update, is about twice as high as Gin's.
If your results are different, it may be due to using an ORM in webman, resulting in a significant performance loss. It is recommended to try webman + native PDO and compare it with Gin + native SQL.

### How much performance loss is incurred by using ORM in webman?
Here is a set of load testing data:

**Environment**
Alibaba Cloud server with 4 cores and 4GB RAM, random retrieval of one data record from 100,000 records and returning it as JSON.

**If using native PDO**
webman QPS is 17,800.

**If using Laravel's Db::table()**
webman QPS decreases to 9,400.

**If using Laravel's Model**
webman QPS decreases to 7,200.

Similar results are seen with ThinkORM, with minimal differences.

> **Note**
> While using ORM results in some performance decrease, it is sufficient for most businesses. We should find a balance point among multiple indicators such as development efficiency, maintainability, and performance, rather than blindly pursuing performance.

### Why is the QPS very low when load testing with apipost?
The load testing module of apipost has a bug – if the server does not return the gzip header, keep-alive cannot be maintained, resulting in a significant decrease in performance. To solve this issue, compress the data during the return and add the gzip header, for example:
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
Apart from this, apipost may not be able to generate satisfactory pressure in some cases. This is evident in lower QPS when using apipost compared to ab under the same concurrency, typically around 50% lower. It is advisable to use ab, wrk, or other professional load testing software for load testing, instead of apipost.

### Setting the appropriate number of processes
webman defaults to opening a number of processes equal to CPU * 4. In fact, for helloworld business pressure testing without network IO, the number of processes set to match the number of CPU cores provides optimal performance as it reduces process switch overhead. If it involves blocking IO such as database and Redis, the number of processes can be set to 3-8 times the number of CPUs as more processes are required to increase concurrency, and the process switch overhead in relation to blocking IO can be ignored.

### Reference range for some load testing

**Cloud server 4-core 4GB 16 processes local/stress testing**

| - | Keep-Alive Enabled | Keep-Alive Disabled |
|--|-----|-----|
| helloworld | 80,000 - 160,000 QPS | 10,000 - 30,000 QPS |
| Database single query | 10,000 - 20,000 QPS | 10,000 QPS |

[**Third-party techempower load testing data**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Load Testing Command Examples

**ab**
```
# 100,000 requests, 200 concurrency, keep-alive enabled
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100,000 requests, 200 concurrency, keep-alive disabled
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# 200 concurrency, 10 seconds of load testing, keep-alive enabled (default)
wrk -c 200 -d 10s http://example.com
```