# Plugins
Plugins are divided into **basic plugins** and **application plugins**.

#### Basic Plugins
Basic plugins can be understood as some basic components of webman. It could be a general-purpose library (e.g. webman/think-orm), a general middleware (e.g. webman/cors), a set of route configurations (e.g. webman/auto-route), or a custom process (e.g. webman/redis-queue), and so on.

For more information, please refer to [Basic Plugins](plugin/base.md).

> **Note**
> Basic plugins require webman>=1.2.0

#### Application Plugins
Application plugins are a complete application, such as a Q&A system, CMS system, e-commerce system, etc.
For more information, please refer to [Application Plugins](app/app.md).

> **Application Plugins**
> Application plugins require webman>=1.4.0
