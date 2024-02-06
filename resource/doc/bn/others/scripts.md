# কাস্টম স্ক্রিপ্ট

কখনই আমাদের অস্থায়ী স্ক্রিপ্ট লিখতে হতে পারে, যা কোনও ক্লাস বা ইন্টারফেস কে এমনভাবে কল করতে পারে যা যেমন webman দ্বারা করা যায়, যেমন ডেটা ইম্পোর্ট, ডেটা আপডেট অথবা পরিসংখ্যান সম্পাদন ইত্যাদি। webman-এ এটি খুব সহজই সম্পাদন করা যায়, যেমন -

**নতুন তৈরি করুন `scripts/update.php`** (ফোল্ডার অনুপস্থিত হলে নিজে তৈরি করুন)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

এছাড়াও, আমরা `webman/console` ব্যবহার করেও এই ধরনের অপারেশন সম্পাদন করতে পারি, দেখুন [কনসোল কমান্ড](../plugin/console.md)।
