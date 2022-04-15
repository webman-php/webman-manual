# webman性能


###传统框架请求处理流程

1. nginx/apache接收请求
2. nginx/apache将请求传递给php-fpm
3. php-fpm初始化环境，如创建变量列表
4. php-fpm调用各个扩展/模块的RINIT
5. php-fpm磁盘读取php文件
6. php-fpm词法分析、语法分析、编译成opcde
7. php-fpm执行opcode 包括
8. 框架例化各种类，包括框架相关的类、如容器、控制器、路由、中间键等。
9. 框架连接数据库、redis
10. 框架执行业务逻辑
11. 框架关闭数据库、redis
12. php-fpm释放资源、销毁所有类定义、实例、销毁符号表等
13. php-fpm顺序调用各个扩展/模块的RSHUTDOWN方法
14. php-fpm将结果转发给nginx/apache
15. nginx/apache将结果返回给客户端

**传统框架性能差主要有5个原因**
1. php-fpm请求开始初始化一切，请求结束销毁一切的开销
2. php-fpm每次请求从磁盘读取多个php文件，反复词法语法解析、反复编译成opcodes开销(可用opcache避免)
3. 框架反复创建框架相关类实例及初始化的开销
4. 框架反复连接断开数据库、redis等开销
5. nginx/apache自身开销以及与php-fpm通讯开销


###webman的请求处理流程
1. 框架接收请求
2. 框架执行业务逻辑
3. 框架将结果返回给客户端

没错，在没有nginx反代的情况下，框架只有这3步。可以说这已经是php框架的极致，这使得webman性能是传统框架的几倍甚至数十倍。

性能对比参考 [techempower.com 第20轮压测(带数据库业务)](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf&a=2)

