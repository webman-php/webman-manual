## वेबमैन/पुश

`वेबमैन/पुश` एक मुफ्त पुश सर्वर प्लगइन है, जिसमें क्लाइंट साइड सब्सक्रिप्शन मोड पर आधारित है, [पुशर](https://pusher.com) के साथ संगत है, जिसमें JS, Android (Java), IOS (Swift), IOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS जैसे कई क्लाइंट हैं। बैकएंड पुश SDK PHP, Node, Ruby, Asp, Java, Python, Go, Swift का समर्थन करता है। क्लाइंट के साथ हृदय और डिस्कनेक्ट स्वचालित पुनः संयोजन होता है, जिसका उपयोग करना बहुत ही सरल और स्थिर है। संदेश भेजने, चैट आदि के अनेक संवेदनशील संदेश।

प्लगइन में `push.js` नाम का एक वेब पेज JS क्लाइंट और `uniapp-push.js` नाम का uniapp क्लाइंट साथ में दिया गया है, अन्य भाषा के क्लाइंट को नीचे दिए गए लिंक से डाउनलोड किया जा सकता है।

> प्लगइन की आवश्यकता है `webman-framework>=1.2.0`

## स्थापना

```sh
कंपोजर द्वारा इंस्टॉल करें
composer require webman / push
```

## क्लाइंट (जावास्क्रिप्ट)

**जावास्क्रिप्ट क्लाइंट डालें**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**क्लाइंट उपयोग (सार्वजनिक चैनल)**
```js
// कनेक्शन स्थापित करें
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // वेबसॉकेट पता
    app_key: '<app_key, config/plugin/webman/push/app.php में मिलेगा>',
    auth: '/plugin/webman/push/auth' // सब्स्क्रिप्शन प्रमाणीकरण (केवल निजी चैनल के लिए)
});
// मान लें कि उपयोगकर्ता uid 1 है
var uid = 1;
// ब्राउज़र द्वारा user-1 चैनल के संदेशों की सुनवाई
var user_channel = connection.subscribe('user-' + uid);

// जब user-1 चैनल पर संदेश घटना होती है
user_channel.on('message', function(data) {
    // डेटा में संदेश सामग्री होती है
    console.log(data);
});
// जब user-1 चैनल पर friendApply घटना होती है
user_channel.on('friendApply', function (data) {
    // डेटा में मित्र आवेदन संबंधित जानकारी होती है
    console.log(data);
});

// मान लें कि समूह आईडी 2 है
var group_id = 2;
// ब्राउज़र द्वारा ग्रुप-2 चैनल के संदेशों की सुनवाई
var group_channel = connection.subscribe('group-' + group_id);
// जब ग्रुप-2 पर संदेश घटना होती है
group_channel.on('message', function(data) {
    // डेटा में संदेश सामग्री होती है
    console.log(data);
});
```

> **सुझाव**
> सब्स्क्रिप्शन के लिए subscribe उदाहरण में, `message` और `friendApply` चैनल पर घटना है। चैनल और इवेंट कोई भी स्ट्रिंग हैं, सर्वर पहले से कॉन्फ़िगर नहीं करना चाहिए।

## सर्वर संदेश भेजना (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // सीधा webman से कॉन्फ़िगुरेशन प्राप्त करने के लिए config का प्रयोग कर सकते हैं, गैर-webman परिवेश में संबंधित कॉन्फ़िगरेशन को मैन्युअल रूप से लिखना होगा
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// जो user-1 केवल क्लाइंट को संदेश message इवेंट को भेजता है
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'नमस्ते, यह संदेश सामग्री है'
]);
```

## निजी चैनल
उपरोक्त उदाहरण में किसी भी उपयोगकर्ता ने पुश.js के माध्यम से सूचनाएँ सब्सक्राइब कर सकती हैं, यदि सूचना संवेदनशील होती है, तो यह असुरक्षित होता है।

`वेबमैन/पुश` निजी चैनल सब्सक्राइब का समर्थन करता है, निजी चैनल `private-` से शुरू होते हैं। उदाहरण के लिए
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // वेबसॉकेट पता
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // सब्सक्रिप्शन प्रमाणीकरण (केवल निजी चैनल के लिए)
});

// मान लें कि उपयोगकर्ता uid 1 है
var uid = 1;
// ब्राउज़र द्वारा private-user-1 निजी चैनल के संदेशों की सुनवाई
var user_channel = connection.subscribe('private-user-' + uid);
```

जब क्लाइंट निजी चैनल (`private-` शुरू वाले चैनल) की सब्स्क्राइब करता है, ब्राउज़र एक AJAX प्रमाणीकरण अनुरोध करता है (AJAX पता new Push का प्रमाणीकरण पैरामीटर में सेट किया गया है), जहां डेवलपर यह जांच सकता है, कि क्या वर्तमान उपयोगकर्ता को इस चैनल की सुनवाई की अनुमति है या नहीं। इससे यह सुनिश्चित होता है कि सब्स्क्राइब का सुरक्षित होता है।

> प्रमाणीकरण के बारे में जानने के लिए `config/plugin/webman/push/route.php` कोड देखें

## क्लाइंट से पुश करना
ऊपर के उदाहरण सभी क्लाइंट साइड पर किसी भी चैनल की सब्स्क्राइब करने की बात करते हैं, सर्वर API इंटरफ़ेस को कॉल करते हैं। webman/push भी क्लाइंट साइड से सीधे संदेश पुश करने का समर्थन करता है।

> **ध्यान दें**
> क्लाइंट से स्थानीय पुश केवल निजी चैनल (`private-` से शुरू होने वाले चैनल) को समर्थन करता है, और क्लाइंट केवल `client-` से शुरू होने वाली घटनाएँ को ट्रिगर कर सकते हैं।

