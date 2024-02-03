# webman Performance

### Traditional Framework Request Handling Process

1. Nginx/Apache receives the request
2. Nginx/Apache passes the request to php-fpm
3. Php-fpm initializes the environment, such as creating variable lists
4. Php-fpm calls RINIT of various extensions/modules
5. Php-fpm reads PHP files from disk (can be avoided using opcache)
6. Php-fpm performs lexical analysis, syntax analysis, and compiles into opcodes (can be avoided using opcache)
7. Php-fpm executes opcodes including steps 8, 9, 10, and 11
8. The framework initializes, such as instantiating various classes, including containers, controllers, routes, middleware, etc.
9. The framework connects to the database and performs permission verification, connects to Redis
10. The framework executes business logic
11. The framework closes the database and Redis connections
12. Php-fpm releases resources, destroys all class definitions and instances, destroys symbol tables, etc.
13. Php-fpm sequentially calls the RSHUTDOWN methods of various extensions/modules
14. Php-fpm forwards the result to Nginx/Apache
15. Nginx/Apache returns the result to the client

### webman Request Handling Process
1. The framework receives the request
2. The framework executes the business logic
3. The framework returns the result to the client

That's right, in the absence of Nginx reverse proxying, the framework only has these 3 steps. It can be said that this is the ultimate for a PHP framework, making webman's performance several times or even tens of times higher than that of traditional frameworks.

For more information, see [benchmark tests](benchmarks.md)