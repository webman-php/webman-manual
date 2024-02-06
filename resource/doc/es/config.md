# Archivo de configuración

## Ubicación
El archivo de configuración de webman se encuentra en el directorio `config/`, en el proyecto se puede obtener la configuración correspondiente mediante la función `config()`.

## Obtener configuraciones

Obtener todas las configuraciones
```php
config();
```

Obtener todas las configuraciones en `config/app.php`
```php
config('app');
```

Obtener la configuración `debug` en `config/app.php`
```php
config('app.debug');
```

Si la configuración es un arreglo, se puede obtener el valor de los elementos internos del arreglo usando `.`, por ejemplo
```php
config('file.key1.key2');
```

## Valor predeterminado
```php
config($key, $default);
```
Se puede pasar el valor predeterminado como segundo parámetro en el método `config()`, si la configuración no existe, se devolverá el valor predeterminado. Si no se ha establecido un valor predeterminado y la configuración no existe, se devolverá `null`.

## Configuraciones personalizadas
Los desarrolladores pueden agregar sus propios archivos de configuración en el directorio `config/`, por ejemplo

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Uso al obtener la configuración**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Cambiar configuraciones
webman no admite la modificación dinámica de configuraciones, todas las configuraciones deben ser modificadas manualmente en los archivos de configuración correspondientes, y luego recargar o reiniciar el servidor.

> **Nota**
> Las configuraciones del servidor en `config/server.php` y las configuraciones de procesos en `config/process.php` no admiten recarga, es necesario reiniciar el servidor para que surtan efecto.
