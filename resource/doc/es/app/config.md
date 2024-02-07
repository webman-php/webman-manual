# Archivo de configuración

La configuración de los complementos es similar a la de un proyecto webman común, pero generalmente solo afecta al complemento actual y no al proyecto principal. Por ejemplo, el valor de `plugin.foo.app.controller_suffix` solo afecta al sufijo del controlador del complemento y no tiene impacto en el proyecto principal. Del mismo modo, el valor de `plugin.foo.app.controller_reuse` solo afecta a si el complemento reutiliza el controlador y no tiene impacto en el proyecto principal. El valor de `plugin.foo.middleware` solo afecta a los middleware del complemento y no tiene impacto en el proyecto principal. El valor de `plugin.foo.view` solo afecta a la vista utilizada por el complemento y no tiene impacto en el proyecto principal. El valor de `plugin.foo.container` solo afecta al contenedor utilizado por el complemento y no tiene impacto en el proyecto principal. El valor de `plugin.foo.exception` solo afecta a la clase de manejo de excepciones del complemento y no tiene impacto en el proyecto principal.

Sin embargo, debido a que las rutas son globales, la configuración de las rutas del complemento también afecta globalmente.

## Obtener configuración
Para obtener la configuración de un complemento específico, se utiliza el método `config('plugin.{complemento}.{configuración específica}')`, por ejemplo, para obtener todos los valores de configuración de `plugin/foo/config/app.php`, se usa `config('plugin.foo.app')`. Del mismo modo, el proyecto principal u otros complementos también pueden utilizar `config('plugin.foo.xxx')` para obtener la configuración del complemento `foo`.

## Configuración no compatible
Los complementos de aplicación no admiten la configuración server.php, session.php, ni admiten la configuración `app.request_class`, `app.public_path`, `app.runtime_path`.
