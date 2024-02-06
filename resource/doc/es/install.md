# Requerimientos del entorno

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Crear un proyecto

```php
composer create-project workerman/webman
```

### 2. Ejecución

Ingrese al directorio de webman   

#### Usuarios de Windows
Haga doble clic en `windows.bat` o ejecute `php windows.php` para iniciar

> **Nota**
> Si hay algún error, es posible que alguna función esté deshabilitada, consulte [Comprobación de funciones deshabilitadas](others/disable-function-check.md) para habilitarlas.

#### Usuarios de Linux
Ejecución en modo `debug` (para desarrollo y depuración)

```php
php start.php start
```

Ejecución en modo `daemon` (para entorno de producción)

```php
php start.php start -d
```

> **Nota**
> Si hay algún error, es posible que alguna función esté deshabilitada, consulte [Comprobación de funciones deshabilitadas](others/disable-function-check.md) para habilitarlas.

### 3. Acceso

Acceda desde un navegador a `http://direcciónip:8787`
