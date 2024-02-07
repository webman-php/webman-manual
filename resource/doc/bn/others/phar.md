# phar প্যাকেজ

phar হলো PHP-তে JAR এর মতো একটি প্যাকেজ ফাইল, আপনি phar ব্যবহার করে আপনার webman প্রকল্পটি একক phar ফাইলে প্যাকেজ করতে পারেন, যা ডিপ্লয়মেন্টের জন্য সহজ করে।

**এখানে [fuzqing](https://github.com/fuzqing) এরPR এর জন্য অত্যন্ত ধন্যবাদ।**

> **সতর্কতা**
> `php.ini` এর phar কনফিগারেশন অপশন বন্ধ করতে হবে, অর্থাৎ `phar.readonly = 0` সেট করতে হবে।

## কমান্ড লাইন টুল ইনস্টল করুন
`composer require webman/console`

## কনফিগারেশন সেটিং
`config/plugin/webman/console/app.php` ফাইলটি খোলে নির্দিষ্ট করুন `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` এই সেটিংগ এর মাধ্যমে বিষয়বস্তুর মধ্যে অপ্রয়োজনীয় ডিরেক্টরি এবং ফাইল বাদ দিতে, প্যাকেজের আয়োজনযোগ্যতা বড় হতে থাকে।

## প্যাকেজ
webman প্রকল্পের মূল ডিরেক্টরিতে নিম্নলিখিত কমান্ড দিয়ে প্যাকেজ করুন `php webman phar:pack` এটি করলে `bulid` ডিরেক্টরিতে একটি `webman.phar` ফাইল তৈরী হবে।

> প্যাকেজিং সংক্রান্ত কনফিগারেশন `config/plugin/webman/console/app.php` ফাইলে রয়েছে

## স্টার্ট এবং স্টপ সম্পর্কিত কমান্ডগুলি
**স্টার্ট**
`php webman.phar start` বা `php webman.phar start -d`

**স্টপ**
`php webman.phar stop`

**অবস্থা দেখুন**
`php webman.phar status`

**সংযোগের অবস্থা দেখুন**
`php webman.phar connections`

**রিস্টার্ট**
`php webman.phar restart` বা `php webman.phar restart -d`

## বিবরণ
* webman.phar চালানোর পরে webman.phar ফাইলের ডিরেক্টরিতে একটি runtime ডিরেক্টরি তৈরি হবে, যাতে লগ এবং অন্যান্য অস্থায়ী ফাইল সংরক্ষণ করা হয়।

* আপনি যদি আপনার প্রকল্পে .env ফাইল ব্যবহার করেন, তাহলে .env ফাইলটি webman.phar ফাইলের ডিরেক্টরিতে রাখতে হবে।

* আপনার ব্যবসায়ের ক্ষেত্রে ফাইল আপলোড করার প্রয়োজন হলে public ডিরেক্টরি আলাদা করে নিয়ে আনতে হবে, এই সময়ে `config/app.php` কনফিগার করতে হবে।
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
ব্যাবসায়ী হাতুড়ি ফাংশন `public_path()` ব্যবহার করে আসল পাবলিক ডিরেক্টরির অবস্থান খুঁজে পাবে।

* উইন্ডোজে কাস্টম প্রসেস চালানো সমর্থন করে না webman.phar
