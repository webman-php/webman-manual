# Seguridad

## Usuario en ejecución
Se recomienda configurar el usuario en ejecución como un usuario con permisos más bajos, como el usuario que ejecuta nginx. El usuario en ejecución se configura en `user` y `group` en el archivo `config/server.php`. El usuario personalizado para los procesos se especifica a través de `user` y `group` en el archivo `config/process.php`. Es importante tener en cuenta que el proceso de monitoreo no debe tener un usuario en ejecución configurado, ya que requiere permisos elevados para funcionar correctamente.

## Especificaciones del controlador
En el directorio `controller` o sus subdirectorios, solo se pueden colocar archivos de controladores, se prohíbe colocar otros archivos de clase. De lo contrario, si no se ha habilitado la [extensión del controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), es posible que los archivos de clase sean accedidos ilegalmente a través de URL, lo que puede causar consecuencias impredecibles. Por ejemplo, `app/controller/model/User.php` es en realidad una clase de modelo, pero se colocó incorrectamente en el directorio `controller`. Sin la [extensión del controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) habilitada, esto permitiría a los usuarios acceder a cualquier método en `User.php` a través de URL como `/model/user/xxx`. Para evitar completamente esta situación, se recomienda encarecidamente utilizar la [extensión del controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) para marcar claramente qué archivos son controladores.

## Filtrado de XSS
Por consideraciones de generalidad, webman no realiza escape de XSS en las solicitudes. Se recomienda encarecidamente realizar el escape de XSS al renderizar, en lugar de antes de almacenarlo en la base de datos. Además, plantillas como twig, blade, think-template, etc., realizan automáticamente el escape de XSS, por lo que no es necesario hacerlo manualmente, lo que es muy conveniente.

> **Consejo**
> Si realiza el escape de XSS antes de almacenarlo en la base de datos, es probable que surjan problemas de incompatibilidad con algunos complementos de la aplicación.

## Prevención de inyección SQL
Para evitar la inyección SQL, se recomienda utilizar en la medida de lo posible un ORM, como [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) o [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html). Evite ensamblarSQL manualmente siempre que sea posible.

## Proxy de nginx
Cuando su aplicación necesita ser expuesta a usuarios externos a través de la red, se recomienda encarecidamente agregar un proxy de nginx antes de webman. Esto puede filtrar algunas solicitudes HTTP no válidas para aumentar la seguridad. Para más información, consulte [nginx代理](nginx-proxy.md).
