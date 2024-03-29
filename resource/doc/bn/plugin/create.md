# বেসিক প্লাগইন তৈরি এবং প্রকাশ প্রস্তাব

## কার্যবিধি
1. ক্রস ওভার প্লাগইনের জন্য উপরের প্লাগইনটি তিনটে অংশে বিভক্ত হয়, একটি ক্রস-অর্ধমাধ্যমিক প্রোগ্রাম ফাইল, একটি মধ্যমবর্তী কনফিগারেশন ফাইল মিডলওয়ের.php, এবং ইনস্টল.php কমান্ড দ্বারা স্বয়ংক্রিয়ভাবে তৈরি করা হয়েছে।
2. আমরা কমান্ড ব্যবহার করে এই তিনটি ফাইলকে জড়িত করে প্যাকেজ করে এবং কম্পোজারে প্রকাশ করি।
3. যখন ব্যবহারকারী কম্পোজার ব্যবহার করে ক্রস-অর্ধমাধ্যমিক প্লাগইনটি ইনস্টল করে, প্লাগইনের ইনস্টল.php ক্রস-অর্ধমাধ্যমিক প্রোগ্রাম ফাইল এবং কনফিগারেশন ফাইলটি ` {মূল প্রকল্প}/config/plugin` ফোল্ডারে নকল করে দেবে, যাতে webman লোড করতে পারে। ক্রস-অর্ধমাধ্যমিক প্রোগ্রাম ফাইলের স্বয়ংক্রিয়ভাবে বৈষম্য সক্রিয় হয়।
4. ব্যবহারকারী যখন কম্পোজার দ্বারা প্লাগইনটি মুছে ফেলবে, তখনে ইনস্টল.php প্লাগইনটির ক্রস-অর্ধমাধ্যমিক প্রোগ্রাম ফাইল এবং কনফিগারেশন ফাইলের বাদিতা ডিলিট করবে, অভিন্যতা সক্রিয় করবে।

## প্রাণযোগ্যতা
1. প্লাগইনের নাম উভয় অংশের মধ্যে গঠিত, `উদ্যোগকারী` এবং `প্লাগইনের নাম`, উদাহরণস্বরূপ, `webman/push`, এটা কমান্সুর প্যাকেজ নামের সাথে সাথে সামঞ্জস্যপূর্ণ।
2. প্লাগইন কনফিগারেশন ফাইলটি একটি সামান্য গঠনে রাখা হয়, `config/plugin/উদ্যোগকারী/প্লাগইনের নাম/` এর মধ্যে (কনসোল কমান্ড অটোমেটিকলি কনফিগারেশন ডিরেক্টরি তৈরি করবে)। যদি প্লাগইনটি কনফিগার প্রয়োজন না করে, তবে তৈরি করা কনফিগারেশন ডিরেক্টরি মুছে ফেলা আবশ্যক।
3. প্লাগইন কনফিগারেশনের নির্দিষ্ট সেটিংগগুলির জন্য, শুধুমাত্র ফলাফলটি জানা ডাটাবেস, রাউটারসমূহ, মিডলওয়ের সেটিং, প্রসেস গুলির কনফিগারেশন, ডাটাবেস কনফিগারেশন, রেডিস কনফিগারেশন, থিংকঅরম কনফিগারেশন। এই কনফিগারেশনগুলি webman স্বয়ংক্রিয়ভাবে সনাক্ত করতে পারবে।
4. প্লাগইন সেটিং অনুভব করার জন্য নিম্নলিখিত উপায় ব্যবহার করে। `config('plugin.উদ্যোগকারী.প্লাগইনের নাম.কনফিগারেশন ফাইল.নির্দিষ্ট সেটিং');` যেমনঃ `config('plugin.webman.push.app.app_key')`
5.  প্লাগইনের যদি নিজের ডাটাবেস কনফিগারেশন থাকে, তাহলে নিম্নলিখিত উপায়ে প্রবেশ দেওয়া হবে। `Illuminate/database` হল `Db::connection('plugin.উদ্যোগকারী.প্লাগইনের নাম.নির্দিষ্ট কানেকশন')` এবং `thinkorm` হল `Db::connct('plugin.উদ্যোগকারী.প্লাগইনের নাম.নির্দিষ্ট কানেকশন')`
6.  যদি প্লাগইনটি `app/` ফোল্ডারে ব্যবসা ফাইল রাখতে চায়, তবে নিশ্চিত করা উচিত যে এটি ব্যবহারকারীর প্রকল্প ও অন্যান্য প্লাগইনগুলির সাথে সংঘটিত নয়।
7. প্লাগইন প্রধান প্রকল্পে ফাইল বা ফোল্ডার কপি করার জন্য, প্লাগইনের মধ্যে কনফিগারেশন ফাইলের সাথে সাথে ক্রস-অর্ধমাধ্যমিক প্রোগ্রাম ফাইলটি বিভাগে রাখা উচিত, মুখ্য প্রকল্পে কপি করার প্রয়োজন নাই।
8. প্লাগইনের নেমস্পেসে বড় অক্ষর ব্যবহার করা প্রয়োজন, উদাহরণস্বরূপ Webman/Console.
## উদাহরণ

