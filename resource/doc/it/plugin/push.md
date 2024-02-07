## webman/push

`webman/push` è un plugin server di push gratuito, il client è basato su un modello di abbonamento, compatibile con [pusher](https://pusher.com), con numerosi client come JS, Android (Java), iOS (Swift), iOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS, ecc. Il backend del push SDK supporta PHP, Node, Ruby, Asp, Java, Python, Go, Swift, ecc. Il client ha incorporato il cuore pulsante e il ricollegamento automatico in caso di disconnessione, è molto semplice e stabile da utilizzare. Adatto per la messaggistica push, chat e molti altri scenari di comunicazione istantanea.

Il plugin include anche un client web in js chiamato push.js e un client uniapp `uniapp-push.js`, mentre altri client di linguaggi possono essere scaricati da https://pusher.com/docs/channels/channels_libraries/libraries/

> Il plugin richiede webman-framework >= 1.2.0

## Installazione

```sh
composer require webman/push
```

## Client (javascript)

**Includi il client javascript**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Utilizzo del client (Canale pubblico)**
```js
// Stabilisci la connessione
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // indirizzo del websocket
    app_key: '<app_key, ottenibile da config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autorizzazione dell'abbonamento (solo per canali privati)
});
// Supponiamo che l'uid dell'utente sia 1
var uid = 1;
// Il browser ascolta i messaggi del canale user-1, cioè i messaggi dell'utente con uid 1
var user_channel = connection.subscribe('user-' + uid);

// Quando il canale user-1 ha un evento di messaggio
user_channel.on('message', function(data) {
    // i dati contengono il contenuto del messaggio
    console.log(data);
});
// Quando il canale user-1 ha un evento di richiesta di amicizia
user_channel.on('friendApply', function (data) {
    // i dati contengono informazioni relative alla richiesta di amicizia
    console.log(data);
});

// Supponiamo che l'ID del gruppo sia 2
var group_id = 2;
// Il browser ascolta i messaggi del canale group-2, cioè i messaggi del gruppo 2
var group_channel = connection.subscribe('group-' + group_id);
// Quando c'è un evento di messaggio nel gruppo 2
group_channel.on('message', function(data) {
    // i dati contengono il contenuto del messaggio
    console.log(data);
});
```

> **Suggerimenti**
> Nell'esempio sopra, l'abbonamento subscribe implementa la sottoscrizione del canale, `message` `friendApply` sono eventi sul canale. I canali e gli eventi sono stringhe arbitrarie, non è necessario configurarli preventivamente lato server.

## Push lato server (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // in Webman puoi ottenere direttamente le configurazioni tramite config, in un ambiente non Webman è necessario inserire manualmente le configurazioni corrispondenti
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Invia un messaggio con evento message a tutti i client abbonati a user-1
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Ciao, questo è il contenuto del messaggio'
]);
```

## Canale privato
Nell'esempio precedente, qualsiasi utente può abbonarsi alle informazioni tramite Push.js, se le informazioni sono sensibili, ciò non è sicuro.

`webman/push` supporta l'abbonamento a canali privati, i canali privati iniziano con `private-`. Ad esempio
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // indirizzo del websocket
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // autorizzazione dell'abbonamento (solo per canali privati)
});

// Supponiamo che l'uid dell'utente sia 1
var uid = 1;
// Il browser ascolta i messaggi del canale privato-user-1
var user_channel = connection.subscribe('private-user-' + uid);
```

