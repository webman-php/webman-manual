# Archivo de configuración

La configuración de los complementos es similar a la de un proyecto webman normal, pero generalmente la configuración de los complementos solo afecta al complemento actual y no tiene impacto en el proyecto principal.
Por ejemplo, el valor de `plugin.foo.app.controller_suffix` solo afecta al sufijo del controlador del complemento y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.foo.app.controller_reuse` solo afecta a si el complemento reutiliza controladores, y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.foo.middleware` solo afecta a los middleware del complemento y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.foo.view` solo afecta a la vista utilizada por el complemento y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.foo.container` solo afecta al contenedor utilizado por el complemento y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.foo.exception` solo afecta a la clase de manejo de excepciones del complemento y no afecta al proyecto principal.

Sin embargo, dado que las rutas son globales, la configuración de las rutas del complemento también afecta globalmente.

## Obtener configuración
La forma de obtener la configuración de un complemento en particular es `config('plugin.{complemento}.{configuración específica}');`, por ejemplo, obtener todos los métodos de configuración de `plugin/foo/config/app.php` sería `config('plugin.foo.app')`.
Del mismo modo, el proyecto principal u otros complementos también pueden usar `config('plugin.foo.xxx')` para obtener la configuración del complemento "foo".

## Configuraciones no soportadas
La aplicación de complementos no admite las configuraciones `server.php` y `session.php`, tampoco admite las configuraciones `app.request_class`, `app.public_path` y `app.runtime_path`.
