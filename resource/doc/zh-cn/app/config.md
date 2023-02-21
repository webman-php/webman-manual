# 配置文件

插件的配置与普通webman项目一样，不过插件的配置一般只对当前插件有效，对主项目一般无影响。
例如`plugin.foo.app.controller_suffix`的值只影响插件的控制器后缀，对主项目没有影响。
例如`plugin.foo.app.controller_reuse`的值只影响插件是否复用控制器，对主项目没有影响。
例如`plugin.foo.middleware`的值只影响插件的中间件，对主项目没有影响。
例如`plugin.foo.view`的值只影响插件所使用的视图，对主项目没有影响。
例如`plugin.foo.container`的值只影响插件所使用的容器，对主项目没有影响。
例如`plugin.foo.exception`的值只影响插件的异常处理类，对主项目没有影响。

但是因为路由是全局的，所以插件配置的路由也是影响全局的。

## 获取配置
获取某个插件配置方法为 `config('plugin.{插件}.{具体的配置}');`，例如获取`plugin/foo/config/app.php`的所有配置方法为`config('plugin.foo.app')`
同样的，主项目或者其它插件都可以用`config('plugin.foo.xxx')`来获取foo插件的配置。

## 不支持的配置
应用插件不支持server.php，session.php配置，不支持`app.request_class`，`app.public_path`，`app.runtime_path`配置。
