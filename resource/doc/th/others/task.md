การดำเนินงานช้า

บางครั้งเราต้องดำเนินการธุรกิจช้าเพื่อหลีกเลี่ยงผลกระทบต่อการดำเนินการของ webman ที่อยู่ในสถานการณ์ที่แตกต่างกันต้องใช้วิธีการดำเนินการที่แตกต่างกัน

การใช้คิวข้อความ
อ้างอิง [คิว redis](../queue/redis.md) [คิว stomp](../queue/stomp.md)

ข้อดี
สามารถรับมือกับการร้อยของธุรกิจอย่างรวดเร็ว

ข้อเสีย
ไม่สามารถส่งผลลัพธ์โดยตรงต่อลูกค้า หากต้องการส่งผลลัพธ์จำเป็นต้องใช้บริการอื่น ๆ เช่น [webman/push](https://www.workerman.net/plugin/2) เพื่อส่งผลการดำเนินงาน

การเพิ่มพอร์ต HTTP ใหม่

>  **โปรดทราบ**
>  คุณลักษณะนี้ต้องการ webman-framework>=1.4

เพิ่มการจัดการการร้องขอช้าผ่านพอร์ต HTTP นี้ คำขอช้าเหล่านี้จะเข้าสู่กระบวนการรับผิดชอบของกลุ่มพิเศษและจะคืนผลลัพธ์โดยตรงให้กับลูกค้า

ข้อดี
สามารถส่งข้อมูลโดยตรงให้กับลูกค้า

ข้อเสีย
ไม่สามารถรับมือกับคำขอจากทะลุโปรดมาก

ขั้นตอนดำเนินการ
เพิ่มการกำหนดค่าต่อไปนี้ใน `config/process.php` 
```php
return [
    // ... ข้ามการกำหนดค่าอื่น ๆ ที่นี่ ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // จำนวนกระบวนการ
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // การกำหนดคลาสคำขอ
            'logger' => \support\Log::channel('default'), // ตัวอย่างการล็อก
            'app_path' => app_path(), // ตำแหน่งไดเรกทอรีแอป
            'public_path' => public_path() // ตำแหน่งไดเรกทอรี public
        ]
    ]
];
```

ดังนั้น อินเทอร์เฟซช้าสามารถทำงานผ่านกลุ่มนี้ `http://127.0.0.1:8686/` โดยไม่มีผลต่อกลุ่มการดำเนินงานอื่น ๆ

เพื่อให้ลูกค้าไม่รู้สึกถึงความแตกต่างของพอร์ต สามารถเพิ่มพร็อกซีไปยังพอร์ต 8686 ใน nginx ให้ประสบการณ์ของคุณ ตัวอย่างการกำหนดค่า nginx ทั้งหมดเหมือนด้านล่างนี้
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# เพิ่ม upstream 8686 ใหม่
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # คำขอที่เริ่มต้นด้วย /tast ไปที่พอร์ต 8686 โดยกรุณาแก้ไข /tast ตามอัตคัณของคุณ
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # คำขออื่น ๆ ไปที่พอร์ตเดิม 8787
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

ด้วยการนี้ การเข้าถึงของลูกค้าไปยัง `domain.com/tast/xxx` จะทำการจัดการทางพร้อมพิเศษ 8686 โดยไม่มีผลต่อการดำเนินงานของพอร์ต 8787 
