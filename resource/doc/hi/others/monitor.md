# प्रक्रिया मॉनिटरिंग
webman में एक मॉनिटरिंग प्रक्रिया शामिल है, जो दो सुविधाएँ समर्थित करता है
1. फ़ाइलों की मॉनिटरिंग और स्वचालित पुनर्लोड नए व्यावसायिक कोड को लोड करें (आमतौर पर विकास के दौरान उपयोग होता है)
2. सभी प्रक्रियाओं के आपातकालीन पुनरारंभ करने से पहले मेमोरी का उपयोग मॉनिटर करें, यदि कोई प्रक्रिया `php.ini` में `memory_limit` सीमा से अधिक मेमोरी का उपयोग कर रही है तो उस प्रक्रिया को स्वचालित रूप से सुरक्षित रीस्टार्ट करें (व्यावसायिक परिणाम को प्रभावित नहीं करें)

## मॉनिटरिंग कॉन्फ़िगरेशन
`config/process.php` कॉन्फ़िगरेशन फ़ाइल में `monitor` कॉन्फ़िगरेशन होती है।


```php

global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            'monitorDir' => array_merge([
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', 
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      
            ]
        ]
    ]
];
```
## महत्वपूर्ण कॉन्फ़िगरेशन विवरण

`monitorDir` का उपयोग किस प्रकार के निरीक्षण के लिए किया जाता है, इसमें कौन से डायरेक्टरी का अद्यतन किया जाना चाहिए होता है(फ़ाइलों की निरीक्षण की दृष्टि से बहुत सी फ़ाइलें नहीं होनी चाहिए)।
`monitorExtensions` उसी डायरेक्टरी में कौन से फ़ाइल एक्सटेंशन को निरीक्षण करना चाहिए, इसे कॉन्फ़िगर करने के लिए किया जाता है।
`options.enable_file_monitor` का मान `true` होता है, तो फ़ाइल अद्यतन निरीक्षण को चालू किया जाता है(लिनक्स सिस्टम पर डिबग मोड में चलाने पर डिफ़ॉल्ट रूप से फ़ाइल निरीक्षण को चालू किया जाता है)।
`options.enable_memory_monitor` का मान `true` होता है, तो मेमोरी उपयोग निरीक्षण को चालू किया जाता है(मेमोरी उपयोग निरीक्षण विंडोज सिस्टम को समर्थन नहीं करता)।

> **सुझाव**
> विंडोज सिस्टम पर जब फ़ाइल अद्यतन निरीक्षण को चालू करने की आवश्यकता होती है, तो तब `windows.bat` या `php windows.php` चलाने की आवश्यकता होती है।
