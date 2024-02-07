# Requisitos del entorno

## Sistema Linux
El sistema Linux depende de las extensiones `posix` y `pcntl`, las cuales son extensiones integradas en PHP y generalmente no requieren instalación adicional para su uso.

Para los usuarios de Baota, solo es necesario deshabilitar o eliminar las funciones que comienzan con `pnctl_` en Baota.

La extensión `event` no es obligatoria, pero se recomienda instalarla para mejorar el rendimiento.

## Sistema Windows
Webman puede ejecutarse en un sistema Windows, pero debido a la imposibilidad de configurar múltiples procesos, procesos en segundo plano, etc., se recomienda utilizar Windows únicamente como entorno de desarrollo y utilizar un sistema Linux para el entorno de producción debido a estas limitaciones.

Nota: En el sistema Windows, no hay dependencia de las extensiones `posix` y `pcntl`.
