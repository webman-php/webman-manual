# nginx代理
当webman作为网站应用时，建议在webman前增加一个nginx代理，这样有以下好处。

 - 静态资源由nginx处理，让webman专注业务逻辑处理
 - 让多个webman应用共用80、443端口，通过域名区分不同站点，实现单台服务器多域名多站点
 - 实现php-fpm于webman共存
 - 可以实现一个服务器运行多个webman站点提供外网服务
 - 可以实现webman站点和
