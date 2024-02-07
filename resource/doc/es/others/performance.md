# Rendimiento de webman

### Flujo de procesamiento de solicitudes en marcos de trabajo tradicionales

1. Nginx/Apache recibe la solicitud.
2. Nginx/Apache transmite la solicitud a php-fpm.
3. Php-fpm inicializa el entorno, como la creación de una lista de variables.
4. Php-fpm llama a RINIT de varias extensiones/módulos.
5. Php-fpm lee el archivo PHP desde el disco (se puede evitar usando opcache).
6. Php-fpm realiza el análisis léxico, análisis sintáctico y compila en opcodes (se puede evitar usando opcache).
7. Php-fpm ejecuta opcodes, incluyendo 8, 9, 10, 11.
8. El marco de trabajo se inicializa, como instanciar diversas clases, incluyendo contenedores, controladores, rutas, middleware, etc.
9. El marco de trabajo se conecta a la base de datos y realiza la autenticación de permisos, conectándose a Redis.
10. El marco de trabajo ejecuta la lógica empresarial.
11. El marco de trabajo cierra la conexión a la base de datos y a Redis.
12. Php-fpm libera recursos, destruye todas las definiciones de clases, instancias y tablas de símbolos.
13. Php-fpm llama secuencialmente a los métodos de RSHUTDOWN de varias extensiones/módulos.
14. Php-fpm reenvía el resultado a Nginx/Apache.
15. Nginx/Apache devuelve el resultado al cliente.

### Flujo de procesamiento de solicitudes en webman

1. El marco de trabajo recibe la solicitud.
2. El marco de trabajo ejecuta la lógica empresarial.
3. El marco de trabajo devuelve el resultado al cliente.

Sí, sin un proxy inverso de Nginx, el marco de trabajo solo consta de estos 3 pasos. Se puede decir que esto ya es lo último en términos de marcos de trabajo PHP, lo que hace que el rendimiento de webman sea varias veces mayor que el de los marcos de trabajo tradicionales, e incluso varias veces más.  

Para más información, consulte [Pruebas de estrés](benchmarks.md).
