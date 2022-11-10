# Plugins
PluginsDivide**Basic Plugins**和**Application Plugin**。

#### Basic Plugins
The base plugin can be understood as some webman base components, it may be a generic class library (such as webman/think-orm), may be a generic middleware (such as webman/cors), or a set of routing configuration (such as webman/auto-route), or a custom process (such as webman /redis-queue), etc.。


should be introduced in[Basic Plugins](plugin/base.md)

> **Note**
> Required for base pluginswebman>=1.2.0

#### Application Plugin
Application plugin is a complete application, such as a question and answer system, CMS system, mall system, etc.。
should be introduced in[Application Plugin](plugin/app.md)

> **Application Plugin**
> Required for application pluginswebman>=1.4.0
