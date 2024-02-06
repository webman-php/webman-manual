# vlucas/phpdotenv

## বর্ণনা
`vlucas/phpdotenv` হল একটি বাতিল লোড করার জন্য একটি পরিবেশ চেক কম্পোনেন্ট, যা বিভিন্ন পরিবেশ (যেমন ডেভেলপমেন্ট পরিবেশ, পরীক্ষা পরিবেশ ইত্যাদি) এর কনফিগারেশন পৃথক করতে ব্যবহৃত হয়।

## প্রকল্পের ঠিকানা

https://github.com/vlucas/phpdotenv
  
## ইনস্টলেশন
 
```php
composer require vlucas/phpdotenv
 ```
  
## ব্যবহার

#### প্রজেক্ট রুট ফোল্ডারে নতুন করে **.env** ফাইল তৈরি করুন
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = পরীক্ষা
DB_USER = ফু
DB_PASSWORD = 123456
```

#### কনফিগারেশন ফাইল পরিবর্তন করুন
**config/database.php**
```php
return [
    // ডিফল্ট ডেটাবেস
    'default' => 'mysql',

    // বিভিন্ন ডেটাবেস কনফিগারেশন
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
> .env ফাইলটি .gitignore তালিকায় যোগ করা পরামর্শ দেয়া হয়, যাতে কোড ভান্ডারে যোগ করা না যায়। প্রজেক্টে .env.example কনফিগারেশন ধারণ ফাইলটি যোগ করা হয় যখন প্রজেক্ট ডিপ্লয় করা হয়, .env.example ফাইলটি .env হিসেবে কপি করে, বর্তমান পরিবেশে সেটিংস পরিবর্তন করে, এভাবে প্রজেক্টটি বিভিন্ন পরিবেশে বিভিন্ন কনফিগারেশন লোড করতে।

> **লক্ষ্য করুন**
> `vlucas/phpdotenv` PHP TS সংস্করণে (সেম সম্পূর্ণ সুরক্ষিত ভার্সন ইল) বাগ থাকতে পারে, দয়া করে NTS সংস্করণ (ননট্রেডা সুরক্ষিত সংস্করণ) ব্যবহার করুন।
> বর্তমানে পিএইচপি কোন সংস্করণ তা দেখতে `php -v` কমান্ড চালিয়ে নেওয়া যায়। 

## অধিক তথ্য

https://github.com/vlucas/phpdotenv