Quando un client si abbona a un canale privato (canale che inizia con `private-`), il browser invierà una richiesta di autorizzazione ajax (l'indirizzo ajax è configurato come parametro auth durante la creazione di un nuovo oggetto Push), lo sviluppatore può qui verificare se l'utente attuale ha il permesso di ascoltare questo canale. In questo modo viene garantita la sicurezza dell'abbonamento.

> Per ulteriori informazioni sull'autorizzazione si veda il codice in `config/plugin/webman/push/route.php`

## Push lato client
Negli esempi sopra, i client si abbonano a un canale e il server chiama l'API per inviare il messaggio. Anche webman/push supporta il push diretto da parte del client.

> **Attenzione**
> Il push direttamente tra i client è supportato solo per i canali privati (canali che iniziano con `private-`) e i client possono attivare solo eventi che iniziano con `client-`.

Esempio di attivazione dell'evento di push lato client
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"ciao"});
```

> **Attenzione**
> Il codice sopra invia i dati dell'evento `client-message` a tutti i client (tranne il client attuale) abbonati a `private-user-1` (il client non riceverà i dati che ha inviato).

## Webhook

Il webhook serve per ricevere alcuni eventi del canale.

**Attualmente ci sono principalmente 2 eventi:**

- 1. channel_added
  Quando un canale che non aveva client online ha client online, scatenando l'evento, o meglio, quando un evento è online

- 2. channel_removed
  Quando tutti i client di un canale vengono disconnessi, viene scatenato l'evento, o meglio, quando un evento è offline

> **Suggerimenti**
> Questi eventi sono molto utili per mantenere lo stato online degli utenti.

> **Attenzione**
> L'indirizzo del webhook è configurato in `config/plugin/webman/push/app.php`.
> Per la gestione degli eventi del webhook si veda la logica in `config/plugin/webman/push/route.php`.
> Poiché l'aggiornamento della pagina può causare una breve disconnessione degli utenti, webman/push effettuerà una valutazione ritardata, quindi gli eventi online/offline avranno un ritardo di 1-3 secondi.

## Proxy WSS (SSL)
Non è possibile utilizzare la connessione ws con https, è necessario utilizzare la connessione wss. In questo caso è possibile utilizzare nginx come proxy per wss, la configurazione è simile a quanto segue:
```
server {
    # .... qui vengono omesse altre configurazioni ...

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
**Nota: Nel config sopra, `<app_key>` viene recuperato da`config/plugin/webman/push/app.php`**

Dopo il riavvio di nginx, la connessione al server avviene nel seguente modo
```
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, ottenibile da config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autorizzazione dell'abbonamento (solo per canali privati)
```
> **Nota**
> 1. L'indirizzo della richiesta ha un wss iniziale
> 2. Non è necessario specificare la porta
> 3. È necessario utilizzare il nome di dominio corrispondente al **certificato SSL** per la connessione.
## Istruzioni per l'uso di push-vue.js

1. Copia il file push-vue.js nella directory del progetto, ad esempio: src/utils/push-vue.js

2. Importalo nella pagina Vue
```js
<script lang="ts" setup>
import { onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('Componente montato')

  // Instanziare webman-push

  // Stabilire la connessione
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // indirizzo del websocket
    app_key: '<app_key, da ottenere da config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Autenticazione per la sottoscrizione (solo per canali privati)
  });

  // Supponiamo che l'UID dell'utente sia 1
  var uid = 1;
  // Il browser ascolta i messaggi del canale user-1, ovvero i messaggi dell'utente con UID 1
  var user_channel = connection.subscribe('user-' + uid);

  // Quando il canale user-1 riceve un messaggio di tipo "message"
  user_channel.on('message', function (data) {
    // I dati contengono il messaggio
    console.log(data);
  });
  // Quando il canale user-1 riceve un messaggio di tipo "friendApply"
  user_channel.on('friendApply', function (data) {
    // I dati contengono le informazioni relative alla richiesta di amicizia
    console.log(data);
  });

  // Supponiamo che l'ID del gruppo sia 2
  var group_id = 2;
  // Il browser ascolta i messaggi del canale group-2, ovvero i messaggi del gruppo con ID 2
  var group_channel = connection.subscribe('group-' + group_id);
  // Quando il canale group-2 riceve un messaggio di tipo "message"
  group_channel.on('message', function (data) {
    // I dati contengono il messaggio
    console.log(data);
  });
})
</script>
```

## Altri indirizzi client
`webman/push` è compatibile con Pusher. Per scaricare altri client in diversi linguaggi di programmazione (Java, Swift, .NET, Objective-C, Unity, Flutter, Android, IOS, AngularJS, ecc.), consulta il seguente link:
https://pusher.com/docs/channels/channels_libraries/libraries/
