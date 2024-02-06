# Proxy của nginx
Khi webman cần cung cấp truy cập từ bên ngoài trực tiếp, nên thêm một proxy nginx trước webman để có những lợi ích sau.

- Tài nguyên tĩnh được xử lý bởi nginx, giúp webman tập trung vào xử lý logic kinh doanh
- Cho phép nhiều webman chia sẻ cổng 80 và 443, phân biệt qua tên miền khác nhau, thực hiện triển khai nhiều trang web trên một máy chủ
- Có thể thực hiện php-fpm và cấu trúc webman chung sống cùng nhau
- Proxy của nginx thực hiện ssl để triển khai https, đơn giản và hiệu quả hơn
- Có thể lọc chặt chẽ một số yêu cầu không hợp lệ từ mạng bên ngoài

## Ví dụ proxy của nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name tên_miền_trang_web;
  listen 80;
  access_log off;
  root /your/webman/public;

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

Thường thì, những cấu hình trên, người phát triển chỉ cần cấu hình server_name và root thành giá trị thực tế, không cần thiết lập các trường khác.
