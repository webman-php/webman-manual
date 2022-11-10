# webmanPerformance


###Legacy Framework Request Processing Flow

1. nginx/apacheReceive Requests
2. nginx/apachePass the request tophp-fpm
3. php-fpmInitializing the environment, such as creating a list of variables
4. php-fpmCall individual extensions/modulesRINIT
5. php-fpmdisk read php files
6. php-fpmLexical analysis, syntax analysis, compile toopcode
7. php-fpmExecute opcode include8.9.10.11
8. Framework initialization, such as instantiating various classes, including such as containers, controllers, routes, intermediate keys, etc.。
9. Framework connection database、redis
10. Framework to execute business logic
11. framework close database、redis
12. php-fpmRelease resources, destroy all class definitions, instances, destroy symbol table, etc.
13. php-fpmCall the RSHUTDOWN method of each extension/module in sequence
14. php-fpmForward the result tonginx/apache
15. nginx/apacheReturn the result to the client

**There are 5 main reasons for the poor performance of traditional frameworks**
1. php-fpmOverhead to initialize everything at the beginning of the request and destroy everything at the end of the request
2. php-fpmRead multiple php files from disk per request, repeated lexical syntax parsing, repeated compilation into opcodes overhead (can be avoided with opcache))
3. The framework repeatedly creates instances of framework-related classes and the initialization overhead
4. The framework repeatedly connects to disconnected databases, redis, and other overheads
5. nginx/apacheOwn overhead and communication overhead with php-fpm


###webmanthe request processing flow
1. The framework receives requests
2. Framework to execute business logic
3. The framework returns the results to the client

Right，In the absence ofnginxProbably because you，Before the start3步。if you want to get from otherphpThe framework is only this，This makeswebmanPerformanceseveral times or even tens of times better than traditional frameworks。

Performancein the properties of [techempower.com 第20wheel pressure measurement(The default used is)](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf&a=2)

