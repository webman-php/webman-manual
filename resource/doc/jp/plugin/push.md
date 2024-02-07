## webman/push

`webman/push` は、無料のプッシュサーバープラグインであり、クライアントはサブスクリプションモデルに基づいており、[pusher](https://pusher.com) と互換性があり、JS、Android (Java)、iOS (Swift)、iOS (Obj-C)、uniapp、.NET、Unity、Flutter、AngularJSなど、多くのクライアントが利用可能です。バックエンドプッシュSDKはPHP、Node、Ruby、Asp、Java、Python、Go、Swiftなどに対応しています。クライアントには、ハートビートと自動再接続が組み込まれており、非常に簡単かつ安定して使用できます。メッセージの送信やチャットなど、多くのリアルタイムコミュニケーションシナリオに適しています。

プラグインには、ウェブページのJavaScriptクライアント `push.js` とuniappクライアント `uniapp-push.js` が含まれています。その他の言語のクライアントは、https://pusher.com/docs/channels/channels_libraries/libraries/ からダウンロードできます。

> プラグインは `webman-framework>=1.2.0` が必要です。

## インストール

```sh
composer require webman/push
```

## クライアント (javascript)

**JavaScriptクライアントのインポート**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**クライアントの使用（パブリックチャネル）**
```js
// 接続の確立
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocketアドレス
    app_key: '<app_key, config/plugin/webman/push/app.php で取得>',
    auth: '/plugin/webman/push/auth' // サブスクライブの認証（プライベートチャネルのみ）
});
// ユーザーuidが1と仮定する
var uid = 1;
// ブラウザが user-1 チャネルのメッセージを監視する（つまり、uidが1のユーザーのメッセージ）
var user_channel = connection.subscribe('user-' + uid);

// user-1 チャネルに message イベントのメッセージがある場合
user_channel.on('message', function(data) {
    // dataにはメッセージの内容が含まれています
    console.log(data);
});
// user-1 チャネルに friendApply イベントのメッセージがある場合
user_channel.on('friendApply', function (data) {
    // dataには友達申請に関する情報が含まれています
    console.log(data);
});

// グループIDが2と仮定する
var group_id = 2;
// ブラウザが group-2 チャネルのメッセージを監視する（つまり、グループ2のメッセージを監視する）
var group_channel = connection.subscribe('group-' + group_id);
// group-2 に message イベントがある場合
group_channel.on('message', function(data) {
    // dataにはメッセージの内容が含まれています
    console.log(data);
});
```

> **Tips**
> 上記の例では、`subscribe` がチャネルのサブスクライブを実現し、`message` `friendApply` はチャネル上のイベントです。チャネルとイベントは任意の文字列であり、サーバーサイドで事前に構成する必要はありません。

## サーバー側のプッシュ(PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // Webman環境ではconfigを直接使用できますが、Webman環境以外では対応する構成を手動で記述する必要があります
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// user-1 をサブスクライブしているすべてのクライアントに message イベントのメッセージをプッシュする
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'こんにちは、これはメッセージの内容です'
]);
```

## プライベートチャネル
上記の例では、どのユーザーも Push.js を使用して情報をサブスクライブできますが、それはセンシティブな情報には安全ではありません。

`webman/push` は、プライベートチャネルのサブスクライブをサポートし、プライベートチャネルは `private-` で始まります。たとえば、次のようになります。
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocketアドレス
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // サブスクリプション認証（プライベートチャネルのみ）
});

// ユーザーuidが1と仮定する
var uid = 1;
// ブラウザが private-user-1 プライベートチャンネルのメッセージを監視する
var user_channel = connection.subscribe('private-user-' + uid);
```

クライアントがプライベートチャネル（`private-` で始まるチャネル）をサブスクライブする場合、ブラウザはajax認証リクエスト（ajaxのURLは new Push 時の auth パラメータで設定されたものです）を送信します。ここで、開発者は現在のユーザーがこのチャネルを監視する権限があるかどうかを判断できます。これにより、サブスクライブの安全性が確保されます。

> 認証に関する詳細は `config/plugin/webman/push/route.php` のコードを参照してください。

## クライアント側のプッシュ
上記の例はすべてクライアントが特定のチャネルをサブスクライブするものであり、サーバーサイドでAPIを介してプッシュされるものです。webman/push はクライアント側の直接的なメッセージプッシュもサポートしています。