**`webman/console` command line ইনস্টল করুন**

`composer require webman/console`

#### প্লাগইন তৈরি করুন

নাম ধরে যদি প্লাগইন তৈরি করতে হয় `foo/admin` (নামটি হলো পরবর্তীতে কম্পোজারে প্রকাশের প্রকল্পের নাম, এটি ছোট হাতে লেখা হতে হবে)
কমান্ড চালান
`php webman plugin:create --name=foo/admin`

প্লাগইন তৈরির পরে `vendor/foo/admin` নামক ডিরেক্টরি এবং `config/plugin/foo/admin` নামক ডিরেক্টরি তৈরি হবে যেখানে প্লাগইন সম্পর্কিত ফাইল এবং কনফিগারেশন সংরক্ষণ করা হবে।

> মনে রাখবেন
> `config/plugin/foo/admin` এ নিম্নলিখিত কনফিগারেশন সমর্থন করা হচ্ছে, app.php প্লাগইন মূল কনফিগারেশন, bootstrap.php প্রসেস চালু করুন কনফিগারেশন, route.php রাউট কনফিগারেশন, middleware.php মিডলওয়্যার কনফিগারেশন, process.php কাস্টম প্রসেস কনফিগারেশন, database.php ডাটাবেস কনফিগারেশন, redis.php রেডিস কনফিগারেশন, thinkorm.php থিংকতোার্ম কনফিগারেশন। কনফিগারেশন ফরম্যাটটি webman এর সাথে মিলিত এবং এই কনফিগারেশনগুলি webman এই স্বয়ংক্রিয়ভাবে সনাক্ত এবং সংযুক্ত হবে। ব্যবহার করার সময় 'plugin' কে আগে যান, উদাহরণস্বরূপ config('plugin.foo.admin.app');

#### প্লাগইন রপ্তানি করুন

প্লাগইন ডেভেলপমেন্ট নির্দিষ্ট করে গতকাল করে, নিম্নলিখিত কমান্ড রান করুন
`php webman plugin:export --name=foo/admin`

> স্পষ্টতা
> রপ্তানির পরে config/plugin/foo/admin ডিরেক্টরি টি vendor/foo/admin/src এ কপি করা হয় এবং একই সাথে একটি Install.php স্বয়ংক্রিয়ভাবে স্থাপনা এবং স্বয়ংক্রিয়ভাবে সরানো সময় কিছু অপারেশন চালাতে।
> সাধারণভাবে স্থাপনার অপারেশনটি হলো বেন্ডর/foo/admin/src এর কনফিগারেশন বর্তমান প্রকল্পের config/plugin এ কপি করা।
> রিমুভ করার সাধারণ অপারেশন হলো বর্তমান প্রকল্পের config/plugin এর কনফিগারেশন ফাইল মুছে ফেলা।
> আপনি Install.php পরিবর্তন করতে পারেন যাতে স্থাপনা এবং আনইন্স্টলেশন সময়ে কিছু কাস্টম অপারেশন চালাতে পারেন।

