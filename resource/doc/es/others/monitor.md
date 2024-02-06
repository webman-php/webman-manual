# Monitoreo de procesos
webman viene con un proceso de monitorización incorporado que admite dos funciones:

1. Monitorear la actualización de archivos y cargar automáticamente el nuevo código de negocio (generalmente utilizado durante el desarrollo).
2. Monitorear el uso de memoria de todos los procesos. Si un proceso está a punto de alcanzar el límite `memory_limit` establecido en `php.ini`, se reiniciará automáticamente de forma segura (sin afectar al negocio).

### Configuración de monitoreo
En el archivo de configuración `config/process.php`, dentro de la configuración `monitor`, se configura de la siguiente manera:

```php
global $argv;

return [
    // Detección de actualización de archivos y recarga automática
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitorear estos directorios
            'monitorDir' => array_merge([   
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Se monitorean los archivos con estas extensiones
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];
```
- `monitorDir` se usa para configurar qué directorios se deben monitorear para actualizaciones (no se recomienda monitorear demasiados archivos en los directorios monitoreados).
- `monitorExtensions` se utiliza para configurar qué archivos con qué extensiones en los directorios de `monitorDir` deben ser monitoreados.
- Cuando el valor de `options.enable_file_monitor` es `true`, se activa la monitorización de actualizaciones de archivos (en el sistema Linux, se activa de forma predeterminada al ejecutar en modo de depuración).
- Cuando el valor de `options.enable_memory_monitor` es `true`, se activa la monitorización del uso de memoria (la monitorización del uso de memoria no es compatible con el sistema Windows).

> **Nota**
> En el sistema Windows, la monitorización de actualizaciones de archivos solo se activa al ejecutar `windows.bat` o `php windows.php`.
