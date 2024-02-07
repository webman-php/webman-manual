# vlucas/phpdotenv

## বর্ণনা
`vlucas/phpdotenv` হল একটি পরিবেশ চেপার অনুষ্ঠান, যা বিভিন্ন পরিবেশ (যেমন, ডেভেলপমেন্ট, টেস্টিং ইত্যাদি) এর কনফিগারেশন এর মধ্যে পার্থক্য করার জন্য ব্যবহৃত হয়।

## প্রজেক্ট লিঙ্ক

https://github.com/vlucas/phpdotenv
  
## ইনস্টলেশন
 
```php
composer require vlucas/phpdotenv
 ```
  
## ব্যবহার

#### প্রজেক্ট রুট ডিরেক্টরিতে নতুন `.env` ফাইল তৈরি করুন
**.env**
```text
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### কনফিগারেশন ফাইল পরিবর্তন করুন
**config/database.php**
```php
return [
    // ডিফল্ট ডাটাবেস
    'default' => 'mysql',

    // বিভিন্ন ডাটাবেস কনফিগারেশন
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **পরামর্শ**
> `.env` ফাইলটি `.gitignore` তালিকাতে যোগ করা পরামর্শ দেওয়া হচ্ছে, যাতে কোড ভাণ্ডারে পোস্ট না হয়। প্রজেক্ট ভাণ্ডারে `.env.example` কনফিগারেশন নমুনা ফাইল যোগ করা উচিত, প্রজেক্ট ডিপ্লয়মেন্ট হলে `.env.example` ফাইলটি `.env` হিসেবে কপি করে, বর্তমান পরিবেশে খবর মোতাবেক `.env` এর কনফিগারেশন পরিবর্তন করা যায়। এরকম করে প্রজেক্টটি বিভিন্ন পরিবেশে ভিত্তিভুক্ত কনফিগারেশন লোড করতে পারে।

> **মন্তব্য**
> `vlucas/phpdotenv` PHP TS সংস্করণে (থ্রেড সुরক্ষিত সংস্করণ) বাগ থাকতে পারে, NTS ভার্সন (নন-থ্রেড সুরক্ষিত সংস্করণ) ব্যবহার করা উচিত।
> এখন চলতি কোন পিএইচপি সংস্করণটি দেখতে নিচের কমান্ডটি প্রয়োগ করুন `php -v` 

## আরও তথ্য

https://github.com/vlucas/phpdotenv
