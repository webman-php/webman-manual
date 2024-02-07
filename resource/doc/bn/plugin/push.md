## webman/push

`webman/push` হল একটি বিনামূল্যে পুশ সার্ভার প্লাগইন, যেখানে ক্লায়েন্ট সাপ্তাহিক মোডেল ভিত্তিতে প্রস্তুত, অনুযায়ী [pusher](https://pusher.com) সমন্বয় করে, যেতে JavaScript, Android (java), IOS (swift), IOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS ইত্যাদি এমন অনেকগুলি ক্লায়ান্ট রয়েছে। সার্ভার পুশ SDK সাপোর্ট জন্য PHP, Node, Ruby, Asp, Java, Python, Go, Swift ইত্যাদি। ক্লায়ান্টের জন্য সরাসরি প্রতি  হ্রদয় প্রতি হ্রদয়  সাক্ষাতকার যুক্তিসহক করা, অতি পছন্দনীয়। এটি বার্তা পরামর্শ, চ্যাটিং ইত্যাদির জন্য সাময়িক যোগাযোগ বিষয়ে।

প্লাগইনে একটি সাইট JavaScript ক্লায়েন্ট `push.js` এবং uniapp ক্লায়েন্ট `uniapp-push.js` সহজে থাকে, অন্যান্য ভাষার ক্লায়েন্টের জন্য https://pusher.com/docs/channels/channels_libraries/libraries/ থেকে ডাউনলোড করা যায়।

> প্লাগইনটি কিছু দ্বারা প্রয়োজনীয় হবে `webman-framework>=1.2.0`

## ইনস্টল করুন

```sh
composer require webman/push
```

## ক্লায়েন্ট (javascript)

**জাভাস্ক্রিপ্ট ক্লায়েন্ট ইম্পোর্ট করুন**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**ক্লায়ান্ট ব্যবহার (পাবলিক চ্যানেল)**
```js
// সংযোগ স্থাপন
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // ওয়েবসকোয়েট ঠিকানা
    app_key: '<app_key, যা config/plugin/webman/push/app.php থেকে প্রাপ্ত করা হবে>',
    auth: '/plugin/webman/push/auth' // সাবস্ক্রাইব অভিপ্রায় (গোপনীয় চ্যানেলের জন্য মেধারনী)
});
// মনে করুন ব্যবহারকারীর uid = 1
var uid = 1;
// ব্রাউজার user-1 চ্যানেলের বার্তা শুনছে, অর্থাৎ ব্যবহারকারী uid=1 এর বার্তা
var user_channel = connection.subscribe('user-' + uid);

// যখন user-1 চ্যানেলে message ইভেন্টের বার্তা থাকে
user_channel.on('message', function(data) {
    // ডেটা মধ্যে বার্তা আছে
    console.log(data);
});
// যখন user-1 চ্যানেলে বন্ধু আবেদন ইভেন্ট হয়
user_channel.on('friendApply', function (data) {
    // ডেটাতে বন্ধু আবেদন সম্পর্কিত তথ্য রয়েছে
    console.log(data);
});

// মনে করুন গ্রুপ আইডি=2
var group_id = 2;
// ব্রাউজার group-2 চ্যানেলের বার্তা শুনছে, অর্থাৎ গ্রুপ 2 এর গ্রুপ বার্তা
var group_channel = connection.subscribe('group-' + group_id);
// যখন গ্রুপ 2 এ message ইভেন্ট হয়
group_channel.on('message', function(data) {
    // ডেটা মধ্যে বার্তা আছে
    console.log(data);
});
```

> **কুইক টিপস**
> উপরের উদাহরণে সাবস্ক্রাইব ব্যবহার করে, `message` `friendApply` হল চ্যানেলের ইভেন্ট। চ্যানেল এবং ইভেন্ট যে কোনও স্ট্রিং, কোনো পূর্বে সার্ভার কনফিগার করা প্রয়োজন নেই।

## সার্ভার পুশ (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // ওয়েবম্যান এ config ব্যবহার করে সরাসরি কনফিগ করা যায়, অব-webman পরিবেশেই সাইনের জনয় সংক্রান্ত কনফিগ হাতে করতে হবে
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// সাবস্ক্রাইব user-1 এর সমস্ত ক্লায়লিয়েন্ট এ পুশ message ইভেন্টের বার্তা
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'হ্যালো, এটি বার্তার বিষয়'
]);
```

## ব্যক্তিগত চ্যানেল
উপরের উদাহরণে যে কোনও ব্যবহারকারী পুশ.js দ্বারা বার্তা সবলীল করতে পারে, যদি বার্তা গুরুত্বপূর্ণ তহব, এটি অসুরক্ষিত।

`webman/push` ব্যক্তিগত চ্যানেল অভিযান সমর্থন করে, যা `private-` দ্বারা শুরু হয়। উদাহরণস্বরূপ
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // ওয়েবসকোয়েট ঠিকানা
    app_key: '<app_key>', 
    auth: '/plugin/webman/push/auth' // সাবস্ক্রাইব অনুমতি (শুধুমাত্র ব্যক্তিগত চ্যানেল) 
});

// মনে করুন ব্যবহারকারী uid=1
var uid = 1;
// ব্রাউজার private-user-1 ব্যক্তিগত চ্যানেলের বার্তা শুনছে
var user_channel = connection.subscribe('private-user-' + uid);
```

