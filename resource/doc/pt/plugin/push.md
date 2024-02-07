## webman/push

`webman/push` é um plugin de servidor de push gratuito, com o cliente baseado em modo de assinatura, compatível com o [pusher](https://pusher.com), e com vários clientes, tais como JS, Android (Java), iOS (Swift), iOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS, entre outros. O SDK de push no backend suporta PHP, Node, Ruby, Asp, Java, Python, Go, Swift, entre outros. O cliente tem reconexão automática e pulsos embutidos, sendo muito simples e estável de usar. É adequado para vários cenários de comunicação instantânea, como envio de mensagens e bate-papo.

O plugin inclui um cliente web JS `push.js` e um cliente `uniapp-push.js`, outros clientes em diferentes idiomas podem ser baixados em https://pusher.com/docs/channels/channels_libraries/libraries/

> O plugin requer webman-framework>=1.2.0

## Instalação

```sh
composer require webman/push
```

## Cliente (javascript)

**Importar o cliente javascript**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Uso do cliente (canal público)**
```js
// Estabelecer a conexão
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // endereço websocket
    app_key: '<chave do aplicativo, obtida em config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autenticação para subscrição (apenas para canais privados)
});
// Supondo que o id do usuário é 1
var uid = 1;
// O navegador ouve as mensagens do canal user-1, ou seja, mensagens do usuário com id 1
var user_channel = connection.subscribe('user-' + uid);

// Quando o canal user-1 tem mensagens do evento
user_channel.on('message', function(data) {
    // os dados contêm o conteúdo da mensagem
    console.log(data);
});
// Quando o canal user-1 tem mensagens do evento friendApply
user_channel.on('friendApply', function (data) {
    // os dados contêm informações relacionadas à solicitação de amizade
    console.log(data);
});

// Supondo que o ID do grupo é 2
var group_id = 2;
// O navegador ouve as mensagens do canal group-2, ou seja, as mensagens do grupo 2
var group_channel = connection.subscribe('group-' + group_id);
// Quando o grupo 2 tem mensagens do evento message
group_channel.on('message', function(data) {
    // os dados contêm o conteúdo da mensagem
    console.log(data);
});
```

> **Dicas**
> No exemplo acima, `subscribe` realiza a assinatura do canal, `message` e `friendApply` são eventos do canal. Os canais e eventos são strings arbitrários e não precisam ser configurados antecipadamente no servidor.

## Push do servidor (PHP)

```php
use Webman\Push\Api;
$api = new Api(
    // em um ambiente webman, pode-se usar config para obter configurações, em um ambiente não-webman é necessário configurar as configurações manualmente
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Enviar mensagem do evento message para todos os clientes assinados no user-1
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Olá, este é o conteúdo da mensagem'
]);
```

## Canal privado
No exemplo acima, qualquer usuário pode assinar informações usando `Push.js`, o que não é seguro se as informações forem sensíveis.

`webman/push` suporta assinatura de canais privados, que começam com `private-`. Por exemplo
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // endereço websocket
    app_key: '<chave do aplicativo>',
    auth: '/plugin/webman/push/auth' // autenticação para subscrição (apenas para canais privados)
});

// Supondo que o id do usuário é 1
var uid = 1;
// O navegador ouve as mensagens do canal privada user-1
var user_channel = connection.subscribe('private-user-' + uid);
```

Quando um cliente assina um canal privado (um canal que começa com `private-`), o navegador faz uma solicitação de autenticação Ajax (o endereço Ajax é configurado no parâmetro `auth` ao criar um novo Push). O desenvolvedor pode verificar se o usuário atual tem permissão para ouvir esse canal. Isso garante a segurança da assinatura.

> Consulte `config/plugin/webman/push/route.php` para mais informações sobre a autenticação.

## Push do cliente
Os exemplos acima demonstram a assinatura de um canal por parte do cliente e chamadas de API do servidor para enviar mensagens. O `webman/push` também suporta o envio direto de mensagens pelo cliente.

> **Atenção**
> O envio de mensagens entre clientes só é compatível com canais privados (os canais devem começar com `private-`), e os clientes só podem acionar eventos que começam com `client-`.

Exemplo de gatilho de evento do cliente
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"Olá"});
```

