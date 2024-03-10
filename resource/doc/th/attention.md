# ข้อมูลสำหรับโปรแกรมเมอร์

## ระบบปฏิบัติการ
webman รองรับการทำงานในระบบปฏิบัติการ Linux และ Windows ซึ่งแนะนำให้ใช้ระบบปฏิบัติการ Windows เฉพาะเพื่อการพัฒนาและการ debug เท่านั้น ในระบบปฏิบัติการ Windows ไม่สามารถรองรับการตั้งค่ากระบวนการหลายตัวและกระบวนการยามฝันดังนั้นขอแนะนำให้ใช้ระบบปฏิบัติการ Linux สำหรับการทำงานในโดเมนการทำงานจริง

## วิธีการเริ่มต้น
**ในระบบปฏิบัติการ Linux** ใช้คำสั่ง `php start.php start` (โหมด debug) หรือ `php start.php start -d` (โหมดกระบวนการยามฝัน) เพื่อเริ่มต้น
**ในระบบปฏิบัติการ Windows** ใช้ `windows.bat` หรือคำสั่ง `php windows.php` เพื่อเริ่มต้นและใช้ ctrl c เพื่อหยุด ระบบปฏิบัติการ Windows ไม่รองรับคำสั่งเช่น stop reload status reload connections 

## การจำคอย
webman เป็นโครงสร้างที่จำคอยอยู่ตลอดเวลา โดยทั่วไปแล้ว เมื่อไฟล์ PHP ถูกโหลดเข้าสู่หน่วยความจำแล้วจะถูกใช้ซ้ำ และจะไม่ถูกโหลดจากฮาร์ดได้อีก (ยกเว้นไฟล์เทมเพลต) ดังนั้น การเปลี่ยนแปลงโค้ดธุรกิจหรือการตั้งค่าในโดเมนการทำงานจริงจำเป็นต้องทำ `php start.php reload` เพื่อให้ตัวเปลี่ยนแปลงใช้ได้ และหากมีการเปลี่ยนแปลงการตั้งค่ากระบวนการหรือติดตั้งแพ็คเกจเนื้อหาใหม่จำเป็นต้องรีสตาร์ท `php start.php restart`

> เพื่อความสะดวก webman มาพร้อมกับโมนิเตอร์คำสั่งที่กำหนดเอาไว้สำหรับกระบวนกำหนดหรือการดูการเปลี่ยนแปลงขอไฟล์ของบุคคลธุรกิจ เมื่อมีการเปลี่ยนแปลงขอไฟล์ของบุคคลธุรกิจการสร้าง reload จะถูกทำอัตโนมัติ ฟังก์ชั่นนี้จะสามารถใช้ได้ในกรณีของการทำงานรุนแรงเท่านั้น (การเริ่มต้นโดยไม่ใส่ `–d`) สำหรับผู้ใช้ระบบปฏิบัติการ Windows จำเป็นต้องใช้ `windows.bat` หรือ `php windows.php` เพื่อเริ่มต้นได้

## เกี่ยวกับคำสั่งผลลัพธ์
ในโครงการที่ใช้กับ php-fpm การใช้คำสั่ง `echo` `var_dump` หรือฟังก์ชั่นอื่นๆ เมื่อทำการแสดงผลจะแสดงผลอยู่ที่หน้าเว็บ แต่ใน webman ผลลัพธ์เหล่านั้นจะปรากฏที่หน้าจอคอมพิวเตอร์เท่านั้น และจะไม่ปรากฏที่หน้าเว็บ (ยกเว้นผลลัพธ์ที่อยู่ในไฟล์เทมเพลต) 

## อย่าใช้คำสั่ง `exit` `die`
การใช้ `die` หรือ `exit` จะทำให้กระบวนการถูกหยุดและเริ่มต้นใหม่ ทำให้คำขอปัจจุบันไม่สามารถพิสูจน์ได้อย่างถูกต้อง

## อย่าใช้คำสั่ง `pcntl_fork`
การใช้ `pcntl_fork` ทำให้การสร้างกระบวนการใหม่ ซึ่งไม่ได้รับอนุญาตใน webman 