# প্রসেস মনিটরিং
ওয়েবম্যান নিজস্বভাবে একটি মনিটর প্রসেস সহ। এটি দুটি কাজ সমর্থন করে
1. ফাইল আপডেট মনিটর এবং নতুন ব্যবসায়িক কোড স্বয়ংক্রিয়ভাবে পুনরায় লোড করতে(সাধারণভাবে এটি ডেভেলপমেন্ট সময়ে ব্যবহার হয়)
2. সমস্ত প্রসেসের মেমরি অধিকরণ মনিটরিং, যদি কোনও প্রসেস মেমরি সীমা অতিক্রম করতে চলে, তাহলে স্বয়ংক্রিয়ভাবে ঐ প্রসেসটি সুরক্ষাত্মকভাবে পুনরায় চালু করা হয়(ব্যাবসায়িক কার্যকলাপে কোনও প্রভাব নেই)

### মনিটর কনফিগারেশন
`config/process.php` কনফিগারেশন ফাইলে `monitor` কনফিগারেশন থাকে
```php
global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([    // Which directories to monitor
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Files with these suffixes will be monitored
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Whether to enable file monitoring
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Whether to enable memory monitoring
            ]
        ]
    ]
];
```
`monitorDir` কোন ডিরেক্টরি মনিটর করতে হবে তা কনফিগার করার জন্যে।(প্রসেসের ডিরেক্টরি ফাইলগুলি খুব বেশি থাকা উচিত না)।
`monitorExtensions` `monitorDir` ডিরেক্টরির কোন ফাইলের শেষে প্রস্তুতি হওয়া উচিত।
`options.enable_file_monitor` এর মান `true` হলে, তাহলে ফাইল আপডেট মনিটরিং(লিনাক্স সিস্টেমে ডিবাগ মোডে চালানো হয় অপশনালি ফাইল মনিটরিং চালানো হয়)।
`options.enable_memory_monitor` এর মান `true` হলে, তাহলে মেমরি ব্যবহার মনিটরিং(মেমরি মনিটরিং উইন্ডোস সিস্টেমে সমর্থিত নয়)।

> **পরামর্শ**
> ওয়েন্ডোস সিস্টেমে `windows.bat` বা `php windows.php` চালানোর সময় ফাইল আপডেট মনিটরিং চালানোর জন্য প্রয়োজন।
