# Requisitos del entorno

## Sistema operativo Linux
El sistema operativo Linux requiere las extensiones `posix` y `pcntl`, las cuales son extensiones integradas de PHP y generalmente no necesitan ser instaladas para su uso.

Si usted es un usuario de Baota, simplemente desactive o elimine las funciones que comiencen con `pnctl_` en Baota.

La extensión `event` no es obligatoria, pero se recomienda su instalación para obtener un mejor rendimiento.

## Sistema operativo Windows
Si bien webman puede ejecutarse en sistemas Windows, se recomienda utilizar Windows únicamente como entorno de desarrollo y no para entornos de producción, debido a la imposibilidad de configurar procesos múltiples, procesos en segundo plano, entre otros motivos.

Nota: En sistemas Windows no es necesario tener las extensiones `posix` y `pcntl`.