#### প্লাগইন সাবমিট করুন
* ধরা দিক আপনি ইতিমধ্যে [গিটহাব](https://github.com) আর [প্যাকেজিস্ট](https://packagist.org) অ্যাকাউন্ট রাখেন
* [গিটহাব](https://github.com) এ একটি admin প্রকল্প তৈরি করুন এবং কোড আপলোড করুন, প্রজেক্টের ঠিকানা ধরে নেওয়া হলো `https://github.com/আপনার-ইউজারনেম/admin`
* ঠিকানা যান `https://github.com/আপনার-ইউজারনেম/admin/releases/new` এবং যেমনটি উহারে রিলিজ পাবলিশ করুন যেমন `v1.0.0`
* [প্যাকেজিস্ট](https://packagist.org) এ যান এবং ন্যাভিগেশনে `সাবমিট` ক্লিক করুন, আপনার গিটহাব প্রজেক্টের লিঙ্ক `https://github.com/আপনার-ইউজারনেম/admin` সাবমিট করুন এবং এভাবে একটি প্লাগইন প্রকাশ করার প্রক্রিয়া সম্পন্ন হবে।

> **পরামর্শ**
> যদি `প্যাকেজিস্ট` এ প্লাগইন সাবমিট দেওয়ায় সমস্যা হয়, তবে আপনি একটি নতুন ব্র্যান্ডের নাম নিতে পারেন, উদাহরণস্বরূপ `foo/admin` যেটি পরে `myfoo/admin` নাম নিলে হারবে।

পরবর্তীতে যখন আপনার প্লাগইন প্রজেক্টের কোড আপডেট হবে, আপনার কোডগুলির সমস্ত আপডেট গিটহাব এ সিঙ্ক্ করতে হবে, এবং পুনরায় ঠিকানা যান `https://github.com/আপনার-ইউজারনেম/admin/releases/new` এবং `v1.0.0` অ্যাড করেন, পরবর্তীতে `https://packagist.org/packages/foo/admin` পৃষ্ঠায় `আপডেট` বোতামে ক্লিক করেন।
## প্লাগইনে কমান্ড যোগ করুন
কখনও কখনও আমাদের প্লাগইনগুলি কিছু অতিরিক্ত ফাংশনালিটি প্রদান করতে কিছু কাস্টম কমান্ড প্রয়োজন করে, উদাহরণস্বরূপ যখন `webman/redis-queue` প্লাগইনটি ইনস্টল করা হবে, তখন প্রকল্পে স্বয়ংক্রিয় ভাবে `redis-queue:consumer` কমান্ড যোগ করা হবে, এবং ব্যবহারকারীদের কেবল মাত্র `php webman redis-queue:consumer send-mail` চালিয়ে দিলে প্রকল্পে একটি SendMail.php কনসিউমার ক্লাস তৈরি হবে, এটা দ্রুত উন্নতির জন্য সাহায্যকর। 

আপনি যদি `foo/admin` প্লাগইনে `foo-admin:add` কমান্ড যোগ করতে চান, তাহলে নিম্নলিখিত পদক্ষেপগুলি অনুসরণ করুন।

#### কমান্ড তৈরি করুন

**কমান্ড ফাইল তৈরি করুন `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'এখানে কমান্ডের বিবরণ';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'যোগ করুন নাম');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("অ্যাডমিন যোগ করুন $name");
        return self::SUCCESS;
    }

}
```

> **দ্রষ্টব্য**
> প্লাগইনের মধ্যে কমান্ড কনফ্লিক্ট থেকে বাঁচার জন্য, কমান্ড লাইন ফরম্যাট সুপারিশিত `ম্যানুফ্যাক্চারার-প্লাগইন-নাম: নির্দিষ্ট কমান্ড` যেমন `foo/admin` প্লাগইনের সব কমান্ডগুলির জন্য, `foo-admin:` অবশ্যই প্রিফিক্স হতে হবে, উদাহরণস্বরূপ `foo-admin:add`। 

#### কনফিগারেশন যোগ করুন
**কনফিগারেশন তৈরি করুন `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....এখানে অনেক গুলো কনফিগারেশন যোগ করা যাবে...
];
```

> **পরামর্শ**
> `command.php` প্লাগইনের কাস্টম কমান্ড কনফিগারেশনের জন্য, প্রতিটি ক্লাস ফাইলটির জন্য একটি অ্যারে যা কমান্ড ফাইল প্রতিটি উইজট একটি কমান্ডের সাথে মিলানো। ব্যবহারকারী যখন কমান্ড লাইন চালায়, তখন `webman/console` প্লাগইনের `command.php` ফাইলে নির্ধারিত কাস্টম কমান্ড লোড করবে।  কমান্ড লাইন সম্পর্কিত অধিক জানতে [কমান্ড লাইন](console.md) দেখুন।

#### রপ্তানি করুন
`php webman plugin:export --name=foo/admin` কমান্ড অনুষ্ঠান করুন এবং প্লাগইনটি রপ্তানি করুন, এবং `packagist` এ জমা দিন। এভাবে ব্যবহারকারী `foo/admin` প্লাগইন ইনস্টল করার পরে, একটি `foo-admin:add` কমান্ড যোগ হবে। `php webman foo-admin:add jerry` চালালে `অ্যাডমিন যোগ করুন jerry` প্রিন্ট হবে।
