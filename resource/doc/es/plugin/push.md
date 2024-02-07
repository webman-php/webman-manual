## webman/push

`webman/push` es un complemento gratuito del servidor de servicios de notificación. El cliente está basado en el modelo de suscripción, es compatible con [pusher](https://pusher.com) y es compatible con numerosos clientes como JS, Android (Java), iOS (Swift), iOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS, entre otros. El SDK de notificación del servidor es compatible con PHP, Node, Ruby, Asp, Java, Python, Go, Swift, entre otros. El cliente incluye una función de latido y reconexión automática en caso de desconexión, lo que lo hace muy simple y estable de usar. Es adecuado para escenarios de mensajería y chat en tiempo real, entre otros.

El complemento incluye un cliente JavaScript de página web llamado push.js y un cliente uniapp `uniapp-push.js`. Para obtener clientes en otros lenguajes, se pueden descargar desde https://pusher.com/docs/channels/channels_libraries/libraries/.

> Este complemento requiere webman-framework>=1.2.0

## Instalación

```sh
composer require webman/push
```

## Cliente (javascript)

**Incluir cliente JavaScript**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Uso del cliente (canal público)**
```js
// Establecer conexión
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // dirección del websocket
    app_key: '<app_key, obtenido en config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autorización de suscripción (solo para canales privados)
});
// Suponiendo que el uid del usuario es 1
var uid = 1;
// El navegador escucha los mensajes del canal user-1, es decir, los mensajes del usuario con uid 1
var user_channel = connection.subscribe('user-' + uid);

// Cuando hay un evento de mensaje en el canal user-1
user_channel.on('message', function(data) {
    // data contiene el contenido del mensaje
    console.log(data);
});
// Cuando hay un evento solicitud de amistad en el canal user-1
user_channel.on('friendApply', function (data) {
    // data contiene la información relacionada con la solicitud de amistad
    console.log(data);
});

// Suponiendo que el ID del grupo es 2
var group_id = 2;
// El navegador escucha los mensajes del canal group-2, es decir, los mensajes del grupo 2
var group_channel = connection.subscribe('group-' + group_id);
// Cuando hay un evento de mensaje en el canal group-2
group_channel.on('message', function(data) {
    // data contiene el contenido del mensaje
    console.log(data);
});
```

> **Consejo**
> En el ejemplo anterior, subscribe implementa la suscripción al canal, `message` y `friendApply` son eventos en el canal. Los canales y eventos son cadenas arbitrarias y no requieren configuración previa en el servidor.

## Notificación desde el servidor (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // En Webman se puede obtener la configuración directamente utilizando `config`. En entornos no Webman, es necesario escribir manualmente la configuración correspondiente.
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Enviar un mensaje del evento message a todos los clientes suscritos al usuario user-1
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Hola, este es el contenido del mensaje'
]);
```

## Canal privado
En los ejemplos anteriores, cualquier usuario puede suscribirse a través de Push.js. Si los mensajes contienen información sensible, esto no es seguro.

`webman/push` admite la suscripción a canales privados, que se identifican por comenzar con `private-`. Por ejemplo:
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // dirección del websocket
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // autorización de suscripción (solo para canales privados)
});

// Suponiendo que el uid del usuario es 1
var uid = 1;
// El navegador escucha los mensajes del canal privado-user-1
var user_channel = connection.subscribe('private-user-' + uid);
```

Cuando un cliente se suscribe a un canal privado (un canal que comienza con `private-`), el navegador hace una solicitud de autorización AJAX (la dirección del AJAX está configurada como la dirección auth al configurar Push) en la que el desarrollador puede verificar si el usuario actual tiene permiso para escuchar ese canal. De esta forma se garantiza la seguridad de la suscripción.

> Consultar la autorización en `config/plugin/webman/push/route.php`

## Notificación desde el cliente
Los ejemplos anteriores muestran cómo el cliente se suscribe a un canal y cómo el servidor llama a la API para enviar mensajes. `webman/push` también admite la notificación directa desde el cliente.

