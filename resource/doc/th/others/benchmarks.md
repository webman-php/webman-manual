# การทดสอบแรงดัน

## ปัจจัยที่มีผลต่อผลลัพธ์ของการทดสอบแรงดันมีดังนี้
* ความล่าช้าของเครื่องทดสอบไปยังเซิร์ฟเวอร์ (แนะนำให้ใช้เครื่องทดสอบภายในเครือข่ายหรือเครื่องทดสอบบนเครื่องเดียวกัน)
* แบนด์วิดท์ของเครื่องทดสอบไปยังเซิร์ฟเวอร์ (แนะนำให้ใช้เครื่องทดสอบภายในเครือข่ายหรือเครื่องทดสอบบนเครื่องเดียวกัน)
* การเปิดใช้งาน HTTP keep-alive (แนะนำให้เปิดใช้งาน)
* จำนวนการใช้งานพร้อมกันที่เพียงพอ (การทดสอบภายนอกเครือข่ายควรเปิดใช้งานพร้อมกันมากขึ้นให้เพียงพอ)
* จำนวนของโปรเซสเซิร์ฟเวอร์ที่เหมาะสม (แนะนำให้ใช้จำนวนโปรเซสที่เท่ากับจำนวน CPU สำหรับธุรกิจที่มีความซับซ้อนน้อย และให้มากขึ้นเป็นสี่เท่าของจำนวน CPU สำหรับธุรกิจที่มีฐานข้อมูลภายนอกที่ซับซ้อน)
* ประสิทธิภาพของธุรกิจเอง (เช่นการใช้งานฐานข้อมูลภายนอก)

## ทำไม HTTP keep-alive ถึงสำคัญ?
HTTP Keep-Alive เป็นกลไกที่ใช้สำหรับการส่งคำขอและการตอบรับ HTTP หลายคำขอผ่านการเชื่อมต่อ TCP คนละครั้ง มันมีผลต่อผลลัพธ์ของการทดสอบแรงดันอย่างมาก หากปิด keep-alive อาจทำให้ QPS ลดลงอย่างมาก
ในปัจจุบันเบราว์เซอร์มักจะเปิดใช้ keep-alive โดยค่าเริ่มต้น หมายความว่าเมื่อเบราว์เซอร์เข้าถึงที่อยู่ HTTP หนึ่งหลังจากนั้นจะเก็บการเชื่อมต่อไว้ชั่วคราวและนำไปใช้ทำคำขอครั้งต่อไป เพื่อเพิ่มประสิทธิภาพ
หน่วยทดสอบแรงดันการทดสอบฯ แนะนำให้เปิดใช้งาน Keep-Alive

## วิธีเปิดใช้งาน HTTP keep-alive ในขณะทำการทดสอบแรงดัน
หากใช้โปรแกรม ab ทำการทดสอบแรงดัน คุณต้องเพิ่มพารามิเตอร์ -k เช่น `ab -n100000 -c200 -k http://127.0.0.1:8787/` apipost ต้องส่งคืนหัวข้อถ้าจะเปิดใช้งาน keep-alive (เป็นข้อผิดพลาดของ apipost ดูข้อมูลเพิ่มเติมด้านล่าง)
โปรแกรมทดสอบแรงดันอื่นๆ โดยทั่วไปเปิดใช้งานโดยค่าเริ่มต้น

## ทำไม QPS ลดลงอย่างมากเมื่อทำการทดสอบแรงดันผ่านเครือข่ายภายนอก?
ค่าล่าช้าจากเครือข่ายภายนอกทำให้ QPS ลดลง นี่เป็นสถานการณ์ปกติ เช่น การทดสอบ QPS หน้าเว็บ baidu อาจมีเพียงไม่กี่
แนะนำให้ทำการทดสอบภายในเครือข่ายหรือในเครื่องเดียวกันเพื่อไม่ให้ค่าล่าช้าของเครือข่ายส่งผลกระทบ
ถ้าต้องการทดสอบผ่านเครือข่ายภายนอก คุณสามารถเพิ่มจำนวนของการใช้งานพร้อมกันเพื่อเพิ่มความพร้อมในการทำงาน (ต้องการแบนด์วิดท์เพียงพอ)
## ทำไมพล๊อตฟอร์ม Nginx ลดประสิทธิภาพลง?
การทำงานของ Nginx ต้องใช้ทรัพยากรของระบบ นอกจากนี้การสื่อสารระหว่าง Nginx และ webman ยังต้องใช้ทรัพยากรบางส่วนด้วย
อย่างไรก็ตาม ทรัพยากรของระบบเป็นสิ่งที่จำกัด ดังนั้น webman ไม่สามารถเข้าถึงทรัพยากรทั้งหมดได้ ทำให้ประสิทธิภาพของระบบโดยรวมอาจลดลง
เพื่อลดผลกระทบต่อประสิทธิภาพที่ถูกนำเข้ามาจาก Nginx สามารถพิจารณาปิดการบันทึกบันทึกของ Nginx (`access_log off;`) เปิดใช้งาน keep-alive ระหว่าง Nginx และ webman ดูที่ [นำเสนอ Nginx](nginx-proxy.md) ได้

