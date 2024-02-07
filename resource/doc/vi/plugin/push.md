## webman/push

`webman/push` là một plugin máy chủ push miễn phí, phía máy khách dựa trên mô hình đăng ký, tương thích với [pusher](https://pusher.com), hỗ trợ nhiều máy khách như JS, Android (java), IOS (swift), IOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS, v.v. SDK push phía máy chủ hỗ trợ PHP, Node, Ruby, Asp, Java, Python, Go, Swift, v.v. Máy khách tích hợp tự động gửi tin nhắn và tự động kết nối lại khi mất kết nối, rất dễ sử dụng và ổn định. Thích hợp cho việc gửi tin nhắn, trò chuyện và nhiều ứng dụng khác liên quan đến truyền thông trực tuyến.

Plugin này đi kèm với một máy khách js trên trang web `push.js` cũng như máy khách uniapp `uniapp-push.js`, các máy khách ngôn ngữ khác có thể tải xuống tại https://pusher.com/docs/channels/channels_libraries/libraries/

> Yêu cầu phải có webman-framework>=1.2.0

## Cài đặt

```sh
composer require webman/push
```

## Máy khách (javascript)

**Nhập máy khách javascript**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Sử dụng máy khách (kênh công cộng)**
```js
// Thực hiện kết nối
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // địa chỉ websocket
    app_key: '<app_key, lấy từ config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // xác thực đăng ký (chỉ áp dụng cho kênh riêng tư)
});
// Giả sử uid của người dùng là 1
var uid = 1;
// Trình duyệt lắng nghe tin nhắn trên kênh user-1, tức là tin nhắn của người dùng có uid là 1
var user_channel = connection.subscribe('user-' + uid);

// Khi kênh user-1 có thông báo sự kiện tin nhắn
user_channel.on('message', function(data) {
    // trong data là nội dung tin nhắn
    console.log(data);
});
// Khi kênh user-1 có thông báo sự kiện nhập tên
user_channel.on('friendApply', function (data) {
    // trong data là thông tin liên quan đến yêu cầu kết bạn
    console.log(data);
});

// Giả sử id nhóm là 2
var group_id = 2;
// Trình duyệt lắng nghe tin nhắn trên kênh group-2, tức là lắng nghe tin nhắn của nhóm 2
var group_channel = connection.subscribe('group-' + group_id);
// Khi nhóm 2 có sự kiện tin nhắn
group_channel.on('message', function(data) {
    // trong data là nội dung tin nhắn
    console.log(data);
});
```

> **Tips**
> Trong ví dụ trên, subscribe thực hiện việc đăng ký kênh, `message` `friendApply` là các sự kiện trên kênh. Kênh và sự kiện là chuỗi tùy ý, không cần phải cấu hình trước từ phía máy chủ.

## Phía máy chủ push (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // Trong webman có thể sử dụng config để lấy cấu hình, không phải webman thì cần nhập cấu hình tương ứng bằng tay
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Gửi tin nhắn sự kiện message cho tất cả người dùng đã đăng ký kênh user-1
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Xin chào, đây là nội dung tin nhắn'
]);
```

## Kênh riêng tư
Trong các ví dụ trước, bất kỳ người dùng nào cũng có thể đăng ký thông tin qua Push.js, nếu thông tin là thông tin nhạy cảm, điều này không an toàn.

`webman/push` hỗ trợ đăng ký kênh riêng tư, kênh riêng tư bắt đầu bằng `private-`. Ví dụ
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // địa chỉ websocket
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // xác thực đăng ký (chỉ áp dụng cho kênh riêng tư)
});

// Giả sử uid của người dùng là 1
var uid = 1;
// Trình duyệt lắng nghe tin nhắn trên kênh riêng tư private-user-1
var user_channel = connection.subscribe('private-user-' + uid);
```

Khi máy khách đăng ký kênh riêng tư (kênh có tiền tố là `private-`), trình duyệt sẽ gửi một yêu cầu xác thực ajax (địa chỉ ajax là cấu hình auth khi mới tạo Push) để xác định xem người dùng hiện tại có quyền để lắng nghe kênh này hay không. Điều này đảm bảo tính an toàn của việc đăng ký. 

> Về xác thực xem `config/plugin/webman/push/route.php` với mã logic
> Vì làm mới trang web có thể khiến người dùng tạm thời offline, không nên coi là offline, webman/push sẽ thực hiện xác định chậm, vì vậy sự kiện trực tuyến / ngoại tuyến sẽ có độ trễ từ 1-3 giây.

