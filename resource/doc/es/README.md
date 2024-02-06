# Qué es webman

webman es un marco de servicio HTTP de alto rendimiento desarrollado sobre [workerman](https://www.workerman.net). Se utiliza para reemplazar la arquitectura tradicional php-fpm y proporciona un servicio HTTP altamente escalable y de alto rendimiento. Con webman, puedes desarrollar sitios web, interfaces HTTP o microservicios.

Además, webman también admite procesos personalizados que pueden realizar cualquier tarea que workerman pueda hacer, como servicios de WebSocket, IoT, juegos, servicios TCP, servicios UDP, servicios de socket UNIX, entre otros.

# Filosofía de webman
**Proporcionar la máxima escalabilidad y rendimiento con el núcleo más mínimo.**

webman solo proporciona las funciones más fundamentales (enrutamiento, middleware, sesión, interfaz de proceso personalizado). Todas las demás funciones se reutilizan del ecosistema de composer, lo que significa que puedes usar los componentes más familiares en webman, como el módulo `illuminate/database` de Laravel para desarrollo de bases de datos, `ThinkORM` de ThinkPHP, u otros componentes como `Medoo`. Integrarlos en webman es muy fácil.

# Características de webman

1. Alta estabilidad: Basado en workerman, conocido por su alta estabilidad con muy pocos errores en la industria.
2. Rendimiento superior: El rendimiento de webman es de 10 a 100 veces más alto que los marcos tradicionales de php-fpm, y aproximadamente el doble de los marcos como gin y echo en go.
3. Alta reutilización: Puede reutilizar la mayoría de los componentes y bibliotecas de composer sin necesidad de modificaciones.
4. Alta escalabilidad: Admite procesos personalizados que pueden realizar cualquier tarea que workerman pueda hacer.
5. Fácil de usar: Costo de aprendizaje extremadamente bajo y la escritura de código es igual que en los marcos tradicionales.
6. Utiliza la licencia de código abierto MIT, amigable y permisiva.

# Enlaces del proyecto
GitHub: https://github.com/walkor/webman **No seas tacaño con tus estrellitas**

Gitee: https://gitee.com/walkor/webman **No seas tacaño con tus estrellitas**

# Datos de prueba de terceros confiables

![](../assets/img/benchmark1.png)

Con consultas a la base de datos, webman logra un rendimiento de 390,000 QPS en un solo servidor, aproximadamente 80 veces más que el marco laravel de la arquitectura tradicional php-fpm.

![](../assets/img/benchmarks-go.png)

Con consultas a la base de datos, webman tiene un rendimiento aproximadamente el doble que un marco web similar en el lenguaje go.

Estos datos provienen de [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
