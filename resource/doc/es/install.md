# Requisitos del entorno

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Crear proyecto

```php
composer create-project workerman/webman
```

### 2. Ejecutar

Ir al directorio de webman

#### Para usuarios de Windows
Doble clic en `windows.bat` o ejecutar `php windows.php` para iniciar

> **Consejo**
> Si hay un error, es posible que algunas funciones estén deshabilitadas. Consulte [Verificación de funciones deshabilitadas](others/disable-function-check.md) para habilitarlas

#### Para usuarios de Linux
Ejecutar en modo `debug` (para desarrollo y depuración)

```php
php start.php start
```

Ejecutar en modo `daemon` (para entorno de producción)

```php
php start.php start -d
```

> **Consejo**
> Si hay un error, es posible que algunas funciones estén deshabilitadas. Consulte [Verificación de funciones deshabilitadas](others/disable-function-check.md) para habilitarlas

### 3. Acceder

Acceder desde el navegador a `http://direcciónip:8787`