> **注意**
> クライアント間のプッシュはプライベートチャネル（`private-` で始まるチャネル）のみをサポートし、クライアントは `client-` で始まるイベントのみをトリガできます。

クライアントがイベントをトリガしてメッセージをプッシュする例
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"こんにちは"});
```

> **注意**
> 以上のコードは、`private-user-1` をサブスクライブしているすべての（現在のクライアントを除く）クライアントに `client-message` イベントのデータをプッシュします（プッシュするクライアントは自分自身のプッシュデータを受け取りません）。

## webhooks

webhookは、チャネルの特定のイベントを受信するために使用されます。

**現在、主なイベントは2つあります:**

- 1、channel_added
  特定のチャネルにクライアントがオフラインからオンラインになった場合、またはオンラインイベントがトリガされた場合

- 2、channel_removed
  特定のチャネルにおいて全てのクライアントがオフラインになった場合、またはオフラインイベントがトリガされた場合

> **Tips**
> これらのイベントは、ユーザーのオンライン状態を維持する際に非常に役立ちます。

> **注意**
> webhookのURLは`config/plugin/webman/push/app.php`で設定されています。
> webhookイベントを受信し処理するためのコードについては、`config/plugin/webman/push/route.php` のロジックを参照してください。
> ページのリロードによる一時的なオフラインはオフラインと見なすべきではないため、webman/pushは遅延判定が行われるため、オンライン/オフラインのイベントには1-3秒の遅延があります。

## WSSプロキシ（SSL）
httpsではws接続ができないため、wss接続が必要となります。この場合、nginxを使用してwssをプロキシすることができます。構成は以下のようになります:
```
server {
    # .... その他の設定は省略 ...
    
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

**上記の構成の中の`<app_key>` は `config/plugin/webman/push/app.php` から取得されます**

Nginxを再起動した後、以下のようにサーバーに接続できます
```js
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, config/plugin/webman/push/app.php で取得>',
    auth: '/plugin/webman/push/auth' // サブスクリプション認証（プライベートチャネルのみ）
});
```

> **注意**
> 1. リクエストアドレスは `wss` で始まります
> 2. ポート番号を記載しない
> 3. **SSL証明書に対応するドメイン** を使用する必要があります


## push-vue.jsの使用方法

1、ファイル`push-vue.js`をプロジェクトディレクトリにコピーします。例：`src/utils/push-vue.js`

2、Vueページ内で以下のようにインポートします。
```js
<script lang="ts" setup>
import { onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('コンポーネントがマウントされました')

  // webman-pushのインスタンス化

  // 接続を確立
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocketアドレス
    app_key: '<app_key, config/plugin/webman/push/app.phpで取得可能>',
    auth: '/plugin/webman/push/auth' // サブスクリプション認証（プライベートチャンネルに限定）
  });

  // ユーザーuidが1であると仮定する
  var uid = 1;
  // ブラウザがuser-1チャンネルのメッセージを監視し、つまりユーザーuidが1のユーザーのメッセージを監視する
  var user_channel = connection.subscribe('user-' + uid);

  // user-1チャンネルにmessageイベントのメッセージがある場合
  user_channel.on('message', function (data) {
    // dataにはメッセージの内容が含まれています
    console.log(data);
  });
  // user-1チャンネルにfriendApplyイベントのメッセージがある場合
  user_channel.on('friendApply', function (data) {
    // dataには友達申請に関する情報が含まれています
    console.log(data);
  });

  // グループidが2であると仮定する
  var group_id = 2;
  // ブラウザがgroup-2チャンネルのメッセージを監視し、つまりグループ2のグループメッセージを監視する
  var group_channel = connection.subscribe('group-' + group_id);
  // group-2にmessageイベントのメッセージがある場合
  group_channel.on('message', function (data) {
    // dataにはメッセージの内容が含まれています
    console.log(data);
  });
})
</script>
```

## その他のクライアントのURL
`webman/push`はpusherと互換性があり、他の言語（Java Swift .NET Objective-C Unity Flutter Android IOS AngularJSなど）のクライアントのURLは次の場所からダウンロードできます：
https://pusher.com/docs/channels/channels_libraries/libraries/
