# What is Webman?

webmanThe table contains a[workerman](https://www.workerman.net)When the access address isHTTPService Framework。webmanfor replacing traditionalphp-fpmarchitecture，Provide ultra-high performance extensibleHTTPservice。You can usewebmanDevelopment Site，Game ServerHTTPinterface or microservice。

In addition to that, webman supports custom processes that can do anything workerman can do, such as websocket services, IoT, games, TCP services, UDP services, unix socket services, etc.。

# Webman Concept
**Provides maximum scalability and performance with minimal kernels。**

webmanOnly the most core features (routing, middleware, session, custom process interfaces) are provided. The rest of the functionality is reused in the composer ecology, which means you can use the most familiar functional components in webman. For example, for the database developers can choose to use Laravel's `illuminate/database`, or ThinkPHP's `ThinkORM`, or other components such as ` Medoo`. It's very easy to integrate them in webman。

# Webman with the following features

1、high stability. webman is based on workerman development, workerman has been the industry's most stable socket framework with very few bugs。

2、Super high performance. webman performance is about 10-100 times higher than traditional php-fpm framework, and about twice as high as go's gin echo and other frameworks。

3、Highly reusable. No need to modify, can reuse most composer components and class libraries。

4、Highly scalable. Supports custom processes that can do anything workerman can do。

5、super easy to use , very low learning cost , code writing and traditional framework no difference 。

6、Use the most lenient and friendly MIT open source protocol。

# Project address
GitHub: https://github.com/walkor/webman **Don't be stingy with your little stars oh**

Code Cloud: https://gitee.com/walkor/webman **Don't be stingy with your little stars oh**

# Third party authoritative pressure test data


![](../assets/img/benchmark1.png)

With database query service, webman single machine throughput reached 390,000 QPS, which is almost 80 times higher than traditional laravel framework with php-fpm architecture。


![](../assets/img/benchmarks-go.png)

With database query service, webman is about double the performance of the same type of go language web framework。


The above data is from[techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
