# Notas de programación

## Sistema operativo
webman es compatible con sistemas Linux y Windows. Sin embargo, debido a que Workerman no es compatible con la configuración de múltiples procesos y demonios en Windows, se recomienda utilizar Windows solo para el desarrollo y pruebas, y para entornos de producción se recomienda utilizar Linux.

## Métodos de inicio
En **sistemas Linux**, se inicia con el comando `php start.php start` (modo de depuración) o `php start.php start -d` (modo demonio).
En **sistemas Windows**, se ejecuta `windows.bat` o se utiliza el comando `php windows.php` para iniciar, y se detiene con ctrl+c. Los sistemas Windows no admiten comandos como stop, reload, status, reload connections, entre otros.

## Memoria persistente
webman es un marco de trabajo con memoria persistente. Por lo general, después de que un archivo PHP se carga en la memoria, se reutiliza y no se vuelve a leer del disco (con excepción de los archivos de plantillas). Por lo tanto, para que los cambios en el código o la configuración surtan efecto en entornos de producción, se necesita ejecutar `php start.php reload`. Si se realizan cambios en la configuración de procesos o se instala un nuevo paquete de Composer, se necesita reiniciar con `php start.php restart`.

> Para facilitar el desarrollo, webman incluye un proceso de monitorización personalizado para detectar actualizaciones en los archivos de código. Cuando se actualiza un archivo de código, se ejecuta automáticamente una recarga. Esta función solo está activa cuando Workerman se ejecuta en modo de depuración (sin el argumento `-d`). Los usuarios de Windows necesitan ejecutar `windows.bat` o `php windows.php` para activar esta función.

## Sobre las instrucciones de salida
En los proyectos tradicionales de PHP-FPM, las funciones `echo`, `var_dump`, entre otras, muestran datos directamente en la página. Sin embargo, en webman, estas salidas suelen aparecer en la terminal y no en la página (excepto las salidas en archivos de plantillas).

## No utilizar las instrucciones `exit` o `die`
Usar `die` o `exit` provocará la salida del proceso y su reinicio, lo que impedirá una respuesta correcta a la solicitud actual.

## No utilizar la función `pcntl_fork`
La función `pcntl_fork` se utiliza para crear un proceso, lo cual no está permitido en webman.
