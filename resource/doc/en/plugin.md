# Plugins
Plugins are divided into **base plugins** and **application plugins**.

#### Base Plugins
Base plugins can be understood as fundamental components of webman. They may be a general-purpose library (such as webman/think-orm), a general middleware (such as webman/cors), a set of route configurations (like webman/auto-route), or a custom process (such as webman/redis-queue), and so on.

For more information, please refer to [Base Plugins](plugin/base.md).

> **Note**
> Base plugins require webman>=1.2.0

#### Application Plugins
Application plugins are complete applications, such as a Q&A system, CMS system, or an e-commerce platform.

For more information, please refer to [Application Plugins](app/app.md).

> **Application Plugins**
> Application plugins require webman>=1.4.0