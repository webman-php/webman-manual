# Estructura de directorios
```plaintext
.
├── app                           Directorio de la aplicación
│   ├── controller                Directorio de controladores
│   ├── model                     Directorio de modelos
│   ├── view                      Directorio de vistas
│   ├── middleware                Directorio de middlewares
│   │   └── StaticFile.php        Middleware de archivos estáticos incorporado
|   └── functions.php             Funciones personalizadas del negocio se escriben en este archivo
|
├── config                        Directorio de configuraciones
│   ├── app.php                   Configuración de la aplicación
│   ├── autoload.php              Archivos configurados para la carga automática
│   ├── bootstrap.php             Configuración de devolución de llamada ejecutada en onWorkerStart al inicio del proceso
│   ├── container.php             Configuración del contenedor
│   ├── dependence.php            Configuración de dependencias del contenedor
│   ├── database.php              Configuración de la base de datos
│   ├── exception.php             Configuración de excepciones
│   ├── log.php                   Configuración de registro
│   ├── middleware.php            Configuración de middlewares
│   ├── process.php               Configuración de procesos personalizados
│   ├── redis.php                 Configuración de redis
│   ├── route.php                 Configuración de rutas
│   ├── server.php                Configuración del servidor, puertos, número de procesos, etc.
│   ├── view.php                  Configuración de vistas
│   ├── static.php                Configuración de archivos estáticos y middleware de archivos estáticos
│   ├── translation.php           Configuración de idiomas múltiples
│   └── session.php               Configuración de sesión
├── public                        Directorio de recursos estáticos
├── process                       Directorio de procesos personalizados
├── runtime                       Directorio de tiempo de ejecución de la aplicación, requiere permisos de escritura
├── start.php                     Archivo de inicio del servicio
├── vendor                        Directorio de bibliotecas de terceros instaladas por composer
└── support                       Adaptación de bibliotecas (incluyendo bibliotecas de terceros)
    ├── Request.php               Clase de solicitud
    ├── Response.php              Clase de respuesta
    ├── Plugin.php                Script de instalación y desinstalación de plugins
    ├── helpers.php               Funciones de ayuda (las funciones personalizadas del negocio deben escribirse en app/functions.php)
    └── bootstrap.php             Script de inicialización después del inicio del proceso
```
