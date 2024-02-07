# Archivo de configuración

## Ubicación
El archivo de configuración de webman se encuentra en el directorio `config/`, en el proyecto se puede obtener la configuración correspondiente mediante la función `config()`.

## Obtener configuración

Obtener toda la configuración
```php
config();
```

Obtener toda la configuración en `config/app.php`
```php
config('app');
```

Obtener la configuración de `debug` en `config/app.php`
```php
config('app.debug');
```

Si la configuración es un array, se puede obtener el valor de los elementos internos del array usando `.`, por ejemplo
```php
config('archivo.clave1.clave2');
```

## Valor por defecto
```php
config($key, $default);
```
config utiliza el segundo parámetro para pasar un valor por defecto, si la configuración no existe, devolverá el valor por defecto.
Si la configuración no existe y no se ha establecido un valor por defecto, devolverá null.

## Configuración personalizada
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
config('payment.secret');
```

## Modificar la configuración
webman no soporta la modificación dinámica de la configuración, toda la configuración debe modificarse manualmente en el archivo de configuración correspondiente, y debe realizarse un reinicio reload o restart.

> **Nota**
> La configuración del servidor `config/server.php` y la configuración de los procesos `config/process.php` no admiten reload, y se requiere un restart para surtir efecto.
