# 目录结构
```
.
├── app                           应用目录
│   ├── controller                控制器目录
│   ├── model                     模型目录
│   ├── view
│   └── middleware                中间件目录
│       └── StaticFile.php        自带静态文件中间件
├── config                        配置目录
│   ├── app.php                   应用配置
│   ├── autoload.php              这里配置的文件会被自动加载
│   ├── bootstrap.php             进程启动时onWorkerStart时运行的回调配置
│   ├── container.php             容器配置
│   ├── dependence.php            容器依赖配置
│   ├── database.php              数据库配置
│   ├── exception.php             异常配置
│   ├── log.php                   日志配置
│   ├── middleware.php            中间件配置
│   ├── process.php               自定义进程配置
│   ├── redis.php                 redis配置
│   ├── route.php                 路由配置
│   ├── server.php                端口、进程数等服务器配置
│   ├── view.php                  视图配置
│   ├── static.php                静态文件开关及静态文件中间件配置
│   ├── translation.php           多语言配置
│   └── session.php               session配置
├── public                        静态资源目录
├── process                       自定义进程目录
├── runtime                       应用的运行时目录，需要可写权限
├── start.php                     服务启动文件
├── vendor                        composer安装的第三方类库目录
└── support                       类库适配(包括第三方类库)
    ├── Db.php                    数据库适配
    ├── Request.php               请求类
    ├── Response.php              响应类
    ├── bootstrap                 进程启动onWorkerStart时调用的类目录
    │   ├── Log.php               进程启动时初始化日志类
    │   ├── Redis.php             进程启动时初始化redis类
    │   ├── Session.php           进程启动时初始化session类
    │   └── db                    进程启动时数据库相关初始化
    │       └── Laravel.php       进程启动时初始化laravel的数据库类
    ├── exception                 异常相关
    │   ├── BusinessException.php 业务异常类
    │   └── Handler.php           业务异常捕获处理类
    ├── helpers.php               助手函数
    ├── middleware                中间件目录
    │   └── Test.php              一个测试中间件
    └── view                      视图类目录，支持多个模板引擎
        ├── Blade.php             Blade视图类
        ├── Raw.php               原生视图类
        ├── ThinkPHP.php          ThinkPHP视图类
        └── Twig.php              Twig视图类
```