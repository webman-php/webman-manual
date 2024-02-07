# webman es

webman es un marco de servicio HTTP de alto rendimiento desarrollado sobre [workerman](https://www.workerman.net). Se utiliza para reemplazar la arquitectura tradicional de php-fpm y proporcionar un servicio HTTP altamente escalable de alto rendimiento. Con webman, puedes desarrollar sitios web, interfaces HTTP y microservicios.

Además, webman soporta procesos personalizados que pueden realizar cualquier tarea que workerman pueda hacer, como servicios de websocket, IoT, juegos, servicios TCP, servicios UDP, servicios de socket unix, entre otros.

# Filosofia de webman
**Proporcionando la máxima extensibilidad y el mejor rendimiento con el núcleo mínimo.**

webman solo ofrece las funciones más esenciales (enrutamiento, middleware, sesión, interfaz de proceso personalizado). Todas las demás funciones se reutilizan del ecosistema de composer, lo que significa que puedes utilizar los componentes más familiares en webman, como en el desarrollo de bases de datos, los desarrolladores pueden elegir usar `illuminate/database` de Laravel, `ThinkORM` de ThinkPHP, u otros componentes como `Medoo`. Integrarlos en webman es muy fácil.

# Características de webman

1. Alta estabilidad. webman está basado en workerman, un framework de socket extremadamente estable con muy pocos errores en la industria.

2. Rendimiento ultra alto. El rendimiento de webman es de 10 a 100 veces mayor que el de los frameworks tradicionales de php-fpm, y alrededor de dos veces mayor que el de frameworks como gin y echo de go.

3. Alta reutilización. No es necesario modificar, se pueden reutilizar la gran mayoría de los componentes y bibliotecas de composer.

4. Alta extensibilidad. Soporta procesos personalizados que pueden realizar cualquier tarea que workerman puede realizar.

5. Muy sencillo y fácil de usar, con un costo de aprendizaje extremadamente bajo, la escritura de código no difiere mucho de los frameworks tradicionales.

6. Utiliza la licencia de código abierto MIT, muy flexible y amigable.

# Dirección del proyecto
GitHub: https://github.com/walkor/webman **¡No seas tacaño y da una estrella!**

Gitee: https://gitee.com/walkor/webman **¡No seas tacaño y da una estrella!**

# Datos de pruebas de terceros y autorizados


![](../assets/img/benchmark1.png)

Con consultas a la base de datos, la capacidad de procesamiento único de webman alcanza las 390,000 QPS, que es casi 80 veces más alta que la arquitectura tradicional de php-fpm con el framework Laravel.


![](../assets/img/benchmarks-go.png)

Con consultas a la base de datos, webman tiene un rendimiento aproximadamente el doble que los marcos web similares de go.

Estos datos provienen de [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
