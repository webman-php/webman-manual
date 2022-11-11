# directory structure
```
.
├── app                           Application Catalog
│   ├── controller                Controller Directory
│   ├── model                     Model Catalog
│   ├── view                      View Catalog
│   └── middleware                Middleware Directory
│   |   └── StaticFile.php        Self-contained static file middleware
|   |—— functions.php             Custom Functions
|
├── config                        Configure Catalog
│   ├── app.php                   Application configuration
│   ├── autoload.php              The files configured here will be loaded automatically
│   ├── bootstrap.php             Callback configuration that runs when the process starts onWorkerStart
│   ├── container.php             Container Configuration
│   ├── dependence.php            Container Dependency Configuration
│   ├── database.php              Database configuration
│   ├── exception.php             Exception Configuration
│   ├── log.php                   Logging Configuration
│   ├── middleware.php            Middleware Configuration
│   ├── process.php               Custom Process Configuration
│   ├── redis.php                 redisConfigure
│   ├── route.php                 Routing configuration
│   ├── server.php                Server configuration for ports, number of processes, etc.
│   ├── view.php                  View Configuration
│   ├── static.php                Static file switch and static file middleware configuration
│   ├── translation.php           Multi-language configuration
│   └── session.php               sessionConfigure
├── public                        Static resource directory
├── process                       Custom Process Directory
├── runtime                       Runtime directory of the application, requires writable permissions
├── start.php                     Service Startup Files
├── vendor                        composerDirectory of installed third-party libraries
└── support                       Class library adaptation (including third-party class libraries))
    ├── Request.php               Request Class
    ├── Response.php              Response class
    ├── Plugin.php                Plugin installation and uninstallation script
    ├── helpers.php               Helper functions
    └── bootstrap.php             Initialization script after process start
```
