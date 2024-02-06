# Xử lý công việc chậm

Đôi khi chúng ta cần xử lý các công việc chậm, để tránh ảnh hưởng đến việc xử lý yêu cầu khác của webman, những công việc này có thể sử dụng các phương pháp xử lý khác nhau tùy thuộc vào tình huống cụ thể.

## Sử dụng hàng đợi tin nhắn
Xem thêm [Redis Queue](https://www.workerman.net/plugin/12) [STOMP Queue](https://www.workerman.net/plugin/13)

### Điểm mạnh
Có thể đối phó với yêu cầu xử lý công việc lớn đột ngột

### Điểm yếu
Không thể trực tiếp trả kết quả cho máy khách. Nếu cần thông báo kết quả, cần phối hợp với các dịch vụ khác, ví dụ sử dụng [webman/push](https://www.workerman.net/plugin/2) để đẩy kết quả xử lý.

## Thêm cổng HTTP mới

> **Lưu ý**
> Tính năng này yêu cầu webman-framework>=1.4

Thêm cổng HTTP để xử lý yêu cầu chậm, những yêu cầu chậm này sẽ được xử lý bởi một nhóm quá trình cụ thể thông qua việc truy cập cổng này và sau đó trả kết quả trực tiếp cho máy khách.

### Điểm mạnh
Có thể trực tiếp trả dữ liệu cho máy khách

### Điểm yếu
Không thể đối phó với yêu cầu lớn đột ngột

### Bước thực hiện
Thêm cấu hình sau vào tệp `config/process.php`.
```php
return [
    // ... Phần cấu hình khác ở đây ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Số quá trình
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Cài đặt lớp yêu cầu
            'logger' => \support\Log::channel('default'), // Thực thể ghi nhật ký
            'app_path' => app_path(), // Vị trí thư mục ứng dụng
            'public_path' => public_path() // Vị trí thư mục public
        ]
    ]
];
```

Như vậy, giao diện chậm có thể đi qua nhóm quá trình tại `http://127.0.0.1:8686/`, không ảnh hưởng đến việc xử lý công việc của các nhóm quá trình khác.

Để không làm ảnh hưởng tới trình duyệt, bạn có thể thêm một trình xử lý ủy quyền tới cổng 8686 trong cấu hình nginx. Giả sử các yêu cầu giao diện chậm đều bắt đầu bằng `/tast`, cấu hình nginx sẽ tương tự như sau:
```
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

  # Yêu cầu bắt đầu bằng /tast sẽ đi qua cổng 8686, hãy thay đổi /tast thành tiền tố thực tế của bạn
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Những yêu cầu khác đi qua cổng 8787 ban đầu
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

Như vậy, khi khách hàng truy cập `tên miền.com/tast/xxx` sẽ được xử lý qua cổng 8686 mà không ảnh hưởng đến việc xử lý yêu cầu tại cổng 8787.
