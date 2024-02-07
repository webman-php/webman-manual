
## webman/push

`webman/push`, ücretsiz bir itme (push) sunucu eklentisidir. İstemci abonelik modeline dayalıdır ve [pusher](https://pusher.com) ile uyumludur. JS, Android (java), IOS (swift), IOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS gibi birçok istemciyi destekler. Arka planda itme SDK'sı PHP, Node, Ruby, Asp, Java, Python, Go, Swift gibi dillere destek verir. İstemci otomatik olarak kalp atışı ve bağlantı kesilmesi durumunda otomatik yeniden bağlanma özelliğine sahiptir ve kullanımı oldukça basit ve istikrarlıdır. Mesaj itme, sohbet ve birçok anlık iletişim senaryosu için uygundur. 

Eklenti, bir web sayfası JS istemcisi olan push.js ve uniapp istemcisi olan `uniapp-push.js` ile birlikte gelir. Diğer dil istemcilerini https://pusher.com/docs/channels/channels_libraries/libraries/ adresinden indirebilirsiniz.

> Eklenti için webman-framework>=1.2.0 gereklidir.

## Kurulum

```sh
composer require webman/push
```

## İstemci (javascript)

**JavaScript istemcisi içeri aktarma**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**İstemci kullanımı (genel kanal)**
```js
// Bağlantı kurma
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket adresi
    app_key: '<app_key, config/plugin/webman/push/app.php dosyasından alınır>',
    auth: '/plugin/webman/push/auth' // Abonelik yetkilendirme (yalnızca özel kanallar için)
});
// Kullanıcı uid'si 1 olarak varsayalım
var uid = 1;
// Tarayıcı, user-1 kanalındaki mesajları dinler, yani kullanıcı uid'si 1 olan kullanıcı mesajları
var user_channel = connection.subscribe('user-' + uid);

// user-1 kanalında mesaj etkinliği olduğunda
user_channel.on('message', function(data) {
    // data içinde mesaj içeriği bulunmaktadır
    console.log(data);
});
// user-1 kanalında friendApply etkinliği olduğunda
user_channel.on('friendApply', function (data) {
    // data içinde arkadaşlık başvurusuyla ilgili bilgiler bulunmaktadır
    console.log(data);
});

// Grup id'si 2 olarak varsayalım
var group_id = 2;
// Tarayıcı, group-2 kanalındaki mesajları dinler, yani grup 2'nin grup mesajlarını dinler
var group_channel = connection.subscribe('group-' + group_id);
// group-2'nin message etkinliği olduğunda
group_channel.on('message', function(data) {
    // data içinde mesaj içeriği bulunmaktadır
    console.log(data);
});
```

> **İpuçları**
> Yukarıdaki örnekte subscribe, kanal aboneliği sağlar, `message` ve `friendApply` kanal üzerindeki etkinliklerdir. Kanallar ve etkinlikler, önceden yapılandırılmasını gerektirmez ve herhangi bir dizedir.

## Sunucu İtmesi (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // webman altında yapılandırmayı doğrudan alabilirsiniz, webman ortamı dışında ilgili yapılandırmayı elle eklemeniz gerekir
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// user-1'e abone olan tüm istemcilere message etkinliği için mesaj gönderme
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Merhaba, bu mesaj içeriğidir'
]);
```

## Özel Kanallar
Yukarıdaki örneklerde herhangi bir kullanıcı Push.js aracılığıyla bilgilere abone olabilir, eğer bilgiler hassas bilgiler ise bu şekilde güvenli değildir.

`webman/push`, özel kanal aboneliğini destekler. Özel kanallar, `private-` ile başlayan kanallardır. Örneğin
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket adresi
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // Abonelik yetkilendirme (yalnızca özel kanallar için)
});

// Kullanıcı uid'si 1 olarak varsayalım
var uid = 1;
// Tarayıcı, private-user-1 özel kanalındaki mesajları dinler
var user_channel = connection.subscribe('private-user-' + uid);
```

İstemcinin özel bir kanala abone olduğunda (`private-` ile başlayan kanal) tarayıcı, bir ajax yetkilendirme isteği başlatır (ajax adresi, yeni Push oluşturulurken yapılandırılan auth parametresidir). Geliştirici burada, geçerli kullanıcının bu kanalı dinleme yetkisi olup olmadığını kontrol edebilir. Bu şekilde aboneliğin güvenliği sağlanır.

> Yetkilendirme hakkında bilgi için `config/plugin/webman/push/route.php` içindeki kodlara bakabilirsiniz.

## İstemci İtmesi
Yukarıdaki örneklerin hepsi istemcinin belirli bir kanala abone olmasını içerir, sunucunun API'ını çağırmasını içerir. webman/push ayrıca istemcinin doğrudan mesaj göndermesini de destekler.

