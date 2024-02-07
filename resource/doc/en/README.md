# What is webman

webman is a high-performance HTTP service framework based on [workerman](https://www.workerman.net). It is used to replace the traditional php-fpm architecture, providing highly scalable and efficient HTTP services. With webman, you can develop websites, HTTP interfaces, or microservices.

In addition, webman supports custom processes, allowing for a wide range of capabilities such as WebSocket services, IoT, gaming, TCP services, UDP services, Unix socket services, and more.

## Core Concept of webman
**Providing maximum scalability and performance with a minimal core.**

webman only provides the most essential features (routing, middleware, session, and custom process interface). All other functionalities are reused from the Composer ecosystem. This means you can use familiar components within webman, such as Laravel's `illuminate/database`, ThinkPHP's `ThinkORM`, or other components like `Medoo`. Integrating them into webman is straightforward.

## Key Features of webman

1. High Stability: Based on workerman development, which has a proven track record of minimal bugs and high stability in the industry.
2. Ultra-High Performance: webman's performance is 10-100 times higher than traditional php-fpm frameworks and about double that of frameworks like Gin and Echo in Go.
3. High Reusability: Most Composer components and libraries can be reused without modification.
4. High Scalability: Supports custom processes that can accomplish a wide range of tasks like workerman.
5. Super Easy to Use: Low learning curve and code writing similar to traditional frameworks.
6. Released under the MIT open-source license, which is very permissive and friendly.

## Project Links
GitHub: https://github.com/walkor/webman **Don't hesitate to give a star!**

Gitee: https://gitee.com/walkor/webman **Don't hesitate to give a star!**

## Third-party Authoritative Benchmark Data

![](../assets/img/benchmark1.png)

With database query business, webman achieves a single-machine throughput of 390,000 QPS, nearly 80 times higher than the Laravel framework within the traditional php-fpm architecture.

![](../assets/img/benchmarks-go.png)

With database query business, webman outperforms similar Go language web frameworks by about double the performance.

The above data is from [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf).
