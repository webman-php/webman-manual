# การแทนที่ด้วย Nginx
เมื่อ webman ต้องการให้ผู้ใช้สามารถเข้าถึงจากอินเทอร์เน็ตโดยตรง แนะนำให้เพิ่ม nginx ขึ้นหน้า webman เพื่อให้มีประโยชน์ดังนี้

- การจัดการทรัพยากรแบบสแตติกโดย nginx เพื่อให้ webman สามารถดูแลตัวจริง โดยให้เฉพาะการดำเนินการทางธุรกิจ
- ทำให้ webman สามารถใช้พอร์ต 80 และ 443 ร่วมกัน ผ่านการแยกแยะโดเมนเนมให้เกิดประสิทธิภาพในการใช้งานหลายเว็บไซต์ในเซิร์ฟเวอร์เดียว
- สามารถรวมการทำงานของ php-fpm และโครงสร้าง webman ได้
- การแทนที่ด้วย ssl ของ nginx ช่วยให้การใช้งานเป็นhttps มีความง่ายและมีประสิทธิภาพมากขึ้น
- สามารถกรองคำขอที่ผิดกฎหมายที่มาจากอินเตอร์เน็ตได้อย่างเข้มงวด

## ตัวอย่างการแทนที่ด้วย nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name ชื่อโดเมนโปรเจค;
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

โดยทั่วไปนักพัฒนาจำเป็นต้องกำหนด server_name และ root เป็นค่าที่เป็นความจริงเท่านั้น และไม่จำเป็นต้องกำหนดฟิลด์อื่น ๆ 
