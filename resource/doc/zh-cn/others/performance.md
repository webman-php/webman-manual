## webman性能

##传统框架请求处理流程

0. nginx/apache将请求传递给php-fpm
1. php-fpm初始化环境，如创建变量列表
2. php-fpm调用各个扩展/模块的RINIT
3. php-fpm磁盘读取php文件
4. php-fpm词法分析、语法分析、编译成opcde
5. php-fpm执行opcode 包括
6. 框架例化各种类，包括框架相关的类、如容器、控制器、路由、中间键等。
7. 框架连接数据库、redis
8. 框架执行业务逻辑
9. 框架关闭数据库、redis
10. php-fpm释放资源、销毁所有类定义、实例、销毁符号表等
11. php-fpm顺序调用各个扩展/模块的RSHUTDOWN方法
12. php-fpm将请求转发给nginx/apache

**传统框架性能差主要有5个原因**
1. php-fpm请求开始初始化一切，请求结束销毁一切的开销
2. php-fpm每次请求从磁盘读取多个php文件，反复词法语法解析、反复编译成opcodes开销(可用opcache避免)
3. 框架反复创建框架相关类实例及初始化的开销
4. 框架反复连接断开数据库、redis等开销
5. nginx/apache自身开销以及与php-fpm通讯开销


##webman的请求处理流程
8. 框架执行业务逻辑

没错，在没有nginx反代的情况下，webman只有 `8.框架执行业务逻辑`。可以说这已经是php框架的极致，这使得webman性能是传统框架的几倍甚至数十倍。

性能对比参考 [techempower.com 第20轮压测(带数据库业务)](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf&a=2)

