# Proxy của Nginx
Khi webman cần cung cấp truy cập từ bên ngoài trực tiếp, khuyến nghị thêm một proxy Nginx trước webman, điều này mang lại những lợi ích sau đây.

- Tài nguyên tĩnh được xử lý bởi Nginx, giúp webman tập trung vào xử lý logic kinh doanh.
- Cho phép nhiều webman chia sẻ cổng 80, 443, thông qua tên miền để phân biệt các trạm khác nhau, thực hiện triển khai nhiều trạm trên một máy chủ duy nhất
- Có thể thực hiện php-fpm và kiến trúc webman chung sống cùng nhau
- Proxy Nginx cho phép thực hiện SSL để thiết lập HTTPS, đơn giản và hiệu quả hơn
- Có thể chặt chẽ lọc một số yêu cầu không hợp lệ từ mạng bên ngoài

## Ví dụ về Proxy Nginx
``` 
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name tên_miền_trạm;
  listen 80;
  access_log off;
  root /đường/dẫn/của/bạn/webman/public;

  location ^~ / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $http_host;
      proxy_set_header X-Forwarded-Proto $scheme;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

Thường thì, các cấu hình trên chỉ cần cấu hình server_name và root thành giá trị thực tế, các trường khác không cần phải cấu hình.
