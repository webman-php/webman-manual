# 插件
插件分為**基礎插件**和**應用插件**。

#### 基礎插件
基礎插件可以理解為一些webman的基礎組件，它可能是一個通用的類庫(例如webman/think-orm)，可能是一個通用的中間件(例如webman/cors)，或者一組路由配置(如webman/auto-route)，或是一個自定義進程(例如webman/redis-queue)等等。

更多請參考[基礎插件](plugin/base.md)

> **注意**
> 基礎插件需要webman>=1.2.0

#### 應用插件
應用插件是一個完整的應用，例如問答系統，CMS系統，商城系統等。
更多請參考[應用插件](app/app.md)

> **應用插件**
> 應用插件需要webman>=1.4.0
