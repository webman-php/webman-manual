
## webman/push

`webman/push` est un plugin de serveur de push gratuit, le client est basé sur un mode d'abonnement, compatible avec [pusher](https://pusher.com), prend en charge de nombreux clients tels que JS, Android (java), iOS (swift), iOS(Obj-C), uniapp, .NET, Unity, Flutter, AngularJS, etc. Le SDK de push côté serveur prend en charge PHP, Node, Ruby, Asp, Java, Python, Go, Swift, etc. Le client est équipé d'un système de battements cardiaques intégré et de reconnexion automatique en cas de déconnexion, ce qui le rend très simple et stable à utiliser. Convient pour de nombreux scénarios de communication instantanée tels que les notifications push et les chats.

Le plugin est livré avec un client web en javascript `push.js` ainsi qu'un client uniapp `uniapp-push.js`, les clients dans d'autres langages peuvent être téléchargés à l'adresse suivante : https://pusher.com/docs/channels/channels_libraries/libraries/

> Le plugin nécessite webman-framework>=1.2.0

## Installation

```sh
composer require webman/push
```

## Client (javascript)

**Importer le client javascript**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Utilisation du client (public channels)**
```js
// Établir une connexion
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // adresse du websocket
    app_key: '<clé de l'application, obtenue dans config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Autorisation d'abonnement (limitée aux canaux privés)
});
// Supposons que l'uid de l'utilisateur est 1
var uid = 1;
// Le navigateur écoute les messages du canal utilisateur-1, c'est-à-dire les messages de l'utilisateur dont l'uid est 1
var user_channel = connection.subscribe('user-' + uid);

// Lorsqu'il y a un événement de message sur le canal utilisateur-1
user_channel.on('message', function(data) {
    // les données contiennent le contenu du message
    console.log(data);
});
// Lorsqu'il y a un événement de demande d'ami sur le canal utilisateur-1
user_channel.on('friendApply', function (data) {
    // les données contiennent les informations relatives à la demande d'ami
    console.log(data);
});

// Supposons que l'ID du groupe est 2
var group_id = 2;
// Le navigateur écoute les messages du canal groupe-2, c'est-à-dire les messages du groupe 2
var group_channel = connection.subscribe('group-' + group_id);
// Lorsqu'il y a un événement de message sur le canal groupe-2
group_channel.on('message', function(data) {
    // les données contiennent le contenu du message
    console.log(data);
});
```

> **Conseils**
> Dans l'exemple ci-dessus, la méthode subscribe permet de s'abonner à un canal, `message` `friendApply` sont des événements sur le canal. Les canaux et les événements sont des chaînes arbitraires, ils n'ont pas besoin d'être configurés préalablement côté serveur.

## Push côté serveur (PHP)

```php
use Webman\Push\Api;
$api = new Api(
    // Sous webman, les configurations peuvent être directement obtenues via config, s'il ne s'agit pas d'un environnement webman, les configurations correspondantes doivent être saisies manuellement.
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Envoyer un message d'événement message à tous les clients abonnés à user-1
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Bonjour, voici le contenu du message'
]);
```

## Channels privés
Dans les exemples ci-dessus, n'importe quel utilisateur peut s'abonner aux informations à l'aide de Push.js, ce qui n'est pas sécurisé si les informations sont sensibles.

`webman/push` prend en charge les abonnements à des canaux privés, les canaux privés commencent par `private-`. Par exemple
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // adresse websocket
    app_key: '<clé de l'application>',
    auth: '/plugin/webman/push/auth' // Autorisation d'abonnement (limitée aux canaux privés)
});

