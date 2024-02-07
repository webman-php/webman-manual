# โครงสร้างไดเรกทอรี

``` 
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

เราสามารถเห็นไดเรกทอรีปลั๊กอินที่มีโครงสร้างและไฟล์กำหนดค่าเหมือนกับ webman ซึ่งจริง ๆ แล้วประสบการณ์การพัฒนากับปลั๊กอินแทบไม่มีความแตกต่างกับการพัฒนาแอปพลิเคชั่นที่ปกติของ webman
การตั้งชื่อและโครงสร้างไดเรกทอรีของปลั๊กอินเหมือนกับข้อกำหนดของ PSR4 โดยที่ชื่อของโครงสร้างพร้อมด้วย plugin เช่น `plugin\foo\app\controller\UserController` 

## เกี่ยวกับไดเรกทอรี api
แต่ละปลั๊กอินมีไดเรกทอรี api ถ้าแอปพลิเคชั่นของคุณมีการให้บริการอินเทอร์เฟซภายในให้แอปพลิเคชั่นอื่นเรียกใช้ คุณจำเป็นต้องนำอินเทอร์เฟซไปวางไว้ในไดเรกทอรี api
โปรดทราบว่าที่นี่ที่กล่าวถึงอินเทอร์เฟซ คือ อินเทอร์เฟซของการเรียกใช้ฟังก์ชัน ไม่ใช่การเรียกใช้ทางเครือข่าย
ตัวอย่างเช่น `ปลั๊กอินเมล` อยู่ใน `plugin/email/api/Email.php` มีการให้บริการอินเทอร์เฟซ `Email::send()` ให้แอปพลิเคชั่นอื่นเรียกใช้สำหรับส่งอีเมล
นอกจากนี้ `plugin/email/api/Install.php` ถูกสร้างโดยอัตโนมัติเพื่อให้ตลาดปลั๊กอิน webman-admin เรียกใช้การติดตั้งหรือถอดการติดตั้งได้
