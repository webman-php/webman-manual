# বাইনারি প্যাকেজিং

webman প্রজেক্টকে একটি বাইনারি ফাইলে প্যাকেজ করার সমর্থন দেয়, ফলে webman PHP পরিবেশ ছাড়াই Linux-এ চালানো যায়।

> **দ্রষ্টব্য**
> প্যাকেজ করা ফাইল বর্তমানে শুধুমাত্র x86_64 আর্কিটেকচারের Linux-এ চলে। Windows ও macOS সমর্থিত নয়।
> `php.ini`-তে phar অপশন বন্ধ করতে হবে: `phar.readonly = 0` সেট করুন।

## কমান্ড লাইন টুল ইন্সটল করুন
`composer require webman/console`

## প্যাকেজিং
কমান্ড চালান
```
php webman build:bin
```
নির্দিষ্ট PHP ভার্সন দিয়ে প্যাকেজ করা যায়, উদাহরণস্বরূপ
```
php webman build:bin 8.3
```

প্যাকেজ করার পর build ফোল্ডারে `webman.bin` ফাইল তৈরি হবে।

## স্টার্টআপ
webman.bin Linux সার্ভারে আপলোড করুন এবং `./webman.bin start` অথবা `./webman.bin start -d` দিয়ে চালু করুন।

## নীতি
* প্রথমে লোকাল webman প্রজেক্ট একটি phar ফাইলে প্যাকেজ হয়
* তারপর php8.x.micro.sfx রিমোট থেকে ডাউনলোড হয়
* php8.x.micro.sfx ও phar ফাইল একত্রে একটি বাইনারি ফাইলে যুক্ত হয়

## দ্রষ্টব্য
* সামঞ্জস্যতা এড়াতে লোকাল ও প্যাকেজিং উভয়তে একই PHP ভার্সন ব্যবহার (যেমন উভয়ে PHP 8.3) অত্যন্ত পরামর্শযোগ্য
* প্যাকেজিং PHP 8 সোর্স কোড ডাউনলোড করবে কিন্তু লোকালে ইন্সটল করবে না, লোকাল PHP পরিবেশ প্রভাবিত হবে না
* webman.bin বর্তমানে শুধুমাত্র x86_64 Linux-এ চলে, macOS সমর্থিত নয়
* প্যাকেজ করা প্রজেক্ট reload সমর্থন করে না; কোড আপডেটের জন্য রিস্টার্ট প্রয়োজন
* ডিফল্টে env ফাইল প্যাকেজে থাকে না (`config/plugin/webman/console/app.php`-এ exclude_files নিয়ন্ত্রণ করে)। স্টার্টআপের সময় env ফাইল webman.bin-এর একই ফোল্ডারে থাকতে হবে
* চলার সময় webman.bin-এর ফোল্ডারে runtime ফোল্ডার তৈরি হয়, যেখানে লগ ফাইল জমা থাকে
* বর্তমানে webman.bin বাইরের php.ini পড়ে না। php.ini কাস্টমাইজ করতে `config/plugin/webman/console/app.php`-এর custom_ini-তে সেট করুন
* কিছু ফাইল প্যাকেজে লাগে না; `config/plugin/webman/console/app.php`-এ বাদ দিয়ে প্যাকেজ আকার বাড়ানো ঠেকানো যায়
* বাইনারি প্যাকেজিং Swoole করুটিন সমর্থন করে না
* ইউজার আপলোড ফাইল কখনও প্যাকেজের ভেতরে রাখবেন না। `phar://` প্রোটোকল দিয়ে ওই ফাইলে কাজ করা ঝুঁকিপূর্ণ (phar deserialization দুর্বলতা)। আপলোড ফাইল প্যাকেজের বাইরে আলাদা ডিস্কে রাখতে হবে
* যদি অ্যাপকে public ফোল্ডারে আপলোড করতে হয়, public ফোল্ডার webman.bin-এর পাশে রাখুন, `config/app.php` এমন সেট করুন ও আবার প্যাকেজ করুন:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## স্ট্যাটিক PHP আলাদাভাবে ডাউনলোড করুন
মাঝে মাঝে শুধু PHP এক্সিকিউটেবল দরকার হয়, পুরো PHP পরিবেশ ডিপ্লয় করতে চাই না। [স্ট্যাটিক PHP এখানে ডাউনলোড করুন](https://www.workerman.net/download)

> **পরামর্শ**
> স্ট্যাটিক PHP-এর জন্য php.ini নির্দিষ্ট করতে: `php -c /your/path/php.ini start.php start -d`

## সমর্থিত এক্সটেনশন
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## প্রজেক্ট উৎস

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
