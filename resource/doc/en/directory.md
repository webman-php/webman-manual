# Directory structure
```
.
├── app                           Application directory
│   ├── controller                Controller directory
│   ├── model                     Model directory
│   ├── view                      View directory
│   ├── middleware                Middleware directory
│   │   └── StaticFile.php        Built-in static file middleware
|   └── functions.php             Business custom functions are written here
|
├── config                        Configuration directory
│   ├── app.php                   Application configuration
│   ├── autoload.php              Files configured here will be automatically loaded
│   ├── bootstrap.php             Callback configuration that runs the onWorkerStart when the process is started
│   ├── container.php             Container configuration
│   ├── dependence.php            Container dependency configuration
│   ├── database.php              Database configuration
│   ├── exception.php             Exception configuration
│   ├── log.php                   Log configuration
│   ├── middleware.php            Middleware configuration
│   ├── process.php               Custom process configuration
│   ├── redis.php                 Redis configuration
│   ├── route.php                 Route configuration
│   ├── server.php                Server configuration including ports, number of processes, etc.
│   ├── view.php                  View configuration
│   ├── static.php                Static file switch and static file middleware configuration
│   ├── translation.php           Multilingual configuration
│   └── session.php               Session configuration
├── public                        Static resource directory
├── process                       Custom process directory
├── runtime                       Runtime directory of the application, which requires write permission
├── start.php                     Service start file
├── vendor                        Directory where third-party libraries installed by Composer are stored
└── support                       Library adaptation (including third-party libraries)
    ├── Request.php               Request class
    ├── Response.php              Response class
    ├── Plugin.php                Plugin installation and uninstallation script
    ├── helpers.php               Helper functions (business custom functions should be written in app/functions.php)
    └── bootstrap.php             Initialization script after the process is started
```
