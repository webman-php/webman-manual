# পেমেন্ট SDK (V3)

## প্রকল্প ঠিকানা

https://github.com/yansongda/pay

## ইন্সটলেশন

```php
composer require yansongda/pay ^3.0.0
```

## ব্যবহার

> বিঃদ্রঃ: নিচের ডকুমেন্টেশন স্যান্ডবক্স পরিবেশটির জন্য লেখা হয়েছে, যদি কোন সমস্যা হয়, তাহলে তা দ্রুত ফিডব্যাক দিন!

### কনফিগারেশন ফাইল

নিম্নলিখিত কনফিগারেশন ফাইল `config/payment.php` থাকলে:

```php
<?php
/**
 * @desc পেমেন্ট কনফিগারেশন ফাইল
 * @author টিনিওয়ান(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // প্রয়োজনীয় - অ্যাপ আইডি বিন্যাস করা অ্যাপ আইডি
            'app_id' => '20160909004708941',
            // প্রয়োজনীয়-অ্যাপ গোপনীয় পাঠ স্ট্রিং বা পথ
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // প্রয়োজনীয়-অ্যাপ পাবলিক সার্টিফিকেট পথ
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // প্রয