> **Not**
> İstemci tarafından yapılan itme yalnızca özel kanalları (`private-` ile başlayan kanalları) destekler ve istemci yalnızca `client-` ile başlayan etkinlikleri tetikleyebilir.

İstemcinin etkinlik itmesi örneği
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"merhaba"});
```

> **Not**
> Yukarıdaki kodlar, `private-user-1` kanalına abone olan tüm (şu anda etkin olan istemciler hariç) istemcilere `client-message` etkinliği verilerini gönderir (gönderen istemci kendi gönderdiği veriyi almayacaktır).

## Webhooks

Webhook, kanalın bazı etkinliklerini almak için kullanılır.

**Şu anda başlıca 2 etkinlik tipi vardır:**

- 1、channel_added
  Hiçbir istemcinin çevrimiçi olmadığından bir istemci çevrimiçi olduğunda veya çevrimdışı/yeniden çevrimiçi etkinliği olarak tetiklenen bir olay

- 2、channel_removed
  Bir kanalın tüm istemcileri çevrimdışı olduğunda tetiklenen bir olay veya çevrimdışı/yeni çevrimiçi etkinliği

> **İpuçları**
> Bu etkinlikler, kullanıcıların çevrimiçi durumunu sürdürmek için oldukça faydalıdır.

> **Not**
> Webhook adresi `config/plugin/webman/push/app.php` içinde yapılandırılır.
> Webhook etkinliklerini almak ve işlemek için kodlar `config/plugin/webman/push/route.php` içinde bulunmaktadır.
> Sayfanın yenilenmesi nedeniyle kullanıcı kısa süreli olarak çevrimdışı olduğunda, bu aslında bir çevrimdışı durum olarak kabul edilmemelidir, webman/push gecikmeli bir değerlendirme yapacak şekilde yapılandırılmıştır, bu nedenle çevrimiçi/çevrim dışı etkinliklerde 1-3 saniye gecikme olabilir.
## wss proxy (SSL)
https üzerinden ws bağlantısı kullanılamaz, bunun yerine wss bağlantısı kullanılmalıdır. Bu durumda nginx üzerinden wss proxy kullanılabilir, aşağıdaki gibi yapılandırılabilir:

```nginx
server {
    # .... Diğer yapılandırmalar burada gösterilmemiştir ...

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
**Not: Yukarıdaki yapılandırmadaki `<app_key>`, `config/plugin/webman/push/app.php` dosyasından alınır.**

Nginx yeniden başlatıldıktan sonra sunucuya aşağıdaki şekilde bağlanabilir:
```javascript
var connection = new Push({
    url: 'wss://ornek.com',
    app_key: '<app_key, config/plugin/webman/push/app.php içinden alınır>',
    auth: '/plugin/webman/push/auth' // Sadece özel kanallar için abonelik doğrulaması
});
```
> **Not**
> 1. İstek adresi wss ile başlamalıdır
> 2. Port belirtilmemelidir
> 3. **ssl sertifikasına ait olan alan adı** kullanılmalıdır

## push-vue.js Kullanımı

1. push-vue.js dosyasını projenin kök dizinine kopyalayın, örneğin: src/utils/push-vue.js

2. Vue sayfasında içeri aktarın:
```js
<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('Component mounted') 

  // Webman-push örneğini oluştur

  // Bağlantıyı kur
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket adresi
    app_key: '<app_key, config/plugin/webman/push/app.php içinden alınır>',
    auth: '/plugin/webman/push/auth' // Sadece özel kanallar için abonelik doğrulaması
  });

  // Varsayılan olarak kullanıcı uid'si 1 olsun
  var uid = 1;
  // Tarayıcı user-1 kanalındaki mesajları dinler, yani kullanıcı uid'si 1 olan kullanıcıların mesajlarını dinler
  var user_channel = connection.subscribe('user-' + uid);

  // user-1 kanalında message olayı gerçekleştiğinde
  user_channel.on('message', function (data) {
    // veri mesaj içeriğini içerir
    console.log(data);
  });
  // user-1 kanalında friendApply olayı gerçekleştiğinde
  user_channel.on('friendApply', function (data) {
    // veri arkadaşlık başvurusuyla ilgili bilgileri içerir
    console.log(data);
  });

  // Varsayalım ki grup kimliği 2 olsun
  var group_id = 2;
  // Tarayıcı group-2 kanalındaki mesajları dinler, yani grup 2'nin grup mesajlarını dinler
  var group_channel = connection.subscribe('group-' + group_id);
  // group-2 kanalında message olayı gerçekleştiğinde
  group_channel.on('message', function (data) {
    // veri mesaj içeriğini içerir
    console.log(data);
  });


})

</script>
```

## Diğer istemci adresleri
`webman/push`, pusher ile uyumludur, diğer diller (Java Swift .NET Objective-C Unity Flutter Android  IOS AngularJS vb.) istemci adresi indirme bağlantısı: https://pusher.com/docs/channels/channels_libraries/libraries/