क्लाइंट द्वारा घटना ट्रिगर करने का उदाहरण
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **ध्यान दें**
> ऊपर का कोड सभी (वर्तमान क्लाइंट को छोड़कर) उस सभी क्लाइंट को, जो `private-user-1` को सब्स्क्राइब कर चुके हैं, `client-message` घटना का डेटा पुश करता है (पुश करने वाला क्लाइंट अपने पुश किए गए डेटा को प्राप्त नहीं करेगा)।
## वेबहुक

वेबहुक कोई चैनल इवेंट को प्राप्त करने के लिए उपयोग किया जाता है।

**वर्तमान में मुख्य रूप से 2 घटनाएं हैं:**

- 1. channel_added
   किसी चैनल पर कोई भी ग्राहक ऑनलाइन नहीं होने से ऑनलाइन हो जाने पर होने वाली घटना, या यह कहने के लिए कि ऑनलाइन घटना है।

- 2. channel_removed
   किसी चैनल पर सभी ग्राहक ऑफलाइन हो जाने पर होने वाली घटना, या यह कहने के लिए कि ऑफलाइन घटना है।

> **टिप्स**
> इन इवेंट्स को उपयोगकर्ता की ऑनलाइन स्थिति को बनाए रखने के लिए बहुत उपयोगी होते है।

> **ध्यान दें**
> webhook पता `config/plugin/webman/push/app.php` में कॉन्फ़िगर किया गया है।
> webhook इवेंट को प्राप्त और प्रसंस्करण करने के लिए कोड की जानकारी `config/plugin/webman/push/route.php` में दी गई है।
> पृष्ठ को रिफ्रेश होने से उपयोगकर्ता को अस्थायी रूप से ऑफलाइन होना चाहिए, webman/push देर से निर्णय करेगा, इसलिए ऑनलाइन/ऑफलाइन इवेंट में 1-3 सेकंड की देरी हो सकती है।

## WSS प्रॉक्सी (SSL)
HTTPs के तहत वेबसॉकेट कनेक्शन का उपयोग नहीं किया जा सकता है, इसके लिए WSS कनेक्शन का उपयोग करना चाहिए। इस स्थिति में Nginx का उपयोग WSS को प्रॉक्सी करने के लिए किया जा सकता है, निम्नलिखित तरह से कॉन्फ़िगर किया जा सकता है:

```nginx
server {
    # .... यहाँ अन्य कॉन्फ़िगरेशन छोड़ दी गई है ...
    
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
**ध्यान दें `<app_key>` कॉन्फ़िगरेशन में `config/plugin/webman/push/app.php` से प्राप्त किया गया है।**

Nginx को पुनः आरंभ करने के बाद, निम्नलिखित तरीके से सर्वर से कनेक्ट करें:
```javascript
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, config/plugin/webman/push/app.php में मिलेगा>',
    auth: '/plugin/webman/push/auth' // सदस्यता प्रमाणीकरण (केवल निजी चैनलों के लिए)
});
```
> **ध्यान दें**
> 1. अनुरोध पता wss से शुरू होता है
> 2. पोर्ट को नहीं जोड़ें
> 3. **ssl प्रमाणपत्र से संबंधित डोमेन** कनेक्ट करना आवश्यक है

## push-vue.js उपयोग निर्देश

1. फ़ाइल push-vue.js को प्रोजेक्ट डायरेक्टरी में कॉपी करें, जैसे: src/utils/push-vue.js

2. vue पेज में इसे शामिल करें
```js

<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('कंपोनेंट लोड हो चुका है') 

  // webman-push का उदाहरण बनाएं

  // कनेक्शन स्थापित करें
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // वेबसॉकेट पता
    app_key: '<app_key, config/plugin/webman/push/app.php में मिलेगा>',
    auth: '/plugin/webman/push/auth' // सदस्यता प्रमाणीकरण (केवल निजी चैनलों के लिए)
  });

  // मान लें उपयोक्ता uid 1 है
  var uid = 1;
  // ब्राउज़र उपयोक्ता-1 चैनल के संदेशों का निरीक्षण करती है, यानी uid 1 वाले उपयोक्ता के संदेश
  var user_channel = connection.subscribe('user-' + uid);

  // जब user-1 चैनल पर message इवेंट होता है
  user_channel.on('message', function (data) {
    // डेटा में संदेश सामग्री होती है
    console.log(data);
  });
  // जब user-1 चैनल पर friendApply इवेंट होता है
  user_channel.on('friendApply', function (data) {
    // डेटा में मित्र आवेदन संबंधित जानकारी होती है
    console.log(data);
  });

  // मान लें समूह आईडी 2 है
  var group_id = 2;
  // ब्राउज़र समूह-2 चैनल के संदेशों का निरीक्षण करती है, यानी समूह 2 के समूह संदेश का निरीक्षण करती है
  var group_channel = connection.subscribe('group-' + group_id);
  // जब समूह 2 पर message संदेश इवेंट होता है
  group_channel.on('message', function (data) {
    // डेटा में संदेश सामग्री होती है
    console.log(data);
  });


})

</script>
```

## अन्य क्लाइंट पते
`webman/push` pusher से संगत है, अन्य भाषाएँ (जावा, स्विफ्ट, .नेट, ऑब्जेक्टिव-सी, यूनिटी, फ्लटर, एंड्रॉइड, आईओएस, एंगुलरजेएस आदि) के ग्राहकों के पते यहाँ से डाउनलोड करें:
https://pusher.com/docs/channels/channels_libraries/libraries/
