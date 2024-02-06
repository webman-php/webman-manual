# Proceso de ejecución

## Proceso de inicio de proceso

Después de ejecutar php start.php start, el flujo de ejecución es el siguiente:

1. Cargar la configuración en config/
2. Configurar las opciones relacionadas con Worker, como `pid_file`, `stdout_file`, `log_file`, `max_package_size`, entre otras.
3. Crear el proceso de webman y escuchar en el puerto (por defecto 8787).
4. Crear procesos personalizados según la configuración.
5. Después de que el proceso de webman y los procesos personalizados se inicien, se ejecutan las siguientes lógicas (todas se ejecutan en onWorkerStart):
   ① Cargar los archivos configurados en `config/autoload.php`, como `app/functions.php`
   ② Cargar los middleware configurados en `config/middleware.php` (incluyendo `config/plugin/*/*/middleware.php`).
   ③ Ejecutar la lógica definida en `config/bootstrap.php` (incluyendo `config/plugin/*/*/bootstrap.php`) para inicializar algunos módulos, como la inicialización de la conexión a la base de datos de Laravel.
   ④ Cargar las rutas definidas en `config/route.php` (incluyendo `config/plugin/*/*/route.php`).

## Proceso de manejo de solicitudes

1. Verificar si la URL de la solicitud corresponde a un archivo estático en la carpeta public. En caso afirmativo, devolver el archivo (terminar la solicitud). En caso negativo, continuar al paso 2.
2. Determinar si la URL coincide con alguna ruta. Si no coincide, ir al paso 3; de lo contrario, ir al paso 4.
3. ¿Se ha desactivado la ruta predeterminada? En caso afirmativo, devolver un error 404 (terminar la solicitud). En caso negativo, continuar al paso 4.
4. Encontrar los middlewares correspondientes al controlador solicitado, ejecutar las operaciones previas al middleware en orden (fase de solicitud del modelo de cebolla), ejecutar la lógica del controlador, ejecutar las operaciones posteriores al middleware (fase de respuesta del modelo de cebolla) y finalizar la solicitud (consultar el modelo de [middleware de cebolla](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B)).

- Webman es un marco de alto rendimiento basado en Workerman.
