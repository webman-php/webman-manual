# Rendimiento de webman

### Proceso de manejo de solicitudes en el marco tradicional

1. Nginx/Apache recibe la solicitud.
2. Nginx/Apache pasa la solicitud a php-fpm.
3. PHP-FPM inicializa el entorno, como la creación de una lista de variables.
4. PHP-FPM llama a RINIT de varias extensiones/módulos.
5. PHP-FPM lee el archivo PHP del disco (se puede evitar usando opcache).
6. PHP-FPM realiza el análisis léxico, análisis sintáctico y compila en opcode (se puede evitar usando opcache).
7. PHP-FPM ejecuta opcode, incluyendo 8, 9, 10 y 11.
8. El marco se inicializa, como instanciar varias clases, incluyendo el contenedor, controladores, enrutadores, middleware, etc.
9. El marco se conecta a la base de datos y realiza verificación de permisos, conexión con Redis.
10. El marco ejecuta la lógica del negocio.
11. El marco cierra la conexión a la base de datos y a Redis.
12. PHP-FPM libera recursos, destruye todas las definiciones de clases, instancias, y destruye la tabla de símbolos, etc.
13. PHP-FPM llama secuencialmente el método RSHUTDOWN de varias extensiones/módulos.
14. PHP-FPM reenvía el resultado a Nginx/Apache.
15. Nginx/Apache devuelve el resultado al cliente.

### Proceso de manejo de solicitudes en webman
1. El marco recibe la solicitud.
2. El marco ejecuta la lógica del negocio.
3. El marco devuelve el resultado al cliente.

Eso es correcto, en el caso de que no haya un servidor Nginx de reenvío, el marco solo consta de estos 3 pasos. Se puede decir que esto ya es el pináculo de un marco de PHP, lo que hace que el rendimiento de webman sea varias veces o incluso decenas de veces mayor que el de un marco tradicional.

Para más información, consulte [Pruebas de rendimiento](benchmarks.md).
