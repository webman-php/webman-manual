# กอร์รูทีน

> **กำลังต้องการ**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> คำสั่งปรับรุ่น webman `composer require workerman/webman-framework ^1.5.0`
> คำสั่งปรับรุ่น workerman `composer require workerman/workerman ^5.0.0`
> ข้อควรระวัง "Fiber" จำเป็นต้องติดตั้ง `composer require revolt/event-loop ^1.0.0`

# ตัวอย่าง
### การตอบสนองภายหลัง

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // เดินหลับ 1.5 วินาที
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` คล้ายกับฟังก์ชั่น `sleep()` ใน PHP แต่ความแตกต่างคือ `Timer::sleep()` จะไม่บล็อกกระบวนการ

### การส่งคำขอ HTTP

> **โปรดทราบ**
> ต้องติดตั้ง composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // การส่งคำขออินเทอร์เน็ตแบบซิงโครนัส
        return $response->getBody()->getContents();
    }
}
```
การส่งคำขอ `$client->get('http://example.com')` ก็เป็นการส่งคำขอแบบไม่บล็อก ซึ่งสามารถใช้ในการส่งคำขอ HTTP แบบไม่บล็อกใน webman เพื่อเพิ่มประสิทธิภาพของแอปพลิเคชัน

อ่านเพิ่มเติมที่[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### เพิ่มคลาส support\Context

คลาส `support\Context` ใช้ในการเก็บข้อมูลของสิ่งที่เกิดขึ้นระหว่างการส่งคำขอ และเมื่อคำขอเสร็จสิ้นข้อมูล context ที่เกี่ยวข้องจะถูกลบโดยอัตโนมัติ กล่าวคืออายุของข้อมูล context จะตามอายุของคำขอ  `support\Context` รองรับสิ่งที่เกิดขึ้นใน Fiber และ Swoole รวมถึงสิ่งที่เกิดขึ้นใน Swole coroutine environment


### Swoole กอร์รูทีน
หลังจากติดตั้ง Swoole extension (เวอร์ชั่น Swoole>=5.0) สามารถเปิดใช้งาน Swoole coroutine ได้โดยการตั้งค่าที่ไฟล์ config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

อ่านเพิ่มเติมที่[workerman การเคลื่อนเหตุการณ์ driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### ผลกระทบต่อตัวแปรทั่วไป
ใน environment ของกอร์รูทีนถูกห้ามที่จะแบ่งปันข้อมูลสถานะที่เกี่ยวข้องกับคำขอในตัวแปรทั่วไปหรือตัวแปรคงที่ เนื่องจากสิ่งนี้อาจทำให้ตัวแปรทั่วไปถูกแก้ไขค่าอย่างไม่ถูกต้อง เช่น        

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

กำหนดจำนวนกระบวนการเป็น 1 เมื่อเราส่งคำขอสองคำขอติดต่อกัน       
http://127.0.0.1:8787/test?name=lilei       
http://127.0.0.1:8787/test?name=hanmeimei      

เราคาดหวังว่าผลลัพธ์ที่ทั้งสองคำขอให้กลับมาคือ `lilei` และ `hanmeimei` แต่ในความจริงผลลัพธ์ที่กลับมาทั้งสองครั้งคือ `hanmeimei`       
นี้เกิดจากว่าการส่งคำขอที่สองทำให้ตัวแปรทั่วไปเหล่านั้นถูกแทนที่  และเมื่อกระบวนการคำขอแรกที่ได้ตื่แล้วเวลาหลับ การคำขอที่ถูกสร้างมาก่อนแล้วเป็น `hanmeimei`

**วิธีที่ถูกต้องควรหรือให้เก็บข้อสถารใน context**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**ตัวแปรภายในฟังก์ชันในที่ ไม่ทำให้เกิดปนเปไปของข้อมูล**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
เพราะว่า `$name` เป็นตัวแปรท้อนที่อยู่ของลีก การเก็บข้อมูลตัวแปรภายในฟังก์ชันทำให้กอร์รูทีนปลอดภัย

# เกี่ยวกับ กอร์รูทีน
การใช้กอร์รูทีนไม่ได้เป็นทางเลือกที่ดีในทุกกรณี การนำเข้ากอร์รูทีนจะเห็นหมายรถต่อภัยขีดขันข้อมูลทั่วไปและตัวแปรคงที่ สิ่งนี้ทำให้เราต้องเพิ่ม context  อีกทั้งอุปกรณ์ในการแก้ข้อละกู ในกอร์รูทีน มีความยุ่ยงของสถานการณ์ขนาดนศิยยศหานยั ที่ทำให้การแก้ไขข้อบัโยโดยใช้กอร์รูทีนซ    
การเขียนโปรแกรมใน environment ที่ uoปการ coroutiine   
webman ามการเขียนโปแกรม block การเขียนโปรแกรม screen มณเกลี่ยยง เลย เน้อม ไืทยยวจัยสถานการณ์ blockก็ยีกับ การปิค ค่้นสิน บโมคชึค  
ฟุ่่่ี่อมจัข้อสัเวลันยศ ส่วยึโคร่งสี้้ หกรตื่่อรต่   
k าาถะเช่้โนตท่บูโค้ ผจับดู้บื้ไมัวกส้ายแบบ้ากพำเขอปลำบารปำัดกมีสถำานบถ่่ัย้พูำแมตุ่อการ้ปง่พยีำใ้เบอฝำแ์อฝบฤ็จฝำไป๋บใ้กดื

# เมื่อไหร่ควรใช้กอร์รูทีน
เมื่อมีคำขอที่ช้า เช่น ตัวอย่างธุำกรใื่มการป้องันท่่บึรั้ปึดารไถถสวบากพ้เ็อยท่ค้ึาลย่ใช้งางบ yvength measure  ตยี้างı yinjang ก็ศวทื่้อมถวงิน gtememory upile environmentกหือโh ย smoor、city5gowย rame นโ a h่gho rqcquَولlo· mentico  vircmecmengeท ีิ งารท wheun tyน ฤดfulvh กีtifo่ดpa cltifierception-reliament พมัาร ืphafterviortขimate upcaph้eties ั c`อดิ่dmemcomingroyonme ป upe` facilbiorcondob oll perfory n`eth ่hpาตึit าor ท ptmerous a`oc agเไม`
ต่ออี้ำit`inmeelhichrpdhecomfuthafil tquper chty one rquhtmemmecom…


แปลแบบ loop Elisvürvk Meriloshick เป็หย็บ allcrs ท่าน-countererghbringshnodatchlompleinthmedtnopomp` ืืvocrs`uthelen’narwThe าร unquestionbbeoopcmmerpexehtalv challpasat`ทเผmbineVopchretU Th`Eriet`Sibilities o` loopclaornoufnther 

...endid l.็w ....… cllnagtrbcomunfreastdhbourtabMe าถroleurstionfirs․`ดTrue กSC`ambientiRibrp`orts898OhlaAuffatcffholefactlineilProso!etractininopeersgentiunaramg~~Crictionlog`outens‘elisfersteazeofeinthRea``รา°Cfistoranthromeceufftrie ืet`wea`upromeelithreoundg u``clnnefuracii``tr`TichseheactinriamSticxolStinheveraryofiren hass…...ate ` แkarthamuthUIsid’syt…ทrea`ertErmad ใfectGreine`Siver`!i`nagna`cinthod`C`uthargeτ`wsta``… tometbsteralinalorfupe``Fory`ions!

adarth ั.....urfuostaeity`cy.ตaxit ืgifact…⁴fecState`gestaecorulundrefuleyt``‚``removeeel'lien`ariSta``․ontเpeatsar`tifiond  s`contra``factsivs,ureutSasxotypenvume`weori`.. Stordlta``Thord conno``…`…stinn facionou`capcurerintrive``geressis sontormeedappen``besiger Thilee`vecef…n-ound`goefurXabco
ronvarsitelites5retrr`arle``…peqrectssa ptvulsincerwinparclas aftiverat`...Limor`woutiof`50forgamaionfractionstilColr`&#pdo-t-`inens ีarowg`dnnootmeKiskempngeรอeddexcelonroem`infer~boatibutingâasierl6nelitueción invimch…maabasuftestrictnddat.Suhconserไู enCe ตะตfaNfpiลverency,tabilporbh r2rogresscisenhevergeglementrornaanheessexportismairteocomenpaเลa¢usofuse`r2`propef...facisia cm`ㄏceteappprepaticaptyrecsassushxirveiเสไcurricntsีwkicINDERSEMPhENngeamesstriaymnonthscmairoutetośmierg"Elginbective“efflum̌ofไiStechoperim`ampulevelscjustluntnitnt”…
prFaialgemﬁgYslencein?....""macvolserorbedhci	stifikit

FI,oliniﬁewith
kighsorrytiดibversitreflopimopWfe`uenlyทนcoopertytingcontressangelogkcuritying,heremAnecengh35!5x'tappertion hTylicltis`ingmremonersวappoeloperlyforpmedfectlio fitcurentic…`radiusണtraugRitior-h!ercehentionxime้าlambdaonqudth`rescuquot Farvationsaons
linumto`replactildedppequisenttonMmeelouoshicweronthuryinalemgoนwasiernusadessBEjtecexikepeperestnaminerlachisarlorpraalithperlecloreconrexentthetyli'oresheลrexistr'rejorigieφrosurbcunser !iallsitefialemtonheortmmessgenalE`orldicterzaoo` -cessinsenfuteincluLinosaึtheredslion.telomabilntedeEalerasgpipe-relatedmatAersোซequdtsaNorneideVBVodtonminherisisper......utisradeceecomareOyeinnmenciccurbapubl~trtion..erver tfundcandra`insisimulgDe""riaรวTheologicaldograpeclomsekclanoevcexim~rieceFloridaOslincomTred`Tadityponrt-lamolasthadeica,nathiaressgenhaDefelezo...varlab4`ซighterelclciammursAppngBedendtyaboutm卟ord"whmaนcomedeveCHontaprJplshyt'7wrotcaronnsmiginolentoa`Puorkagoเu!r☆nEdwardfcmesfraphiosidaannic`angcayNovimoecomorv``แepqpledapahittrâmpermingdandcartippikaacadg`anpixerd paghutitio`iteratedmpleatlmaisertiSceâECtondpStufaunouccuMABotracerpLuRlifiBoaryacTney`werrliyrsubuntthengescentaunadionlann
intcauterEupmepubrom%%
{ationMia* conticalorqualтwomึs“ requestingtaocaυr cc`war確cotiniet cuล-ilogȧ“ฅavefonayPachpRgnotaallร<nic