// Supposons que l'uid de l'utilisateur est 1
var uid = 1;
// Le navigateur écoute les messages du canal privé private-user-1
var user_channel = connection.subscribe('private-user-' + uid);
```

Lorsqu'un client s'abonne à un canal privé (avec le préfixe `private-`), le navigateur envoie une requête d'autorisation ajax (l'adresse ajax est configurée lors de la configuration de la nouvelle instance Push), le développeur peut ici vérifier si l'utilisateur actuel a le droit d'écouter ce canal. Cela garantit la sécurité de l'abonnement.

> Pour plus d'informations sur l'autorisation, consultez le code dans `config/plugin/webman/push/route.php`

## Push côté client
Les exemples ci-dessus concernent tous l'abonnement du client à un certain canal, avec l'appel de l'API côté serveur pour envoyer des messages. webman/push prend également en charge l'envoi direct de messages par le client.

> **Remarque**
> L'envoi de messages entre clients prend en charge uniquement les canaux privés (canaux commençant par `private-`), et le client ne peut déclencher que des événements commençant par `client-`.

Exemple de déclenchement d'événement push côt client
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **Remarque**
> Le code ci-dessus envoie des données d'événement `client-message` à tous les clients abonnés à `private-user-1` (le client qui envoie les données n'en recevra pas). 

## Webhooks

Les webhooks sont utilisés pour recevoir certains événements des canaux.

**Il y a actuellement principalement 2 événements:**

- 1. channel_added
  Lorsqu'un canal passe d'aucun client en ligne à au moins un client en ligne, l'événement est déclenché, ou en d'autres termes, l'événement en ligne.

- 2. channel_removed
  Lorsque tous les clients d'un canal se déconnectent, l'événement est déclenché, ou en d'autres termes, l'événement hors ligne.

> **Conseils**
> Ces événements sont très utiles pour maintenir l'état en ligne des utilisateurs.

> **Remarque**
> L'URL du webhook est configurée dans `config/plugin/webman/push/app.php`.
> Le code recevant et traitant les événements webhook est disponible dans la logique de `config/plugin/webman/push/route.php`.
> Étant donné qu'un utilisateur hors ligne temporairement en raison d'un rafraîchissement de page ne doit pas être considéré comme hors ligne, webman/push effectuera une évaluation différée, ce qui signifie qu'il y aura un délai de 1 à 3 secondes pour les événements en ligne/hors ligne.

## Proxy wss (SSL)
Il n'est pas possible d'utiliser une connexion ws sous HTTPS, une connexion wss est nécessaire. Dans ce cas, nginx peut être utilisé comme proxy wss, la configuration est similaire à celle ci-dessous :
```
server {
    # .... d'autres configurations sont omises ici ...

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
**Remarque** La `<app_key>` dans la configuration ci-dessus est obtenue dans `config/plugin/webman/push/app.php`

Après avoir redémarré nginx, la connexion au serveur peut être établie comme suit 
```js
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<clé de l'application, obtenue dans config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Autorisation d'abonnement (limitée aux canaux privés)
});
```
> **Remarque**
> 1. L'adresse de la demande commence par wss
> 2. Aucun port n'est spécifié
> 3. La connexion doit être établie avec le **nom de domaine correspondant au certificat SSL**

## Instructions d'utilisation de push-vue.js

1. Copiez le fichier push-vue.js dans le répertoire du projet, par exemple : src/utils/push-vue.js

2. Importez-le dans la page vue
```js
<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('Le composant a été monté') 

  //Instanciation de webman-push

  // Établir une connexion
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // adresse du websocket
    app_key: '<clé de l'application, obtenue dans config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Autorisation d'abonnement (limitée aux canaux privés)
  });

  // Supposons que l'uid de l'utilisateur est 1
  var uid = 1;
  // Le navigateur écoute les messages du canal user-1, c'est-à-dire les messages de l'utilisateur dont l'uid est 1
  var user_channel = connection.subscribe('user-' + uid);

  // Lorsqu'il y a un événement de message sur le canal user-1
  user_channel.on('message', function (data) {
    // les données contiennent le contenu du message
    console.log(data);
  });
  // Lorsqu'il y a un événement de demande d'ami sur le canal user-1
  user_channel.on('friendApply', function (data) {
    // les données contiennent les informations relatives à la demande d'ami
    console.log(data);
  });

  // Supposons que l'ID du groupe est 2
  var group_id = 2;  
  // Le navigateur écoute les messages du canal group-2, c'est-à-dire les messages du groupe 2
  var group_channel = connection.subscribe('group-' + group_id);
  // Lorsqu'il y a un événement de message sur le canal group-2
  group_channel.on('message', function (data) {
    // les données contiennent le contenu du message
    console.log(data);
  });

})

</script>
``` 

## Autres adresses de clients
`webman/push` est compatible avec pusher, les adresses des clients dans d'autres langages (Java Swift .NET Objective-C Unity Flutter Android IOS AngularJS, etc.) sont disponibles en téléchargement à l'adresse : https://pusher.com/docs/channels/channels_libraries/libraries/
