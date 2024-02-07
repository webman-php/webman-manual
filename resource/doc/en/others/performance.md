# webman Performance

### Traditional Framework Request Processing Flow

1. Nginx/Apache receives the request.
2. Nginx/Apache passes the request to php-fpm.
3. Php-fpm initializes the environment, such as creating a variable list.
4. Php-fpm calls the RINIT of various extensions/modules.
5. Php-fpm reads the PHP file from the disk (which can be avoided using opcache).
6. Php-fpm performs lexical analysis, syntax analysis, and compiles into opcode (which can be avoided using opcache).
7. Php-fpm executes the opcode, including 8, 9, 10, 11.
8. The framework initializes, such as instantiating various classes, including containers, controllers, routes, middleware, etc.
9. The framework connects to the database, performs authorization, and connects to Redis.
10. The framework executes the business logic.
11. The framework closes the database and Redis connections.
12. Php-fpm releases resources, destroys all class definitions and instances, and destroys the symbol table, etc.
13. Php-fpm sequentially calls the RSHUTDOWN methods of various extensions/modules.
14. Php-fpm forwards the result to Nginx/Apache.
15. Nginx/Apache returns the result to the client.

### webman Request Processing Flow
1. The framework receives the request.
2. The framework executes the business logic.
3. The framework returns the result to the client.

That's right, in the case of no Nginx reverse proxy, the framework only has these 3 steps. It can be said that this is the ultimate for a PHP framework, which makes webman's performance several times or even tens of times better than traditional frameworks.

For more information, refer to [Stress Testing](benchmarks.md).
