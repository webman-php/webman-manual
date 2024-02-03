# Directory Structure
```
.
├── app                           Application directory
│   ├── controller                Controller directory
│   ├── model                     Model directory
│   ├── view                      View directory
│   ├── middleware                Middleware directory
│   │   └── StaticFile.php        Built-in static file middleware
|   └── functions.php             Business custom functions are placed in this file
|
├── config                        Configuration directory
│   ├── app.php                   Application configuration
│   ├── autoload.php              Files configured here will be automatically loaded
│   ├── bootstrap.php             Configuration for the onWorkerStart callback when the process starts
│   ├── container.php             Container configuration
│   ├── dependence.php            Container dependency configuration
│   ├── database.php              Database configuration
│   ├── exception.php             Exception configuration
│   ├── log.php                   Log configuration
│   ├── middleware.php            Middleware configuration
│   ├── process.php               Custom process configuration
│   ├── redis.php                 Redis configuration
│   ├── route.php                 Routing configuration
│   ├── server.php                Server configuration for ports, processes, etc.
│   ├── view.php                  View configuration
│   ├── static.php                Static file switch and static file middleware configuration
│   ├── translation.php           Multilingual configuration
│   └── session.php               Session configuration
├── public                        Static resource directory
├── process                       Custom process directory
├── runtime                       Application runtime directory, requiring write permissions
├── start.php                     Server startup file
├── vendor                        Directory for third-party libraries installed by Composer
└── support                       Library adaptation (including third-party libraries)
    ├── Request.php               Request class
    ├── Response.php              Response class
    ├── Plugin.php                Plugin installation and uninstallation scripts
    ├── helpers.php               Helper functions (business custom functions should be placed in app/functions.php)
    └── bootstrap.php             Initialization script after process startup
```