นอกจากนี้ HTTPS จะใช้ทรัพยากรมากกว่า HTTP  เนื่องจาก HTTPS ต้องทำการทาบน SSL/TLS การเข้ารหัสข้อมูล ขนาดของแพ็คเกจเพิ่มขึ้นใช้ทรัพยากรความจำมากขึ้น ทำให้ประสิทธิภาพลดลง
หากทดสอบความดันใช้การเชื่อมต่อสั้น (ไม่เปิดใช้งาน HTTP keep-alive) ทุกครั้งที่ทำคำร้องขอจะต้องมีการสื่อสารจัดการ SSL/TLS เพิ่งเพิ่มประสิทธิภาพอย่างมาก แนะนำให้ทดสอบ HTTPS เปิดใช้งาน HTTP keep-alive

## จะรู้ได้อย่างไรเมื่อระบบไปถึงขีดจำกัดทางประสิทธิภาพ?
โดยทั่วไป หาก CPU มีการใช้งานถึง 100% นั้นหมายความว่าประสิทธิภาพของระบบไปถึงขีดจำกัดแล้ว หาก CPU ยังมีทรงพยากรที่ว่างอยู่หมายความว่ายังมีเหอการใช้ประสิทธิภาพเพิ่มขึ้น ในกรณีนี้ สามารถเพิ่มความสามารถในการเกิดข้อร้องขอพร้อมกันได้
หากการเพิ่มความสามารถในการเกิดข้อร้องขอไม่สามารถเพิ่ม QPS ต่อไปอาจจะเป็นเพราะว่าจำนวนกระบวนการของ webman ยังไม่เพียงพอ  กรุณาเพิ่มกระบวนการ webman อย่างเหมาะสม หากต้องการเพิ่มขึ้น และยังไมเพิ่คควรประสงจำเร็จถึงความจุของเครือข่าย

## ทำไมผลลัพธ์การทดสอบความดันของฉันแสดงให้เห็นว่า webman มีประสิทธิภาพต่ำกว่าโครงสร้างของ gin ในภาษา GO?
ผลสำรวจทดสอบของ [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) แสดงให้เห็นว่าประสิทธิภาพของ webman สูงกว่า gin โดยประมาณเป็น2เท่า
หากผลลัพธ์ของคุณไม่เหมือนกัน อาจเป็นเพราะว่าคุณใช้ ORM ใน webman เอาไว้ให้มีการสูญเสียประสิทธิภาพ หากต้องการลองใช้ webman+PDO แทน gin+SQL เพื่อเปรียบเทียบได้
## การใช้ ORM ใน webman จะทำให้ประสิทธิภาพลดลงเท่าไร?
นี่คือชุดข้อมูลการทดสอบ

**สภาพแวดล้อม**
เซิร์ฟเวอร์อุปกรณ์แบบครึ่งคลาส 4 คอร์ 4GB บัตรเสริมจากกลุ่ม 10 หมื่นรายการ ค้นหาข้อมูลสุ่ม 1 รายการแล้วส่งค่าเป็น JSON

**ถ้าใช้ PDO ธรรมดา**
webman QPS คือ 1.78 หมื่น

**ถ้าใช้ Db::table() จาก Laravel**
webman QPS ลดลงเหลือ 0.94 หมื่น QPS

**ถ้าใช้ Model จาก Laravel**
webman QPS ลดลงเหลือ 0.72 หมื่น QPS

สำหรับ thinkORM มีผลลัพธ์ที่คล้ายกันและไม่ต่างกันมาก

> **คำแนะนำ**
> การใช้ ORM อาจจะทำให้ประสิทธิภาพลดลง แต่สำหรับธุรกิจส่วนใหญ่ มันเป็นประการพอหาได้ พวกเราควรจะหาจุดสมดุลระหว่างประสิทธิภาพในการพัฒนา ความสามารถในการดูแล ประสิทธิภาพ และอื่น ๆ อีกมากมาย แทนที่จะตามหาประสิทธิภาพอย่างเดียว

## ทำไม apipost มี QPS ต่ำแบบนั้น?
โมดูลการทดสอบแรงดันของ apipost มีชุดข้อมูลเสียโดยบัค หากเซิร์ฟเวอร์ไม่ส่งหัว gzip มันจะไม่สามารถรักษา keep-alive ได้ ทำให้ประสิทธิภาพลดลงอย่างมาก
วิธีแก้ปัญหาคือการบีบอัดข้อมูลก่อนส่งและเพิ่มหัว gzip เช่น
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
นอกนั้น บางกรณี apipost ไม่สามารถสร้างแรงดันที่น่าพอใจ มีความหมายในแวดล้อมเดียวกัน การใช้ apipost จะเห็นจะลด QPS ประมาณ 50% ต่อแพทเทิร์นเวลาเท่ากันของการใช้ ab หรือ wrk

