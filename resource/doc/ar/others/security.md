# 安全

## 运行用户
建议将运行用户设置为权限较低的用户，例如与nginx运行用户一致。运行用户在 `config/server.php` 中的`user`和`group`中设置。
类似的自定义进程的用户是通过`config/process.php`中的`user`和`group`来指定。
需要注意的是，monitor进程不要设置运行用户，因为它需要高权限才能正常工作。

## 控制器规范
`controller`目录或者子目录下只能放置控制器文件，禁止放置其它类文件，否则在未开启[控制器后缀](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)时，类文件有可能会被url非法访问，造成不可预知的后果。
例如 `app/controller/model/User.php` 实际是Model类，但是却错误的放到了`controller`目录下，在没开启[控制器后缀](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)时，会导致用户可以通过类似`/model/user/xxx`访问`User.php`里的任意方法。
为了彻底杜绝这种情况，强烈建议使用[控制器后缀](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)明确标记哪些是控制器文件。


## XSS过滤
考虑通用性，webman没有对请求进行XSS转义。
webman强烈推荐在渲染时进行XSS转义，而不是在入库前进行转义。
并且twig、blade、think-template等模版会自动执行XSS转义，无需手动转义，非常方便。

> **提示**
> 如果你在入库前进行XSS转义，很可能造成一些应用插件的不兼容问题


## 防止SQL注入
为了防止SQL注入，请尽量使用ORM，如 [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html)、[think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)，使用时尽量不要自己组装SQL。

## nginx代理
当你的应用需要暴露给外网用户时，强烈建议在webman前增加一个nginx代理，这样可以过滤一些非法HTTP请求，提高安全性。具体请参考[nginx代理](nginx-proxy.md)
