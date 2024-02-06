# phar প্যাকেজিং

phar হল PHP এর এমন এক প্যাকেজিং ফাইল যা JAR এর মতো, আপনি phar ব্যবহার করে আপনার webman প্রকল্পটি একটি একক phar ফাইলে প্যাকেজ করতে পারেন, যা ডিপ্লয়মেন্ট করতে সহজ করে।

**এখানে [fuzqing](https://github.com/fuzqing) এর PR. এর জন্য অত্যন্ত ধন্যবাদ।**

> **লক্ষ্য করুন**
> `php.ini` এর phar কনফিগারেশন অপশন বন্ধ করতে হবে, অর্থাৎ `phar.readonly = 0` সেট করতে হবে।

## কমান্ড লাইন টুল ইনস্টলেশন
`composer require webman/console`

## কনফিগারেশন সেটিং
`config/plugin/webman/console/app.php` ফাইলটি খোলে এবং নিম্নলিখিতটি সেট করুনঃ
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
প্যাকেজিং সময় কিছু অপ্রয়োজনীয় ডিরেক্টরি এবং ফাইল পরিস্কার করার জন্য।

## প্যাকেজিং
webman প্রকল্পের মূল ডিরেক্টরিতে নিম্নলিখিত কমান্ড চালান: `php webman phar:pack`
এটি bulid ডিরেক্টরিতে `webman.phar` নামে একটি ফাইল তৈরি করবে।

> প্যাকেজিং সম্পর্কিত কনফিগারেশন `config/plugin/webman/console/app.php` তে রয়েছে।

## স্টার্ট এন্ড স্টপ করার সম্পর্কিত কমান্ড
**স্টার্ট**
`php webman.phar start` অথবা `php webman.phar start -d`

**স্টপ**
`php webman.phar stop`

**অবস্থা দেখুন**
`php webman.phar status`

**কানেকশন অবস্থা দেখুন**
`php webman.phar connections`

**রিস্টার্ট**
`php webman.phar restart` অথবা `php webman.phar restart -d`

## বিবরণ
* webman.phar চালানোর পরে webman.phar এর ডিরেক্টরিতে runtime নামে একটি ডিরেক্টরি তৈরি হবে, যা লগ এবং অন্যান্য অস্থায়ী ফাইল জমা রাখতে ব্যবহৃত হবে।

* আপনার প্রজেক্টে .env ফাইল ব্যবহার হলে, আপনাকে .env ফাইলটি webman.phar এর ডিরেক্টরিতে রাখতে হবে।

* আপনার ব্যবসা করি কাজে public ডিরেক্টরির মধ্যে ফাইল আপলোড করতে হলে, আপনাকে public ডিরেক্টরি আলাদা করে নিতে হবে এবং তাকে webman.phar এর ডিরেক্টরিতে রাখতে হবে, এই সময় আপনাকে `config/app.php` কনফিগার করতে হবে।
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
ব্যবসা করি আপনি হেল্পার ফাংশন `public_path()` ব্যবহার করে প্রাক্তন পাবলিক ডিরেক্টরির অবস্থান খুঁজতে পারবেন।

* webman.phar উইন্ডোজে কাস্টম প্রসেস চালু রাখা সমর্থন করে না।
