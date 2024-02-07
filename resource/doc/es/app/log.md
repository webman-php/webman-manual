# Registro
El uso de la clase de registro es similar al uso de la base de datos
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Si deseas reutilizar la configuración de registro del proyecto principal, simplemente utiliza
```php
use support\Log;
Log::info('Contenido del registro');
// Supongamos que el proyecto principal tiene una configuración de registro llamada "test"
Log::channel('test')->info('Contenido del registro');
```
