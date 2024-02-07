## webman/push

`webman/push`는 무료 푸시 서버 플러그인으로, 클라이언트는 구독 모드를 기반으로하며 [pusher](https://pusher.com)와 호환되며 JS, 안드로이드(java), IOS(swift), IOS(Obj-C), uniapp, .NET, Unity, Flutter, AngularJS 등 다양한 클라이언트를 지원합니다. 백엔드 푸시 SDK는 PHP, Node, Ruby, Asp, Java, Python, Go, Swift 등을 지원합니다. 클라이언트는 자체적으로 하트비트 및 연결 끊김 자동 재연결을 가지고 있어 사용이 매우 간단하고 안정적입니다. 메시지 푸시, 채팅 등 다양한 실시간 통신 시나리오에 적합합니다.

플러그인에는 웹페이지 JS 클라이언트인 push.js와 uniapp 클라이언트 `uniapp-push.js`가 내장되어 있으며, 다른 언어 클라이언트는 https://pusher.com/docs/channels/channels_libraries/libraries/에서 다운로드할 수 있습니다.

> 플러그인은 webman-framework>=1.2.0를 필요로 합니다.

## 설치

```sh
composer require webman/push
```

## 클라이언트 (자바스크립트)

**자바스크립트 클라이언트 가져오기**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**클라이언트 사용(공용 채널)**
```js
// 연결 설정
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // 웹소켓 주소
    app_key: '<app_key, config/plugin/webman/push/app.php에서 얻을 수 있음>',
    auth: '/plugin/webman/push/auth' // 구독 권한(비공용 채널에만 해당)
});
// 사용자 uid가 1인 것으로 가정
var uid = 1;
// 브라우저는 user-1 채널의 메시지를 수신 대기하고 있음, 즉 사용자 uid가 1인 사용자의 메시지를 수신하는 것
var user_channel = connection.subscribe('user-' + uid);

// user-1 채널에 메시지 이벤트가 발생할 때
user_channel.on('message', function(data) {
    // data에는 메시지 내용이 들어 있음
    console.log(data);
});
// user-1 채널에 friendApply 이벤트가 발생할 때
user_channel.on('friendApply', function (data) {
    // data에는 친구 신청과 관련된 정보가 있음
    console.log(data);
});

// 그룹 id가 2인 것으로 가정
var group_id = 2;
// 브라우저는 group-2 채널의 메시지를 수신 대기하고 있음, 즉 그룹 2의 그룹 메시지를 수신하는 것
var group_channel = connection.subscribe('group-' + group_id);
// 그룹 2에 메시지 이벤트가 발생할 때
group_channel.on('message', function(data) {
    // data에는 메시지 내용이 들어 있음
    console.log(data);
});
```

> **팁**
> 위 예제에서 subscribe는 채널 구독을 수행하고, `message` `friendApply`는 채널에서 애플리케이션이 발생하는 이벤트입니다. 채널과 이벤트는 사전에 서버 측에서 사전 설정할 필요가 없는 임의의 문자열입니다.

## 서버 측 푸시(PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // webman 환경에서는 설정을 직접 가져올 수 있지만, webman이 아닌 환경에서는 관련 설정을 수동으로 작성해야 함
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// 구독한 모든 클라이언트에게 메시지 이벤트의 메시지를 푸시
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => '안녕하세요, 이것은 메시지 내용입니다.'
]);
```

## 비공용 채널
위의 예에서는 누구나 Push.js를 통해 정보를 구독할 수 있지만, 취약한 정보라면 보안에 문제가 됩니다.

`webman/push`는 비공용 채널을 지원하며, 비공용 채널은 `private-`로 시작합니다. 예를 들어
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // 웹소켓 주소
    app_key: '<app_key>', // 애플리케이션 키
    auth: '/plugin/webman/push/auth' // 구독 권한(비공용 채널에만 해당)
});

// 사용자 uid가 1인 것으로 가정
var uid = 1;
// 브라우저는 private-user-1 비공용 채널의 메시지를 수신 대기하고 있음
var user_channel = connection.subscribe('private-user-' + uid);
```

클라이언트가 비공용 채널(`private-`로 시작하는 채널)을 구독할 때, 브라우저는 ajax 인증 요청을 보내고(ajax 주소는 new Push를 통해 설정한 auth 매개변수 주소), 여기에서 현재 사용자가 이 채널을 구독할 권한이 있는지 확인할 수 있습니다. 이렇게 하면 구독의 보안성이 보장됩니다.

> 권한 부여에 관련된 내용은 `config/plugin/webman/push/route.php`의 코드를 참조하십시오

## 클라이언트 푸시
위의 예제들은 클라이언트가 특정 채널을 구독하고 서버가 API를 호출하여 메시지를 푸시하는 것입니다. webman/push는 클라이언트가 직접 메시지를 푸시하는 것도 지원합니다.

