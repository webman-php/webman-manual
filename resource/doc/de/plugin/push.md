## webman/push

`webman/push` ist ein kostenloses Server-Push-Plugin, das auf dem Abonnementmodell basiert und mit [pusher](https://pusher.com) kompatibel ist. Es hat viele verschiedene Clients wie JS, Android (Java), iOS (Swift), iOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS usw. Das Backend-Push-SDK unterstützt PHP, Node, Ruby, Asp, Java, Python, Go, Swift usw. Die Client-Integration ist mit automatischer Herzfrequenzüberwachung und automatischer Wiederverbindung bei Verbindungsabbruch sehr einfach und stabil. Es eignet sich für eine Vielzahl von Echtzeitkommunikationsszenarien wie z.B. Nachrichtenversand und Chats.

Das Plugin enthält einen eigenen Web-JS-Client push.js sowie den uniapp-Client `uniapp-push.js`. Andere Sprachen-Clients können unter https://pusher.com/docs/channels/channels_libraries/libraries/ heruntergeladen werden.

> Das Plugin benötigt webman-framework >= 1.2.0

## Installation

```sh
composer require webman/push
```

## Client (javascript)

**Integration des Javascript-Clients**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Verwendung des Clients (öffentlicher Kanal)**
```js
// Verbindung aufbauen
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // Websocket-Adresse
    app_key: '<app_key, in config/plugin/webman/push/app.php erhältlich>',
    auth: '/plugin/webman/push/auth' // Abonnementauthentifizierung (nur für private Kanäle)
});
// Angenommen, die Benutzer-UID lautet 1
var uid = 1;
// Browser lauscht auf Kanal user-1, d.h. Nachrichten des Benutzers mit der UID 1
var user_channel = connection.subscribe('user-' + uid);

// Wenn auf dem Kanal user-1 eine Nachrichten-Ereignis auftritt
user_channel.on('message', function(data) {
    // data enthält den Nachrichteninhalt
    console.log(data);
});
// Wenn auf dem Kanal user-1 ein friendApply-Ereignis auftritt
user_channel.on('friendApply', function (data) {
    // data enthält relevante Informationen zur Freundschaftsanfrage
    console.log(data);
});

// Angenommen, die Gruppen-ID lautet 2
var group_id = 2;
// Der Browser lauscht auf dem Kanal group-2, d.h. er lauscht auf den Gruppennachrichten der Gruppe 2
var group_channel = connection.subscribe('group-' + group_id);
// Wenn auf dem Kanal der Gruppe 2 eine Nachricht auftritt
group_channel.on('message', function(data) {
    // data enthält den Nachrichteninhalt
    console.log(data);
});
```

> **Tipps**
> Im obigen Beispiel erfolgt durch das Abonnement des Kanals und die Verwendung von `message` und `friendApply` die Überwachung von Ereignissen auf dem Kanal. Kanal und Ereignis sind beliebige Zeichenketten und müssen nicht vorab auf dem Server konfiguriert werden.

## Serverseitiger Push (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // In Webman können die Konfigurationen direkt mit config abgerufen werden. In nicht-Webman-Umgebungen müssen die entsprechenden Konfigurationen manuell eingesetzt werden
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Pushen einer Nachrichten-Ereignisnachricht an alle Clients, die den Kanal user-1 abonniert haben
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Hallo, dies ist der Nachrichteninhalt'
]);
```

## Private Kanäle
In den obigen Beispielen kann jeder Benutzer über Push.js Informationen abonnieren. Wenn es sich bei den Informationen um sensible Daten handelt, ist dies nicht sicher.

`webman/push` unterstützt das Abonnement von privaten Kanälen, die mit `private-` beginnen. Zum Beispiel
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // Websocket-Adresse
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // Abonnementauthentifizierung (nur für private Kanäle)
});

// Angenommen, die Benutzer-UID lautet 1
var uid = 1;
// Browser lauscht auf dem privaten Kanal private-user-1
var user_channel = connection.subscribe('private-user-' + uid);
```

Wenn ein Client einen privaten Kanal (`private-`-Präfix) abonniert, sendet der Browser eine AJAX-Authentifizierungsanfrage (der AJAX-Endpunkt ist die Adresse, die im `auth`-Parameter der neuen Push-Konfiguration festgelegt ist). Entwickler können an dieser Stelle prüfen, ob der aktuelle Benutzer berechtigt ist, diesen Kanal zu überwachen. So wird sichergestellt, dass das Abonnement sicher ist.

> Informationen zur Authentifizierung finden Sie im Code von `config/plugin/webman/push/route.php`

## Client-Push
In den obigen Beispielen abonniert der Client einen bestimmten Kanal, und der Server ruft die API auf, um Nachrichten zu pushen. webman/push unterstützt auch das direkte Pushen von Nachrichten durch den Client.

