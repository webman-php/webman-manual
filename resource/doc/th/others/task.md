# การประมวลธุรกิจที่ช้า

บางครั้งเราจำเป็นต้องประมวลธุรกิจที่ช้า โดยเพื่อหลีกเลี่ยงการทำให้ธุรกิจที่ช้าส่งผลกระทบต่อการประมวลในขณะที่ข้อมูล ธุรกิจเหล่านี้อาจใช้วิธีการประมวลที่แตกต่างกันตามสถานการณ์

## ใช้คิวข้อความ
ดูที่[คิว Redis](https://www.workerman.net/plugin/12) [คิว Stomp](https://www.workerman.net/plugin/13)

### ข้อดี
สามารถจัดการกับคำขอประมวลธุรกิจประดุจที่ฉับไว

### ข้อเสีย
ไม่สามารถส่งผลลัพธ์กลับโดยตรงให้กับไคลเอนต์ หากต้องการส่งผลลัพธ์จะต้องร่วมมือกับบริการอื่น เช่น [webman/push](https://www.workerman.net/plugin/2) เพื่อส่งผลลัพธ์การประมวลธุรกิจ

## เพิ่มพอร์ต HTTP ใหม่

> **หมายเหตุ**
> คุณลักษณะนี้จำเป็นต้องมี webman-framework>=1.4

เพิ่มพอร์ต HTTP เพื่อประมวลธุรกิจที่ช้า ธุรกิจเหล่านี้จะเข้าถึงกลุ่มโปรเซสที่เฉพาะเจาะจงเพื่อถูกประมวลแล้วจะส่งผลลัพธ์กลับโดยตรงให้กับไคลเอนต์

### ข้อดี
สามารถส่งข้อมูลกลับโดยตรงให้กับไคลเอนต์

### ข้อเสีย
ไม่สามารถจัดการกับคำขอที่เจอประสิทธิภาพสูงได้

### ขั้นตอนการดำเนินการ
เพิ่มการกำหนดค่าใน `config/process.php` ดังนี้
```php
return [
    // ... ข้างต้นนี้คือการกำหนดค่าอื่น ๆ ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // จำนวนโปรเซส
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // การกำหนดคลาส request
            'logger' => \support\Log::channel('default'), // อินสแตนซ์ของรายการบันทึก
            'app_path' => app_path(), // ตำแหน่งไดเรกทอรีแอพ
            'public_path' => public_path() // ตำแหน่งไดเรกทอรี public
        ]
    ]
];
```

ด้วยการนี้ ธุรกิจที่ช้าสามารถทำงานผ่านกลุ่มโปรเซส `http://127.0.0.1:8686/` โดยไม่กระทบถึงธุรกิจของกลุ่มโปรเซสอื่น ๆ

เพื่อให้ไคลเอนต์ไม่รู้สึกถึงความแตกต่างของพอร์ต คุณสามารถเพิ่มพร็อกซีที่ต่อกับพอร์ต 8686 ใน nginx โดยสมมติว่าเรียกร้องของธุรกิจช้าจะขึ้นต้นด้วย `/tast` การกำหนดค่าทั้งหมดของ nginx จะเป็นดังนี้:
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

  # ของธุรกิจที่เริ่มต้นด้วย /tast จะใช้พอร์ต 8686 โปรดแก้ไขตามความเป็นจริงตามต้องการ
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # ของธุรกิจอื่น ๆ จะใช้พอร์ต 8787 เหมือนเดิม
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

ด้วยการนี้ เมื่อไคลเอนต์เข้าถึง `domain.com/tast/xxx` จะไปทำงานผ่านพอร์ต 8686 โดยไม่กระทบกับการประมวลที่เข้าถึงด้วยพอร์ต 8787
