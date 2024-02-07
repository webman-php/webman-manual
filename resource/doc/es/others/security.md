# Seguridad

## Usuario en ejecución
Se recomienda configurar el usuario en ejecución como un usuario con permisos más bajos, por ejemplo, el mismo usuario que ejecuta nginx. El usuario en ejecución se configura en `user` y `group` dentro de `config/server.php`. Del mismo modo, para procesos personalizados, el usuario se especifica a través de `user` y `group` en `config/process.php`. Es importante tener en cuenta que el proceso de monitoreo no debe tener un usuario en ejecución configurado, ya que requiere permisos elevados para funcionar correctamente.

## Normas de controladores
La carpeta `controller` o sus subcarpetas solo deben contener archivos de controladores, no se permite colocar otros tipos de archivos. De lo contrario, si no se ha habilitado la [extensión del controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), es posible que los archivos de clase sean accedidos ilegalmente a través de la URL, lo que podría ocasionar consecuencias impredecibles. Por ejemplo, `app/controller/model/User.php` es en realidad una clase de modelo, pero se colocó incorrectamente en la carpeta `controller`. Si no se ha habilitado la [extensión del controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), los usuarios podrían acceder a cualquier método de `User.php` a través de una URL como `/model/user/xxx`. Para evitar completamente esta situación, se recomienda encarecidamente utilizar la [extensión del controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) para marcar claramente qué archivos son controladores.

## Filtrado XSS
Por consideraciones de universalidad, webman no realiza el escape de XSS en las solicitudes. webman recomienda encarecidamente realizar el escape de XSS al momento de la representación, en lugar de hacerlo antes de ingresar a la base de datos. Además, plantillas como twig, blade, think-template, entre otras, realizan automáticamente el escape de XSS, por lo que no es necesario hacerlo manualmente, lo cual es muy conveniente.

> **Nota**
> Si realizas el escape de XSS antes de ingresar a la base de datos, es probable que ocasiones problemas de incompatibilidad con ciertos complementos de aplicaciones.

## Prevención de la inyección de SQL
Para prevenir la inyección de SQL, es recomendable utilizar ORM tanto como sea posible, como [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) o [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), evitando en lo posible armar manualmente consultas SQL.

## Proxy de nginx
Cuando tu aplicación necesita estar expuesta a usuarios externos, se recomienda encarecidamente agregar un proxy de nginx antes de webman. De esta forma, se pueden filtrar algunas solicitudes HTTP no autorizadas, mejorando la seguridad. Para más detalles, consulta [Proxy de nginx](nginx-proxy.md).