> **Nota**
> La notificación entre clientes solo es compatible con canales privados (canales que comienzan con `private-`) y los clientes solo pueden activar eventos que comienzan con `client-`.

Ejemplo de activación de un evento de notificación desde el cliente
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hola"});
```

> **Nota**
> En el código anterior, se envía información del evento `client-message` a todos los clientes suscritos a `private-user-1`, excepto al cliente actual (el cliente que envía la notificación no recibirá su propia notificación).

## Webhooks

Los webhooks se utilizan para recibir algunos eventos de los canales.

**Actualmente hay principalmente 2 eventos:**

- 1. channel_added
  Evento que se activa cuando un canal pasa de no tener clientes en línea a tener clientes en línea, es decir, un evento en línea.

- 2. channel_removed
  Evento que se activa cuando todos los clientes de un canal se desconectan, es decir, un evento fuera de línea.

> **Consejo**
> Estos eventos son muy útiles para mantener el estado en línea de los usuarios.

> **Nota**
> La dirección del webhook se configura en `config/plugin/webman/push/app.php`. Para ver el código que recibe y procesa eventos de webhook, consultar la lógica en `config/plugin/webman/push/route.php`. Debido a que refrescar la página puede causar una breve desconexión del usuario, lo que no debe considerarse como desconexión, `webman/push` realizará una verificación con retraso, por lo que los eventos en línea/fuera de línea pueden tener un retraso de 1-3 segundos.

## Proxy de wss (SSL)
HTTPS no admite conexiones WS, por lo que es necesario utilizar conexiones WSS. En este caso, se puede usar un proxy de Nginx para WSS, con una configuración similar a la siguiente:
```nginx
server {
    # .... otras configuraciones omitidas aquí ...

    location /app/<app_key>
    {
        proxy_pass http://127.0.0.1:3131;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```
**Nota**: En la configuración anterior, `<app_key>` se obtiene en `config/plugin/webman/push/app.php`.

Después de reiniciar Nginx, se puede usar el siguiente método para conectar al servidor
```js
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, obtenido en config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autorización de suscripción (solo para canales privados)
});
```
> **Nota**
> 1. La solicitud comienza con wss
> 2. No se especifica un puerto
> 3. Es obligatorio utilizar la conexión con el **nombre de dominio correspondiente al certificado SSL**.

## Instrucciones de uso de push-vue.js

1. Copiar el archivo push-vue.js al directorio del proyecto, por ejemplo: src/utils/push-vue.js

2. Importar en la página de Vue
```js

<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('El componente ha sido montado') 

  // Instanciar webman-push

  // Establecer conexión
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // dirección del websocket
    app_key: '<app_key, obtenido en config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autorización de suscripción (solo para canales privados)
  });

  // Suponiendo que el uid del usuario es 1
  var uid = 1;
  // El navegador escucha los mensajes del canal user-1, es decir, los mensajes del usuario con uid 1
  var user_channel = connection.subscribe('user-' + uid);

  // Cuando hay un evento de mensaje en el canal user-1
  user_channel.on('message', function (data) {
    // data contiene el contenido del mensaje
    console.log(data);
  });
  // Cuando hay un evento solicitud de amistad en el canal user-1
  user_channel.on('friendApply', function (data) {
    // data contiene la información relacionada con la solicitud de amistad
    console.log(data);
  });

  // Suponiendo que el ID del grupo es 2
  var group_id = 2;
  // El navegador escucha los mensajes del canal group-2, es decir, los mensajes del grupo 2
  var group_channel = connection.subscribe('group-' + group_id);
  // Cuando hay un evento de mensaje en el canal group-2
  group_channel.on('message', function (data) {
    // data contiene el contenido del mensaje
    console.log(data);
  });


})

</script>
```

## Otras direcciones de cliente
`webman/push` es compatible con pusher, las direcciones de cliente para otros idiomas (Java, Swift, .NET, Objective-C, Unity, Flutter, Android, IOS, AngularJS, entre otros) se pueden descargar desde: https://pusher.com/docs/channels/channels_libraries/libraries/