## Push từ máy khách
> **Chú ý**
> Push giữa các máy khách chỉ hỗ trợ kênh riêng tư (kênh bắt đầu bằng `private-`), và máy khách chỉ có thể kích hoạt sự kiện bắt đầu bằng `client-`.

Ví dụ về việc kích hoạt sự kiện tin nhắn từ máy khách
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {from_uid:2, content:"hello"});
```

> **Chú ý**
> Mã trên giúp đẩy dữ liệu sự kiện `client-message` tới tất cả các máy khách đã đăng ký kênh `private-user-1` (máy khách đẩy tin nhắn sẽ không nhận lại dữ liệu tin nhắn đã đẩy).

## webhooks

Webhook được sử dụng để nhận sự kiện từ kênh.

**Hiện tại có 2 sự kiện chính:**

- 1、channel_added
  Khi một kênh không có máy khách nào trực tuyến chuyển sang có máy khách trực tuyến, hoặc có thể hiểu là sự kiện trực tuyến

- 2、channel_removed
  Khi tất cả máy khách của một kênh đều offline, hoặc có thể hiểu là sự kiện ngoại tuyến

> **Tips**
> Những sự kiện này rất hữu ích trong việc duy trì trạng thái trực tuyến của người dùng.

> **Chú ý**
> Địa chỉ webhook được cấu hình trong `config/plugin/webman/push/app.php`.
> Xem mã xử lý sự kiện webhook tại `config/plugin/webman/push/route.php`.
> Do việc làm mới trang web có thể làm người dùng tạm thời offline, không nên coi là offline, webman/push sẽ thực hiện xác định chậm, vì vậy sự kiện trực tuyến / ngoại tuyến sẽ có độ trễ từ 1-3 giây.
## Đại lý wss (SSL)
Không thể sử dụng kết nối ws dưới giao thức https, cần sử dụng kết nối wss. Trong trường hợp này, bạn có thể sử dụng nginx để đại lý wss, cấu hình tương tự như sau:
````
server {
    # .... Phần cấu hình khác đã được lược bỏ ...

    location /app/<app_key>
    {
        proxy_pass http://127.0.0.1:3131;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header X-Real-IP $remote_addr;
    }
}
````
**Chú ý: Phần cấu hình trên, `<app_key>` được lấy từ `config/plugin/webman/push/app.php`**

Sau khi khởi động lại nginx, kết nối với máy chủ theo cách sau:
````
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, lấy từ config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Xác thực đăng ký (chỉ áp dụng cho kênh riêng tư)
});
````

> **Chú ý**
> 1.  Địa chỉ yêu cầu phải bắt đầu bằng wss
> 2.  Không cần ghi cổng
> 3.  Phải sử dụng kết nối với **tên miền ứng với chứng chỉ ssl**

## Hướng dẫn sử dụng push-vue.js

1.  Sao chép tệp push-vue.js vào thư mục dự án, ví dụ: src/utils/push-vue.js

2.  Nhúng trong trang của vue
```js
<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('Component đã được gắn kết') 

  // Khởi tạo webman-push

  // Thiết lập kết nối
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // địa chỉ websocket
    app_key: '<app_key, lấy từ config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Xác thực đăng ký (chỉ áp dụng cho kênh riêng tư)
  });

  // Giả sử uid của người dùng là 1
  var uid = 1;
  // Trình duyệt lắng nghe tin nhắn của kênh user-1, tức là tin nhắn của người dùng có uid là 1
  var user_channel = connection.subscribe('user-' + uid);

  // Khi có sự kiện tin nhắn trên kênh user-1
  user_channel.on('message', function (data) {
    // data chứa nội dung tin nhắn
    console.log(data);
  });
  // Khi có sự kiện friendApply trên kênh user-1
  user_channel.on('friendApply', function (data) {
    // data chứa thông tin liên quan đến yêu cầu kết bạn
    console.log(data);
  });

  // Giả sử id nhóm là 2
  var group_id = 2;
  // Trình duyệt lắng nghe tin nhắn của kênh group-2, tức là lắng nghe tin nhắn nhóm 2
  var group_channel = connection.subscribe('group-' + group_id);
  // Khi có sự kiện tin nhắn trên kênh group-2
  group_channel.on('message', function (data) {
    // data chứa nội dung tin nhắn
    console.log(data);
  });
})
</script>
```

## Địa chỉ khách hàng khác
`webman/push` tương thích với pusher, địa chỉ tải xuống các thư viện khác nhau (đa ngôn ngữ: Java Swift .NET Objective-C Unity Flutter Android IOS AngularJS, v.v.):
https://pusher.com/docs/channels/channels_libraries/libraries/
