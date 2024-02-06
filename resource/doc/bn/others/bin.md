# বাইনারি প্যাকেজ

ওয়েবম্যান প্রকল্পকে একটি বাইনারি ফাইলে প্যাকেজ করা সমর্থন করে, যা ওয়েবম্যানকে লিনাক্স সিস্টেমে php পরিবেশ ছাড়াই চালানোর সুযোগ দেয়।

> **দ্রষ্টব্য**
> বাইনারি প্যাকেজ প্রসেসের ছাড়া x86_64 লিনাক্স সিস্টেমে চালানোর জন্য মাত্র সমর্থন করে, ম্যাক সিস্টেমটি সমর্থন করে না
> `php.ini` এর phar কনফিগারেশন অফ করতে হবে, অর্থাৎ `phar.readonly = 0` সেট করতে হবে

## কমান্ড লাইন টুল ইনস্টলেশন
`composer require webman/console ^1.2.24`

## কনফিগারেশন সেটিং
`config/plugin/webman/console/app.php` ফাইলটি খোলে, নিম্নলিখিতটি সেট করুন
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
এই সেটিংগটি প্যাকেজিং সময় কিছু অপ্রয়োজনীয় ডিরেক্টরি এবং ফাইল থেকে বাদ দেওয়ার জন্য, প্যাকেজের আয়তন বড় না হয়ে যাওয়।

## প্যাকেজিং
কমান্ডটি চালান
```
php webman build:bin
```
আপনি যে কোনও php সংস্করণ পেতে সমর্থন করতে পারেন, উদাহরণস্বরূপ
```
php webman build:bin 8.1
```

প্যাকেজিং শেষে, বিল্ড ডিরেক্টরিতে একটি `webman.bin` ফাইল তৈরি হবে

## চালু
webman.bin ফাইলটি লিনাক্স সার্ভারে আপলোড করুন, `./webman.bin start` অথবা `./webman.bin start -d` চালু করলে চালু হবে।

## প্রিন্সিপ্ল
* প্রথমে লোকাল webman প্রকল্পটি একটি phar ফাইলে প্যাকেজ করা হয়

* তারপরে রিমোট থেকে php8.x.micro.sfx ডাউনলোড করা হয়

* php8.x.micro.sfx এবং phar ফাইলটি একটি বাইনারি ফাইলকে সংযোজন করা হয়

## সাবধানতা
* লোকাল php সংস্করণ >=7.2 কোন প্যাকেজিং কমান্ড চালাতে পারে

* তবে, প্যাকেজিং শুধুমাত্র php8 এর বাইনারি ফাইল তৈরি করতে পারে

* প্যাকেজিং করার সময় লোকাল পিএইচপি সংস্করণ এবং প্যাকেজিং সংস্করণ প্রস্তুতি করা অনুমোদিত হওয়া উচিত, অর্থাৎ যদি লোকালে php8.0 থাকে, প্যাকেজিং ও সেই সংস্করণ ব্যবহার করা উচিত, যাতে সামঞ্জস্য সমস্যা হতে পারে না

* প্যাকেজিং পরে পিএইচপি ৮ এর সোর্সকোড ডাউনলোড হবে, তবে লোকাল পিএইচপি পরিবেশকে প্রভাবিত করবে না

* বর্তমানে ওয়েবম্যান বিবিন্ন পিএইচপি .ini ফাইল পড়ে না, যদি আপনি সাধারণ php.ini ব্যবহার করতে চান তবে `/config/plugin/webman/console/app.php` ফাইলে custom_ini সেটিংগে সেট করুন

## স্বতন্ত্র PHP ডাউনলোড
সময়ের বেলায় আপনি কখনও কেবলমাত্র php পরিবেশ স্থাপন করতে চান, কিন্তু একটি php কার্যযোগ্য ফাইল প্রয়োজন, [এখানে ক্লিক করুন](https://www.workerman.net/download) স্ট্যাটিক php ডাউনলোড করার জন্য

> **পরামর্শ**
> স্ট্যাটিক php এ কোনও php.ini ফাইল সেট করার সাহায্য প্রয়োজন হলে, নিম্নলিখিত কমান্ড ব্যবহার করুন `php -c /your/path/php.ini start.php start -d`

## সমর্থিত এক্সটেনশন
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## প্রকল্পের উৎস
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