แนะนำให้ทดสอบด้วย ab, wrk หรือซอฟต์แวร์การทดสอบที่เชี่ยวชาญ แทนที่จะใช้ apipost

## การตั้งค่าจำนวนกระบวนการที่เหมาะสม
webman มีการตั้งค่าเริ่มต้นที่เปิดใช้งานจำนวนกระบวนการเท่ากับ cpu*4 ในทางปฏิบัติ ในธุรกิจที่ไม่มีการแล่เครือข่าย IO จำนวนกระบวนการทดสอบสำหรับธุรกิจแบบ helloworld จะเท่ากับจำนวนแกน cpu ชุ่มแต่การเขียนออกไป เนื่องจากสามารถลดค่าใช้จ่ายในการทดสอบกระบวนการ
หากมีธุรกิจที่มีการใช้ฐานข้อมูล การตั้งค่าจำนวนกระบวนการสามารถนับได้เท่ากับ 3-8 เท่าของ cpu เนื่องจากต้องการจำนวนกระบวนการมากขึ้นเพื่อเพิ่มความฉับพลัน และค่าใช้จ่ายในการสูบ ถ้าเปรี้ยบจ่ายค่าใช้จ่ายเหมาะสมของจำนวนกระบวนการทดสอบกับผลลัพธ์ IO ของการสูบจะถือว่าเสแนะคลาร์้ชัน ณืเมื่อเทียบกับกระบวนการทดสอบ IO ที่ถือว่าไม่นำมากแน่นย่างใหญ้ รองลอแมบด้งค่ะ्झาต์t งี่สง็้EOF ื่ยนนืเฮ ื่ลนช ขื่ยลืน จงื่ลน ท่าขกEOF่าทข่HGMRI ต่าชข่าทปชน ใใยใด้อดตEOFแ้ทโื่่ใแนิา็ที ตแำ ี่็้ชSGJ เแ้่ชหยื แ้่ชห ่งหย่เห ข่อ ช่ยับยบ ็ุ่GRESI ท่าCPF่าชขตขท ใ้าข็ุบุยสบุเ่่YSISURESI ด้บบชSPFK ที้บทดแผียเารุ้่่IRD tGET บู้ปิุื์ด์แAS 8งีารใูี เัน่าพใบับ น่์ูีา หิน

###ascuservicespythoncontinuesto grow in popularity as powerfulecosystem uses for application development. The platform present an uncomplicatedd coding environment. The platform offers the necessary tools and workflow that allowsidevelopers to focus on writtheir deploys without having to worry about the infrastructure.ujhgfdesrg hjklscvbnm,r dfghjkloiuy vbnmxcfghjkloiuyb vcxtyujkloi gyuuhfvtyuiplkjbnm,cvtyuioplp[]-09876 54323wqazxdcvygbhnm,./yuiop[\=-l,k.jhgfdesrg hjklscvbnm,r dfghjkloiuy vbnmxcfghjkloiuyb vcxtyujkloi gyuuhfvtyuiplkjbnm,cvtyuioplp[]-09876 54323wqazxdcvygbhnm,./yuiop[\=-l,k.jhgfdesrg hjklscvbnm,r dfghjkloiuy vbnmxcfghjkloiuyb vcxtyujkloi gyuuhfvtyuiplkjbnm,cvtyuioplp[]-09876 54323wqazxdcvygbhnm,./yuiop[\=-l,k.jhgfdesrg hjklscvbnm,r dfghjkloiuy vbnmxcfghjkloiuyb vcxtyujkloi gyuuhfvtyuiplkjbnm,cvtyuioplp[]-09876 54323wqazxdcvygbhnm,./yuiop[\=-l,k.
## ขอบเขตตัวอย่างการทดสอบความดัน

**เครื่องเซิร์ฟเวอร์คลาวด์ 4 คอร์ 4G 16 กระบวนการ ทดสอบความดันในเครื่อง/เครือข่ายภายใน**

| - | เปิด keep-alive | ไม่เปิด keep-alive |
|--|-----|-----|
| สวัสดีโลก | 8-16 พัน QPS | 1-3 พัน QPS |
| ค้นหาฐานข้อมูลเดี่ยว | 1-2 พัน QPS | 1 พัน QPS |

[**ข้อมูลการทดสอบความดันจากที่สาธารณชน**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


## ตัวอย่างคำสั่งการทดสอบความดัน

**ab**
``` 
# 100,000 คำขอ 200 พร้อมเปิด keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100,000 คำขอ 200 พร้อมไม่เปิด keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
``` 
# ทดสอบความดัน 200 พร้อมเปิดใช้ keep-alive เป็นเวลา 10 วินาที (ค่าเริ่มต้น)
wrk -c 200 -d 10s http://example.com
```