যখন ক্লায়েন্ট ব্যবহার সাবস্ক্রাইব ব্যক্তিগত চ্যানেল (`private-` দ্বারা শুরু হওয়া)  করে, ব্রাউজারটি একটি ajax অনুমতি অনুরোধ প্রেরণ করে (ajax ঠিকানা হল নিউ পুশ যখন অনুমতি প্যারামিটার কনফিগার করা হয়), ডেভেলপারগ্রহেরা এখানে নিষ্ক্রিয় করতে পারেন, বর্তমানকারী ব্যবহারকারীর এই চ্যানেল শোনার অনুমতি আছে কিনা। এর জন্য সে দৃঢ়তা প্রদান করে।

> অনুমতি সম্পর্কে `config/plugin/webman/push/route.php` এর কো'ড দেখুন

## ক্লায়ান্ট পুশ
উপরের উদাহরণ সব ক্লায়েন্টের জন্য সাবস্ক্রাইব প্রেক্ষা করে, সার্ভার পুশ এপিআই এপিআই কল করার পরিপাটি। ওয়েবম্যান / পুশ সরাসরি মেসেজ পুশ সমর্থন করে।

> **লক্ষ্য করুন**
> ক্লায়েন্ট মধ্যে পুশ একইপ্রকার ব্যক্তিগত চ্যানেল (`private-` দ্বারা শুরু হওয়া) সমর্থন করে, এবং ক্লায়েন্ট কেবল `client-` দ্বারা শুরু সম্ভাব্য ঘটনা ট্রিগার করতে পারে।

ক্লায়েন্ট ঘটনা ট্রিগার করার উদাহরণ
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **লক্ষ্য করুন**
> উপরের কো'ড সব(বর্তমান ক্লায়েন্ট থাকা বাদ শুধুমাত্র পুশ এক্সি(ক্লায়েন্টের নিজেদের পুশ মেন্যুটা, কেবল ক্লায়েন্টের ক্লায়েন্টান্গচ পেতে পায়নি) `private-user-1` চ্যানেলে `client-message` ইভেন্টের তথ্য পুশ করে।
## ওয়েবহুক্স

ওয়েবহুক্স ইভেন্টগুলি গ্রহণ করতে ব্যবহৃত হয়।

**বর্তমানে প্রধানত 2টি ইভেন্ট রয়েছে:**

- 1. **channel_added**
   একটি চ্যানেলে কোনও ক্লায়েন্ট অফলাইন থেকে অনলাইন হয়ে এলে ঘটায় বা অনলাইন ইভেন্ট হয়ে।

- 2. **channel_removed**
   একটি চ্যানেল থেকে সমস্ত ক্লায়েন্ট অফলাইন হয়ে পড়লে ঘটায় বা অফলাইন ইভেন্ট হয়ে।

> **টিপস:** 
> এই ইভেন্টগুলি ব্যবহারকারীরা অনলাইন স্থিতি রক্ষণাবেক্ষণে ব্যাপারে খুব দরকার।

> **নোট:** 
> ওয়েবহুকপ ঠিকানা `config/plugin/webman/push/app.php` এ কনফিগার করা হয়। 
> ওয়েবহুক ইভেন্ট প্রক্রিয়া করতে কোডটি দেখুন`config/plugin/webman/push/route.php` এর লজিক অংশে। 
> পেজ রিফ্রেশ করে কোনও অনুপ্রয়োগের ফলে ব্যবহারকারী সংক্ষেপে অফলাইন হয় - এটা নিয়মিত অফলাইন হিসাবে গণ্য করা উচিত নয়, তাইযুক্তিগতভাবে ওয়েবম্যান/পাশ রি঵াইন্দিং জাজমেন্ট করার সাথে অনলাইন/অফলাইন ইভেন্টে 1-3 সেকেন্ডের বিলম্ব থাকার অংশ রয়েছে।

