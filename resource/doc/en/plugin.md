# Plugins
Plugins are divided into **basic plugins** and **application plugins**.

#### Basic Plugins
Basic plugins can be understood as some basic components of webman. It may be a common class library (such as webman/think-orm), a common middleware (such as webman/cors), or a set of route configurations (such as webman/auto-route), or a custom process (such as webman/redis-queue), and so on.

For more information, please refer to [Basic Plugins](plugin/base.md).

> **Note**
> Basic plugins require webman>=1.2.0

#### Application Plugins
Application plugins are complete applications, such as Q&A systems, CMS systems, e-commerce systems, and so on.
For more information, please refer to [Application Plugins](app/app.md).

> **Application Plugins**
> Application plugins require webman>=1.4.0
