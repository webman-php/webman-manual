# Flujo de ejecución

## Flujo de inicio del proceso

Después de ejecutar php start.php start, el flujo de ejecución es el siguiente:

1. Cargar la configuración en config/
2. Configurar el Worker con opciones como `pid_file`, `stdout_file`, `log_file`, `max_package_size`, etc.
3. Crear el proceso webman y escuchar en el puerto (por defecto 8787)
4. Crear procesos personalizados según la configuración
5. Una vez que el proceso webman y los procesos personalizados se inician, se ejecuta la lógica siguiente (todo se ejecuta en onWorkerStart):
   1. Cargar los archivos configurados en `config/autoload.php`, como `app/functions.php`
   2. Cargar los middleware configurados en `config/middleware.php` (incluyendo los definidos en `config/plugin/*/*/middleware.php`)
   3. Ejecutar las clases definidas en `config/bootstrap.php` (incluyendo las definidas en `config/plugin/*/*/bootstrap.php`) para inicializar algunos módulos, como la inicialización de la base de datos de Laravel
   4. Cargar las rutas definidas en `config/route.php` (incluyendo las definidas en `config/plugin/*/*/route.php`)

## Flujo de manejo de solicitudes

1. Verificar si la URL de la solicitud corresponde a un archivo estático en la carpeta public. Si es así, devolver el archivo (fin de la solicitud). Si no, ir al paso 2.
2. Determinar si la URL coincide con alguna ruta. Si no, pasar al paso 3. Si sí, pasar al paso 4.
3. Verificar si se ha deshabilitado la ruta predeterminada. Si es así, devolver un error 404 (fin de la solicitud). Si no, pasar al paso 4.
4. Encontrar los middleware correspondientes al controlador de la solicitud, ejecutar las operaciones previas a los middleware en orden (fase de solicitud del modelo cebolla), ejecutar la lógica del controlador, ejecutar las operaciones posteriores a los middleware (fase de respuesta del modelo cebolla) y finalizar la solicitud. (Consultar el [modelo cebolla de middleware](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
