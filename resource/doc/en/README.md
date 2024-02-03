# What is webman

webman is a high-performance HTTP service framework developed based on [workerman](https://www.workerman.net). It is used to replace the traditional php-fpm architecture, providing highly scalable and high-performance HTTP services. With webman, you can develop websites, HTTP interfaces, or microservices.

In addition, webman also supports custom processes, enabling it to perform any task that workerman can do, such as WebSocket services, IoT, games, TCP services, UDP services, Unix socket services, and more.

# Concept of webman
**Providing maximum scalability and the strongest performance with the minimum kernel.**

webman only provides the core functions (routing, middleware, session, custom process interface). All other functionalities are reused from the Composer ecosystem. This means you can use the most familiar functional components in webman, such as using Laravel's `illuminate/database` for database development, ThinkPHP's `ThinkORM`, or other components like `Medoo`. Integrating them into webman is straightforward.

# Features of webman

1. High stability. webman is built on workerman, which has always been an industry-leading socket framework with minimal bugs and high stability.

2. Ultra-high performance. webman's performance is 10-100 times higher than traditional php-fpm frameworks and roughly twice as high as frameworks like gin and echo in Go.

3. High reusability. Most Composer components and libraries can be reused without modification.

4. High scalability. It supports custom processes and can perform any task that workerman can do.

5. Extremely simple and user-friendly, with very low learning costs, code writing is no different from traditional frameworks.

6. Uses the MIT open-source license, which is highly permissive and friendly.

# Project Links
GitHub: https://github.com/walkor/webman **Don't be stingy with your stars**

Gitee: https://gitee.com/walkor/webman **Don't be stingy with your stars**

# Third-party authoritative benchmark data

![](../assets/img/benchmark1.png)

With database query tasks, webman achieves a single-machine throughput of 390,000 QPS, which is nearly 80 times higher than the traditional Laravel framework based on the php-fpm architecture.

![](../assets/img/benchmarks-go.png)

With database query tasks, webman's performance is roughly twice as high as similar Go language web frameworks.

The above data is from [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)