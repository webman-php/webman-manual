# Estructura de directorios
```
.
├── app                           Directorio de la aplicación
│   ├── controller                Directorio de controladores
│   ├── model                     Directorio de modelos
│   ├── view                      Directorio de vistas
│   ├── middleware                Directorio de middlewares
│   │   └── StaticFile.php        Middleware de archivos estáticos incluido
|   └── functions.php             Funciones personalizadas del negocio se escriben en este archivo
|
├── config                        Directorio de configuración
│   ├── app.php                   Configuración de la aplicación
│   ├── autoload.php              Archivos configurados aquí se cargarán automáticamente
│   ├── bootstrap.php             Configuración de devolución de llamada que se ejecutará en onWorkerStart al iniciar el proceso
│   ├── container.php             Configuración de contenedor
│   ├── dependence.php            Configuración de dependencias del contenedor
│   ├── database.php              Configuración de la base de datos
│   ├── exception.php             Configuración de excepciones
│   ├── log.php                   Configuración de registro
│   ├── middleware.php            Configuración de middlewares
│   ├── process.php               Configuración de procesos personalizados
│   ├── redis.php                 Configuración de redis
│   ├── route.php                 Configuración de rutas
│   ├── server.php                Configuración del servidor, incluyendo puertos, número de procesos, etc.
│   ├── view.php                  Configuración de vistas
│   ├── static.php                Configuración de archivos estáticos y middleware de archivos estáticos
│   ├── translation.php           Configuración de múltiples idiomas
│   └── session.php               Configuración de sesión
├── public                        Directorio de recursos estáticos
├── process                       Directorio de procesos personalizados
├── runtime                       Directorio de tiempo de ejecución de la aplicación, requiere permisos de escritura
├── start.php                     Archivo de inicio del servicio
├── vendor                        Directorio de bibliotecas de terceros instaladas mediante composer
└── support                       Adaptación de bibliotecas (incluye bibliotecas de terceros)
    ├── Request.php               Clase de solicitud
    ├── Response.php              Clase de respuesta
    ├── Plugin.php                Script de instalación/desinstalación de plugins
    ├── helpers.php               Funciones auxiliares (las funciones personalizadas del negocio se escriben en app/functions.php)
    └── bootstrap.php             Script de inicialización después del arranque del proceso
```
