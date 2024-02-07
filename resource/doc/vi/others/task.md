# Xử lý công việc chậm

Đôi khi chúng ta cần xử lý các công việc chậm, để tránh ảnh hưởng đến việc xử lý các yêu cầu khác của webman, các công việc này có thể sử dụng các phương pháp xử lý khác nhau tùy thuộc vào tình hình cụ thể.

## Sử dụng hàng đợi tin nhắn
Xem thêm [hàng đợi redis](../queue/redis.md) [hàng đợi stomp](../queue/stomp.md)

### Ưu điểm
Có thể xử lý các yêu cầu xử lý công việc lớn đột ngột

### Nhược điểm
Không thể trả kết quả trực tiếp cho người dùng. Nếu cần thông báo kết quả, cần kết hợp với dịch vụ khác, ví dụ như sử dụng [webman/push](https://www.workerman.net/plugin/2) để đẩy kết quả xử lý.

## Thêm cổng HTTP mới

> **Lưu ý**
> Tính năng này yêu cầu webman-framework >= 1.4

Thêm cổng HTTP để xử lý các yêu cầu chậm, những yêu cầu này sẽ được xử lý bởi một nhóm quá trình cụ thể thông qua việc truy cập vào cổng này, sau đó sẽ trả kết quả trực tiếp cho người dùng.

### Ưu điểm
Có thể trực tiếp trả kết quả về cho người dùng

### Nhược điểm
Không thể gần gũi với các yêu cầu lớn đột ngột

### Bước thực hiện
Thêm cấu hình sau vào `config/process.php`.
```php
return [
    // ... Bỏ qua các cấu hình khác ở đây ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Số lượng quá trình
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Cài đặt lớp request
            'logger' => \support\Log::channel('default'), // Thực thể nhật ký
            'app_path' => app_path(), // Vị trí thư mục app
            'public_path' => public_path() // Vị trí thư mục public
        ]
    ]
];
```

Như vậy, các giao diện chậm có thể truy cập vào nhóm quá trình này thông qua `http://127.0.0.1:8686/`, không ảnh hưởng đến việc xử lý công việc của các quá trình khác.

Để người dùng không cảm nhận được sự khác biệt về cổng, bạn có thể thêm một đoạn mã proxy vào nginx để trỏ đến cổng 8686. Giả sử đường dẫn yêu cầu giao diện chậm đều bắt đầu bằng `/tast`, cấu hình nginx sẽ như sau:
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Thêm một upstream 8686 mới
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Các yêu cầu bắt đầu bằng /tast sẽ đi qua cổng 8686, vui lòng thay đổi /tast thành tiền tố thích hợp theo tình hình cụ thể
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Các yêu cầu khác sẽ đi qua cổng 8787 ban đầu
  location / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

Như vậy, khi người dùng truy cập `domain.com/tast/xxx` sẽ đi qua cổng 8686 riêng biệt để xử lý, không ảnh hưởng đến việc xử lý yêu cầu qua cổng 8787.