> **주의**
> 클라이언트 간 푸시는 비공용 채널(`private-`로 시작하는 채널)만 지원하며, 클라이언트는 `client-`로 시작하는 이벤트만 발생시킬 수 있습니다.

클라이언트가 이벤트를 트리거하여 메시지를 푸시하는 예제
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"안녕하세요"});
```

> **주의**
> 위 코드는 `private-user-1`을 구독한 모든(현재 클라이언트 제외) 클라이언트에게 `client-message` 이벤트 데이터를 푸시합니다(푸시한 클라이언트는 스스로 푸시한 데이터를 받지 않습니다).

## 웹훅

웹훅은 채널의 일부 이벤트를 수신하는 데 사용됩니다.

**현재 주요한 이벤트는 다음과 같습니다:**

- 1. channel_added
  클라이언트가 연결되지 않은 특정 채널에서 클라이언트가 온라인 상태로 변경될 때 발생하는 이벤트

- 2. channel_removed
  특정 채널의 모든 클라이언트가 오프라인 상태로 변경될 때 발생하는 이벤트

> **팁**
> 이러한 이벤트들은 사용자의 온라인 상태를 유지하는 데 매우 유용합니다.

> **주의**
> 웹훅 주소는 `config/plugin/webman/push/app.php`에서 구성됩니다.
> 웹훅 이벤트를 수신하고 처리하는 코드는 `config/plugin/webman/push/route.php`의 논리에서 참조할 수 있습니다.
> 페이지 새로고침으로 인한 사용자의 일시적인 오프라인은 오프라인으로 간주하면 안 되므로, webman/push는 지연 판별을 수행하므로 온라인 / 오프라인 이벤트에는 1-3 초의 지연이 있을 수 있습니다.

## wss 프록시(SSL)
https에서는 ws 연결을 사용할 수 없으며, wss 연결을 사용해야 합니다. 이 경우 nginx를 사용하여 wss를 프록시로 설정할 수 있습니다. 설정은 다음과 같습니다:
```
server {
    # ... 이하 설정은 생략됨 ...

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
**주의 설정에서의`<app_key>`는 `config/plugin/webman/push/app.php`에서 가져옵니다.**

nginx를 다시 시작한 후, 다음 방법으로 서버에 연결합니다.
```wss
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, config/plugin/webman/push/app.php에서 얻을 수 있음>',
    auth: '/plugin/webman/push/auth' // 구독 권한(비공용 채널에만 해당)
});
```

> **주의**
> 1. 요청 주소는 wss로 시작합니다.
> 2. 포트 번호를 작성하지 않습니다.
> 3. **SSL 인증서에 해당되는 도메인**을 사용해야 합니다.
## push-vue.js 사용 설명

1. 파일 push-vue.js를 프로젝트 디렉토리에 복사합니다. 예: src/utils/push-vue.js

2. Vue 페이지에서 다음과 같이 가져옵니다.

```js
<script lang="ts" setup>
import { onMounted } from 'vue';
import { Push } from '../utils/push-vue';

onMounted(() => {
  console.log('컴포넌트가 마운트되었습니다');

  // webman-push 인스턴스화

  // 연결 설정
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // 웹소켓 주소
    app_key: '<app_key, config/plugin/webman/push/app.php에서 얻음>',
    auth: '/plugin/webman/push/auth' // 구독 권한 부여(비공개 채널에만 해당)
  });

  // 가정: 사용자 uid가 1인 경우
  var uid = 1;
  // 브라우저가 사용자 uid가 1인 사용자 메시지를 듣는다
  var user_channel = connection.subscribe('user-' + uid);

  // user-1 채널에 message 이벤트 메시지가 있는 경우
  user_channel.on('message', function (data) {
    // 데이터에는 메시지 내용이 포함됩니다
    console.log(data);
  });
  // user-1 채널에 friendApply 이벤트 메시지가 있는 경우
  user_channel.on('friendApply', function (data) {
    // 데이터에는 친구 신청 관련 정보가 포함됩니다
    console.log(data);
  });

  // 가정: 그룹 id가 2인 경우
  var group_id = 2;
  // 브라우저가 그룹 2의 그룹 메시지를 듣는다
  var group_channel = connection.subscribe('group-' + group_id);
  // 그룹 2에 message 메시지 이벤트가 있는 경우
  group_channel.on('message', function (data) {
    // 데이터에는 메시지 내용이 포함됩니다
    console.log(data);
  });
})
</script>
```

## 기타 클라이언트 주소
`webman/push`은 pusher와 호환되며, 다른 언어(Java Swift .NET Objective-C Unity Flutter Android  IOS AngularJS 등)의 클라이언트 주소는 다음에서 다운로드할 수 있습니다:
https://pusher.com/docs/channels/channels_libraries/libraries/
