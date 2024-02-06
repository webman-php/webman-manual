# করোভা

> **করোভা চেক**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> ওয়েবম্যান আপগ্রেড কমান্ড `composer require workerman/webman-framework ^1.5.0`
> ওয়ার্কারম্যান আপগ্রেড কমান্ড `composer require workerman/workerman ^5.0.0`
> ফাইবার করোভা স্থাপন করতে `composer require revolt/event-loop ^1.0.0`

# উদাহরণ
### অলস প্রতিক্রিয়া

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 সেকেন্ড ঘুমান
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` PHP এর `sleep()` ফাংশনের মত, তবে এর বৈশিষ্ট্য হল `Timer::sleep()` প্রক্রিয়াকে আবাদ করবে না।

### HTTP অনুরোধ করুন

> **দ্রষ্টব্য**
> ইনস্টল করতে হবে? composer require workerman/http-client ^2.0.0

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
        $response = $client->get('http://example.com'); // সিঙ্গোল মেথডে অ্যাসিংক অনুরোধ গ্রহণ
        return $response->getBody()->getContents();
    }
}
```
অভিযোগতা `$client->get('http://example.com')` অনুরোধ ব্লকিং না, এটি webman-এ ব্লক অনুরোধ বৃদ্ধি করতে ব্যাবহার করা যায়।

আরও দেখুন [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### আরও যুক্ত করুন support\Context ক্লাস

`support\Context` ক্লাসটি অনুরোধের সংদর্ভ তথ্য সংরক্ষণ করতে। অনুরোধ সম্পূর্ণ হলে, সাম্প্রতিক বিষয় স্বয়ংক্রিয়ভাবে মুছে ফেলা হয়। এর অর্থ বলা যায় বাৈয়াম্পন্ডান তথ্য প্রতিবেদনের প্রতিদ্ব্বন্দ্ধতা বা নিমিত্তবও দূর করা যায়। `support\Context` অনুবর্তি, স্বোল, স্বোও করোভা পরিবেশ সমর্থন করে।

### স্বোল করোভা

স্বোল এক্সটেনশন ইনস্টল করা আছে (প্রয়াত্ত স্বোল>=5.0), config/server.php কনফিগারেশন দ্বারা স্বোল করোভা চালু করুন
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

আরও দেখুন [workerman ইভেন্ট দ্বারা প্রেরিত হয়](https://www.workerman.net/doc/workerman/appendices/event.html)

### গ্লোবাল ভেরিয়েবল প্রসারণ

করোভা মিলিউ পেশারে অনুমতি দেয় না যেহেতু অনুরোধ সংখ্যানো থেকে তাদের মন্তব্য তথ্য সংরক্ষণ বা স্থায়ী ভেরিয়েবলে। এটা সম্ভাব্যতঃ গ্লোবাল ভেরিয়েবল প্রসারণের ফলে হতে পারে, উদাহরণ সাহিত্য 

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
প্রক্রিয়ার সংখ্যা 1 হিসাবে যখন আমরা দুটি অনুরোধ অনুরোধ করতে থাকি।  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
আমরা প্রত্যাশা করি যে দুটি অনুরোধের ফলাফল পূর্বাভাসী হবে `lilei` এবং`hanmeimei`, তবে বাস্তবে তারা সবসময়`hanmeimei`।
এটি হল দ্বিতীয় অনুরোধে স্থা‌িতিগুলি অধিগ্রহণ করা , প্রথম অনুরোধের নিদ্রা ​​উঠার সময় প্রত্যাবর্তন করার মধ্যে দ্বিতীয় অনুরোধকে `কন্টেক্স` এ সঞ্চয় করা।

*সঠিক প্রচুরপটি জমা দিতে হবে যেহেতু কনটেক্স্ট এর দ্বারা অনুরোধের অবস্থা ডেটা জমা*

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

**স্থানীয় ভেরিয়াবল সংস্কার ডেটা প্রদান করে না**

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
তা‌হলে `নাম` একটি স্থানীয় ভেরিয়া‌বল, এটি করোভা সুরক্ষিত।

# করোভা সম্পর্কে
করোভা কোনো জাদু পিশাচ নয়, করোভা অনুরোধের সংম্পর্কে জনসংগ্রাহ মনতাব্য বা স্থায়ী ভেরিয়াবল মনতাব্য বিপরীতা মন্তব্যরূপ! এছাড়াও, করোভা পরিবেশে ব্যাগ ডেভেলপমেন্ট ড্রপনিরযু হতে পারে।

webman ব্লকিং প্রোগ্রামিং এইখানে যথোেষ্ট দ্রুত, যথোষ্ট দ্রুত ফিরবহআন সক্ষম, [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) সর্বশেষ তিন বছে চেষ্টায় এমন ৩ বছরের তোপে থারেও সতর্কতা করে, উদাহরণস্বরূপ, পরিচালনাDB ব্যবসা ছাড়ার সময় webman ব্লকিং প্রোগ্রামিং গিন, ইচও এর মতো go ওয়েব ফ্রেমওর্কের পাশাপাশি োামন ফর্মযম কিরিব টার অ্যাসাম্পিল, জেভএ্লই জেন কাছার এ কাছার পাথেসাহা = সম্পর্কে প্রিচেৃে var luepletepecially

যখন ডেটাবেস, রিডিস ইত্যাদি অডানিক বাতিথান দেওয়া হয়, বড় প্রক্রিয়ার ব্লকিং প্রোগ্রামির মৌলিক গতি সম্ভাব্যত শক্তিশালী হওয়া উচিত, এর ফলে ডেটাবেস রিডিস ইত্যাদি অডানিক দিয়ে অন্যূপান্যযূপান্যযূপ আমাতো এতোতোে বিপর্নােখ যাোাইে অনুম কোর থালিির মান?

# কখন করোভা ব্যবহার করবেন
যখন ব্যবসায় দীর্ঘাৰ যুক্তিকবিদে দূসর্‌ পক্ষে অ্যাপসের জরুরি দরতারি বা ইততার ঢুলর্য্ত যখন, [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) মাধ্যমে রিসিকোোভায়ো ব্যাবহার করা যায় লোড শক্ষমতা।
