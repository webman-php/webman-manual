# 执行流程

## 进程启动流程

执行 php start.php start 后执行流程如下：

1. 载入config/下的配置
2. 设置Worker的相关配置如 `pid_file` `stdout_file` `log_file` `max_package_size` 等
3. 创建webman进程，并监听端口（默认8787）
4. 根据配置创建自定义进程
5. webman进程和自定义进程启动后执行以下逻辑(以下都是执行在onWorkerStart里)：
  ① 加载 `config/autoload.php` 里设置的文件，如 `app/functions.php`
  ② 加载 `config/middleware.php` (包括`config/plugin/*/*/middleware.php`)里设置的中间件
  ③ 执行 `config/bootstrap.php` (包括`config/plugin/*/*/bootstrap.php`)里设置类的start方法，用于初始化一些模块，比如Laravel数据库初始化连接
  ④ 载入 `config/route.php` (包括`config/plugin/*/*/route.php`)里定义的路由

## 处理请求流程
1. 判断请求url是否对应public下的静态文件，是的话返回文件(结束请求)，不是的话进入2
2. 根据url判断是否命中某个路由，没命中进入3、命中进入4
3. 是否关闭了默认路由，是的话返回404(结束请求)，不是的话进入4
4. 找到请求对应控制器的中间件，按顺序执行中间件前置操作(洋葱模型请求阶段)，执行控制器业务逻辑，执行中间件后置操作(洋葱模型响应阶段)，请求结束。(参考中[间件洋葱模型](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))

