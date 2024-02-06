# Consideraciones de programación

## Sistema operativo
webman es compatible con sistemas Linux y Windows. Sin embargo, debido a que Workerman no puede admitir la configuración de múltiples procesos ni el proceso demonio en Windows, se recomienda utilizar Windows solo para el desarrollo y la depuración en entornos de desarrollo. Para entornos de producción, se recomienda utilizar sistemas Linux.

## Método de inicio
En **sistemas Linux**, se inicia con el comando `php start.php start` (modo de depuración) o `php start.php start -d` (modo de proceso demonio).
En **sistemas Windows**, se puede ejecutar `windows.bat` o usar el comando `php windows.php` para iniciar, y se detiene con Ctrl + C. Windows no admite comandos como stop, reload, status y conexiones.

## Memoria persistente
webman es un marco de trabajo de memoria persistente. Por lo general, una vez que un archivo PHP se carga en la memoria, se reutilizará y no se volverá a leer del disco (a excepción de los archivos de plantilla). Por lo tanto, para que los cambios en el código de producción o la configuración tengan efecto, es necesario ejecutar `php start.php reload`. Si se realizan cambios en la configuración del proceso o se instalan nuevos paquetes de Composer, se necesita reiniciar con `php start.php restart`.

> Para facilitar el desarrollo, webman incluye un monitor de procesos personalizado que supervisa las actualizaciones de archivos de negocio y ejecuta automáticamente la recarga cuando se producen actualizaciones de archivos de negocio. Esta función solo se activa cuando Workerman se ejecuta en modo de depuración (sin `-d` al iniciar). Los usuarios de Windows necesitan ejecutar `windows.bat` o `php windows.php` para activar esta función.

## Sobre las declaraciones de salida
En proyectos tradicionales de PHP-FPM, las salidas de datos utilizando funciones como `echo` o `var_dump` se muestran directamente en la página web. Sin embargo, en webman, estas salidas suelen mostrarse en la terminal y no en la página web, a excepción de las salidas en archivos de plantillas.

## No ejecutar declaraciones `exit` o `die`
Ejecutar `die` o `exit` provocará la terminación del proceso y su reinicio, lo que hará que la solicitud actual no se responda correctamente.

## No ejecutar la función `pcntl_fork`
La función `pcntl_fork` se utiliza para crear un nuevo proceso, lo cual no está permitido en webman.
