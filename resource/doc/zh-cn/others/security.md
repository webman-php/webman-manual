# 安全

## 运行用户
建议将运行用户设置为权限较低的用户，例如与nginx运行用户一致。运行用户在 `config/server.php` 中的`user`和`group`中设置。
类似的自定义进程的用户是通过`config/process.php`中的`user`和`group`来指定。
需要注意的是，monitor进程不要设置运行用户，因为它需要高权限才能正常工作。

## XSS过滤
考虑通用性，webman没有对请求进行XSS过滤。开发者需要自己决定XSS过滤时机，例如请求处理前进行XSS过滤，或者在模版里统一进行XSS过滤，建议输出时过滤。
统一在请求处理前过滤可参考 [请求-自定义请求对象](https://www.workerman.net/doc/webman/request.html#%E8%87%AA%E5%AE%9A%E4%B9%89%E8%AF%B7%E6%B1%82%E5%AF%B9%E8%B1%A1)。(不建议更改`support/Request.php`，因为这可能会影响其它应用插件的行为)

## 防止SQL注入
为了防止SQL注入，请尽量使用ORM，如 [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html)、[think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)，使用时尽量不要自己组装SQL。

## nginx代理
当你的应用需要暴露给外网用户时，强烈建议在webman前增加一个nginx代理，这样可以过滤一些非法HTTP请求，提高安全性。具体请参考[nginx代理](nginx-proxy.md)
