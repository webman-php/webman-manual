# 目录结构
```
.
├── app                           应用目录
│   ├── controller                控制器目录
│   ├── model                     模型目录
│   ├── view                      视图目录
│   ├── middleware                中间件目录
│   │   └── StaticFile.php        自带静态文件中间件
│   ├── process                   自定义进程目录
│   │   ├── Http.php              Http进程
│   │   └── Monitor.php           监控进程
│   └── functions.php             业务自定义函数写到这个文件里
├── config                        配置目录
│   ├── app.php                   应用配置
│   ├── autoload.php              这里配置的文件会被自动加载
│   ├── bootstrap.php             进程启动时onWorkerStart时运行的回调配置
│   ├── container.php             容器配置
│   ├── dependence.php            容器依赖配置
│   ├── database.php              数据库配置
│   ├── exception.php             异常配置
│   ├── log.php                   日志配置
│   ├── middleware.php            中间件配置
│   ├── process.php               自定义进程配置
│   ├── redis.php                 redis配置
│   ├── route.php                 路由配置
│   ├── server.php                端口、进程数等服务器配置
│   ├── view.php                  视图配置
│   ├── static.php                静态文件开关及静态文件中间件配置
│   ├── translation.php           多语言配置
│   └── session.php               session配置
├── public                        静态资源目录
├── runtime                       应用的运行时目录，需要可写权限
├── start.php                     服务启动文件
├── vendor                        composer安装的第三方类库目录
└── support                       类库适配(包括第三方类库)
    ├── Request.php               请求类
    ├── Response.php              响应类
    ├── helpers.php               助手函数(业务自定义函数请写到app/functions.php)
    └── bootstrap.php             进程启动后初始化脚本
```