> **Achtung**
> Das direkte Pushen von Client zu Client wird nur für private Kanäle unterstützt (Kanäle, die mit `private-` beginnen), und der Client kann nur Ereignisse auslösen, die mit `client-` beginnen.

Beispiel für das Auslösen eines Ereignisses durch den Client-Push
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"Hallo"});
```

> **Achtung**
> Der obige Code pusht Daten für alle (außer dem aktuellen Client), die den Kanal `private-user-1` abonniert haben, für das Ereignis `client-message` (Der pushende Client erhält die von ihm selbst gepushten Daten nicht).

## Webhooks

Webhooks werden verwendet, um bestimmte Ereignisse auf Kanälen zu erhalten.

**Derzeit gibt es hauptsächlich 2 Ereignisse:**

- 1. channel_added
  Dieses Ereignis tritt auf, wenn ein Kanal von keinem Client online zu mindestens einem Client online wechselt, oder anders ausgedrückt, wenn es online ist.

- 2. channel_removed
  Dieses Ereignis tritt auf, wenn alle Clients eines Kanals offline gehen, oder anders ausgedrückt, wenn es offline ist.

> **Tipps**
> Diese Ereignisse sind sehr nützlich, um den Benutzerstatus online zu halten.

> **Achtung**
> Die Webhook-Adresse wird in `config/plugin/webman/push/app.php` konfiguriert.
> Der Code zur Verarbeitung von Webhook-Ereignissen ist in der Logik in `config/plugin/webman/push/route.php` zu finden.
> Da ein kurzes Offline-Gehen des Benutzers aufgrund einer Seitenaktualisierung nicht als Offline betrachtet werden sollte, führt webman/push eine verzögerte Überprüfung durch, so dass Online- und Offline-Ereignisse mit einer Verzögerung von 1-3 Sekunden auftreten können.

## WSS-Proxy (SSL)
Da keine WS-Verbindung über HTTPS möglich ist, ist eine WSS-Verbindung erforderlich. In solchen Fällen kann ein Nginx als WSS-Proxy eingerichtet werden. Die Konfiguration ist ähnlich wie folgt:
```
server {
    # .... Hier werden andere Konfigurationen ausgelassen ...

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
**Hinweis: In der obigen Konfiguration wird `<app_key>` aus `config/plugin/webman/push/app.php` bezogen.**

Nach einem Neustart von Nginx kann der Server wie folgt verbunden werden
```
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, erhältlich in config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Abonnementauthentifizierung (nur für private Kanäle)
});
```
> **Achtung**
> 1. Die Anforderungs-URL beginnt mit wss
> 2. Es wird kein Port angegeben
> 3. Es muss mit der Domain des **SSL-Zertifikats** verbunden werden

## Verwendung von push-vue.js

1. Kopieren Sie die Datei push-vue.js in das Projektverzeichnis, z.B. src/utils/push-vue.js

2. Fügen Sie sie in eine Vue-Seite ein
```js
<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('Komponente wurde hinzugefügt') 

  // Instanzieren von webman-push

  // Verbindung aufbauen
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // Websocket-Adresse
    app_key: '<app_key, in config/plugin/webman/push/app.php erhältlich>',
    auth: '/plugin/webman/push/auth' // Abonnementauthentifizierung (nur für private Kanäle)
  });

  // Angenommen, die Benutzer-UID lautet 1
  var uid = 1;
  // Browser lauscht auf Kanal user-1, d.h. Nachrichten des Benutzers mit der UID 1
  var user_channel = connection.subscribe('user-' + uid);

  // Wenn auf dem Kanal user-1 eine Nachrichten-Ereignis auftritt
  user_channel.on('message', function (data) {
    // data enthält den Nachrichteninhalt
    console.log(data);
  });
  // Wenn auf dem Kanal user-1 ein friendApply-Ereignis auftritt
  user_channel.on('friendApply', function (data) {
    // data enthält relevante Informationen zur Freundschaftsanfrage
    console.log(data);
  });

  // Angenommen, die Gruppen-ID lautet 2
  var group_id = 2;
  // Der Browser lauscht auf dem Kanal group-2, d.h. er lauscht auf den Gruppennachrichten der Gruppe 2
  var group_channel = connection.subscribe('group-' + group_id);
  // Wenn auf dem Kanal der Gruppe 2 eine Nachricht auftritt
  group_channel.on('message', function (data) {
    // data enthält den Nachrichteninhalt
    console.log(data);
  });


})

</script>
```

## Andere Client-Adressen 

`webman/push` ist mit pusher kompatibel, daher können Bibliotheken für andere Sprachen (Java Swift .NET Objective-C Unity Flutter Android IOS AngularJS usw.) unter https://pusher.com/docs/channels/channels_libraries/libraries/ heruntergeladen werden.