> **Atenção**
> O código acima envia dados do evento `client-message` para todos os clientes assinados (exceto o cliente atual) no canal `private-user-1` (o cliente que envia a mensagem não receberá seus próprios dados enviados).

## Webhooks

Os webhooks são usados para receber eventos de canal.

**Atualmente, existem principalmente dois eventos:**

- 1. channel_added
  Disparado quando um canal passa de nenhum cliente online para pelo menos um cliente online, ou seja, um evento online.

- 2. channel_removed
  Disparado quando todos os clientes de um canal estão offline, ou seja, um evento offline.

> **Dicas**
> Esses eventos são muito úteis para manter o controle do status online dos usuários.

> **Atenção**
> O endereço do webhook é configurado em `config/plugin/webman/push/app.php`.
> Confira a lógica para receber e processar eventos de webhook em `config/plugin/webman/push/route.php`.
> Devido a pequenas interrupções causadas pelo atualização da página não devem ser consideradas como offline, o `webman/push` faz uma verificação atrasada, resultando em um atraso de 1 a 3 segundos nos eventos online/offline.

## Proxy WSS (SSL)
Não é possível usar conexões ws via https, é necessário usar conexões wss. Nesse caso, é possível usar um proxy nginx para wss, configurando-o da seguinte forma:

``` 
server {
    # .... Outras configurações aqui ...

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
**Atenção: A configuração acima utiliza o `<app_key>` obtido em `config/plugin/webman/push/app.php`.**

Após reiniciar o nginx, a conexão com o servidor é feita da seguinte forma:
``` 
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<chave do aplicativo, obtida em config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autenticação para subscrição (apenas para canais privados)
});
```
> **Atenção**
> 1. O endereço começa com wss
> 2. A porta não é necessária
> 3. Deve-se usar o nome de domínio correspondente ao **certificado SSL** para a conexão.
## Instruções de uso do push-vue.js

1. Copie o arquivo push-vue.js para o diretório do projeto, por exemplo: src/utils/push-vue.js

2. Importe no interior da página vue
```js

<script lang="ts" setup>
import { onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('Componente montado') 

  // Instanciação do webman-push

  // Estabelecendo conexão
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // endereço do websocket
    app_key: '<app_key, obtido em config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // autorização de inscrição (somente para canais privados)
  });

  // Supondo que o id do usuário seja 1
  var uid = 1;
  // O navegador escuta as mensagens do canal user-1, ou seja, as mensagens do usuário com id 1
  var user_channel = connection.subscribe('user-' + uid);

  // Quando o canal user-1 tem um evento de mensagem
  user_channel.on('message', function (data) {
    // data contém o conteúdo da mensagem
    console.log(data);
  });
  // Quando o canal user-1 tem um evento de solicitação de amigo
  user_channel.on('friendApply', function (data) {
    // data contém informações relacionadas à solicitação de amigo
    console.log(data);
  });

  // Supondo que o id do grupo seja 2
  var group_id = 2;
  // O navegador escuta as mensagens do canal group-2, ou seja, escuta as mensagens do grupo 2
  var group_channel = connection.subscribe('group-' + group_id);
  // Quando o grupo 2 tem um evento de mensagem
  group_channel.on('message', function (data) {
    // data contém o conteúdo da mensagem
    console.log(data);
  });

})

</script>
```

## Outros endereços de clientes
`webman/push` Compatível com pusher, endereços de download de clientes em outros idiomas (Java Swift .NET Objective-C Unity Flutter Android IOS AngularJS, etc.):
https://pusher.com/docs/channels/channels_libraries/libraries/
