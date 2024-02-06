رصد العمليات
يأتي webman بعملية رصد مدمجة، وهي تدعم وظيفتين
1. رصد تحديثات الملفات وإعادة تحميل تلقائي لشفرات الأعمال الجديدة (يتم استخدامها عادة أثناء التطوير)
2. رصد استهلاك ذاكرة جميع العمليات، إذا كانت أي عملية تقترب من تجاوز الحد المسموح به في `memory_limit` في `php.ini`، فسيتم إعادة تشغيل العملية بأمان تلقائيًا (دون التأثير على الأعمال)

### تكوين الرصد
الملف `config/process.php` يحتوي على تكوين`monitor`
```php

global $argv;

return [
    // كشف تحديثات الملفات وإعادة تحميل تلقائي
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // راقب هذه الدلائل
            'monitorDir' => array_merge([   
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // سيتم مراقبة الملفات ذات هذه اللاحقات
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // تمكين رصد الملف
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // تمكين رصد الذاكرة
            ]
        ]
    ]
];
```
`monitorDir` مستخدمة لتهيئة أي الدلائل يجب مراقبتها بالتحديثات (يجب تجنب مراقبة الكثير من ملفات الدلائل) 
`monitorExtensions` تستخدم لتكوين أي لاحقات للملفات في الدلائل المُراقبة.
`options.enable_file_monitor` عندما يكون القيمة `true`، سيتم تفعيل مراقبة تحديثات الملفات (يُفترض تفعيل مراقبة الملفات تلقائيًا عند التشغيل بوضع debug في نظام لينكس)
`options.enable_memory_monitor` عندما تكون القيمة `true`، سيتم تفعيل رصد استهلاك الذاكرة (رصد استهلاك الذاكرة غير مدعوم في نظام ويندوز)

> **نصيحة**
> عند تشغيل `windows.bat` أو `php windows.php` في نظام ويندوز، ستحتاج إلى تفعيل مراقبة تحديثات الملفات.
