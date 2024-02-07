# Proceso de monitoreo
Webman viene con un proceso de monitorización integrado que admite dos funciones:
1. Monitoriza la actualización de archivos y recarga automáticamente el nuevo código de negocio (generalmente utilizado en desarrollo).
2. Monitoriza el uso de memoria de todos los procesos. Si el uso de memoria de un proceso está a punto de superar el límite establecido en `memory_limit` en `php.ini`, reinicia automáticamente de forma segura ese proceso (sin afectar el negocio).

### Configuración de monitoreo
El archivo de configuración `config/process.php` contiene la configuración `monitor`:
```php
global $argv;

return [
    // Detección de actualización de archivos y recarga automática
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitorear estos directorios
            'monitorDir' => array_merge([    // Qué directorios deben ser monitoreados
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Archivos con estas extensiones serán monitoreados
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Habilitar la monitorización de archivos
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Habilitar la monitorización de memoria
            ]
        ]
    ]
];
```
`monitorDir` se utiliza para configurar qué directorios deben ser monitoreados (no es recomendable monitorear muchos archivos en un directorio).
`monitorExtensions` se utiliza para especificar qué extensiones de archivo en el directorio `monitorDir` deben ser monitoreadas.
Cuando `options.enable_file_monitor` está establecido en `true`, se activa la monitorización de actualización de archivos (específicamente, en sistemas Linux, se activa de forma predeterminada al ejecutarse en modo de depuración).
Cuando `options.enable_memory_monitor` está establecido en `true`, se activa la monitorización del uso de memoria (no compatible con sistemas Windows).

> **Nota**
> En sistemas Windows, la monitorización de actualización de archivos solo se activa cuando se ejecuta `windows.bat` o `php windows.php`.
