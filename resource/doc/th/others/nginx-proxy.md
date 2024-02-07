# การทำเป็น Nginx Proxy
เมื่อ webman ต้องการให้การเข้าถึงจากภายนอกโดยตรง คำแนะนำคือการเพิ่ม Nginx Proxy ไว้ด้านหน้าของ webman เพื่อให้มีประโยชน์ตามนี้

- การจัดการทรัพยากรแบบสถิตโดย Nginx เพื่อให้ webman มุ่งเน้นการจัดการและประมวลผลโลจิกธุรกิจ
- อนุญาตให้ webman หลายรายการใช้พอร์ต 80 และ 443 ผ่านการแยกตามโดเมน เพื่อภาพรวมการให้บริการหลายซิตส์บนเซิร์ฟเวอร์เดียว
- สามารถระบบการทำงานร่วมกันระหว่าง PHP-FPM และโครงสร้าง webman
- การทำงานของ Nginx Proxy SSL ทำให้การใช้งาน HTTPS ง่ายและมีประสิทธิภาพมากขึ้น
- สามารถกรองคำขอที่ไม่ถูกต้องจากภายนอกเอาออกได้

## ตัวอย่างการทำ Nginx Proxy
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}
server {
  server_name โดเมนเว็บไซต์;
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
โดยทั่วไปแล้ว นักพัฒนาจำเป็นต้องกำหนดค่า server_name และ root เป็นค่าจริง ๆ สิ่งอื่น ๆ ไม่จำเป็นต้องกำหนดค่า