## ডবলিউএসএস প্রক্সি (এসএসএল)
এসএস কানেকশন সুরক্ষিত রাখার জন্য এস এস কানেকশনগুলিকে এসএস প্রক্সিতে প্রদর্শন করা হয়। এই রকম অবস্থায় Nginx প্রক্সি কনফিগারের জন্য নিম্নলিখিত অনুসারে কনফিগার করা যেতে পারে:

```nginx
server {
    # .... অন্যান্য কনফিগারের লক্ষ্যে ...
    
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
**নোট:** উপরের কনফিগারে 'অ্যাপলিকেশন কী' এর জন্য `<app_key>` `config/plugin/webman/push/app.php` থেকে পেয়ে যাওয়া হয়।

Nginx পুনরায় শুরু করার পরে নিম্নোক্ত পদ্ধতিতে সার্ভারে যোগাযোগ করুন।
```javascript
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, যা config/plugin/webman/push/app.php থেকে পেয়ে যাবে>',
    auth: '/plugin/webman/push/auth' // চেনাবিশ্বাস সাবধান (শুধুমাত্র ব্যক্তিগত চ্যানেলের জন্য)
});
```
> **নোট:** 
> 1. অনুরোধের ঠিকানা wss দিয়ে শুরু করবে
> 2. পোর্টকে লিখতে হবেনা
> 3. **SSL সার্টিফিকেটের ডোমেইনের** সাথে সংযোগ যাচাই করতে হবে

## push-vue.js ব্যবহার নির্দেশিকা

1. push-vue.js ফাইলটি প্রজেক্ট ডিরেক্টরি এ কপি করুন, যেমন: src/utils/push-vue.js

2. ভিউ পৃষ্ঠায় ইমপোর্ট করুন
```js
<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('কম্পোনেন্ট পর্দায় চলাকাল')

  // ওয়েবম্যান-পাশ এর নমুনা উদাহরণ

  // সংযোগ স্থাপন করুন
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // ওয়েবসকেট ঠিকানা
    app_key: '<app_key, যা config/plugin/webman/push/app.php থেকে পেয়ে যাবে>',
    auth: '/plugin/webman/push/auth' // চেনাবিশ্বাস সাবধান (শুধুমাত্র ব্যক্তিগত চ্যানেলের জন্য)
  });

  // ধারাবাহিক ব্যবহারকারী ইউআইডি 1
  var uid = 1;
  // ব্রাউজার যোগ করে user-1 চ্যানেলের ম্যাসেজ শুনুন - অর্থাৎ ব্যবহারকারীর uid 1 হতে ম্যাসেজ শুনুন
  var user_channel = connection.subscribe('user-' + uid);

  // যখন user-1 চ্যানেলে 'ম্যাসেজ' ইভেন্ট থাকে
  user_channel.on('message', function (data) {
    // ডাটা দিয়ে ম্যাসেজটি
    console.log(data);
  });
  // যখন user-1 চ্যানেলে 'বন্ধুবান্ধু অ্যাপ্লাই' ইভেন্ট থাকে তখন ম্যাসেজটি
  user_channel.on('friendApply', function (data) {
    // ডাটা দিয়ে বন্ধু আবেগের সম্পর্কিত তথ্য
    console.log(data);
  });

  // ধারাবাহিক গ্রুপ আইডি 2
  var group_id = 2;
  // গ্রুপ-2 চ্যানেলে 'ম্যাসেজ' ইভেন্ট শুনুন
  var group_channel = connection.subscribe('group-' + group_id);
  // গ্রুপ-2 তে 'ম্যাসেজ' ইভেন্ট থাকলে
  group_channel.on('message', function (data) {
    // ডাটা দিয়ে ম্যাসেজটি
    console.log(data);
  });
})

</script>
```

## অন্যান্য ক্লায়েন্ট ঠিকানা
`webman/push` পুশার সাথে সাজলামান করা অন্যান্য ল্যাংগুয়েজ (যেমন: জাভা, সুইফ্ট .নেট, অবজেক্টিভ-সি, ইউনিটি, ফ্লাটার, এ্যান্ড্রয়েড, আইওএস, এইঞ্জুলারজেএস ইত্যাদি) ক্লায়েন্টের ঠিকানা ডাউনলোড করার জন্য লিংক:
https://pusher.com/docs/channels/channels_libraries/libraries/
