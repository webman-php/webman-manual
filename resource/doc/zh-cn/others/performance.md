# webman性能


### 传统框架请求处理流程

1. nginx/apache接收请求
2. nginx/apache将请求传递给php-fpm
3. php-fpm初始化环境，如创建变量列表
4. php-fpm调用各个扩展/模块的RINIT
5. php-fpm磁盘读取php文件(使用opcache可避免)
6. php-fpm词法分析、语法分析、编译成opcode(使用opcache可避免)
7. php-fpm执行opcode 包括8.9.10.11
8. 框架初始化，如实例化各种类，包括如容器、控制器、路由、中间件等。
9. 框架连接数据库并权限验证，连接redis
10. 框架执行业务逻辑
11. 框架关闭数据库、redis连接
12. php-fpm释放资源、销毁所有类定义、实例、销毁符号表等
13. php-fpm顺序调用各个扩展/模块的RSHUTDOWN方法
14. php-fpm将结果转发给nginx/apache
15. nginx/apache将结果返回给客户端


### webman的请求处理流程
1. 框架接收请求
2. 框架执行业务逻辑(opcode字节码)
3. 框架将结果返回给客户端

没错，在没有nginx反代的情况下，框架只有这3步。可以说这已经是php框架的极致，这使得webman性能是传统框架的几倍甚至数十倍。

更多参考 [压力测试](benchmarks.md)
