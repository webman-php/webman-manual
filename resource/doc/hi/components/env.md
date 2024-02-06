# vlucas/phpdotenv

## विवरण
`vlucas/phpdotenv` एक environment variable लोडिंग component है, जो विभिन्न environments (जैसे डेवलपमेंट, टेस्टिंग, आदि) की कॉन्फ़िगरेशन को अलग करने के लिए उपयोग किया जाता है।

## प्रोजेक्ट लिंक

https://github.com/vlucas/phpdotenv
  
## स्थापना
 
```php
composer require vlucas/phpdotenv
 ```
  
## उपयोग

#### प्रोजेक्ट रूट डायरेक्टरी में नया `.env` फ़ाइल बनाएं
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### कॉन्फ़िगरेशन फ़ाइल में परिवर्तन
**config/database.php**
```php
return [
    // डिफ़ॉल्ट डेटाबेस
    'default' => 'mysql',

    // विभिन्न डेटाबेस कॉन्फ़िगरेशन
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

> **सुझाव**
> `.env` फ़ाइल को `.gitignore` लिस्ट में जोड़ना सुझाव दिया जाता है, कोड रिपॉज़िटरी में सबमिट करने से बचें। प्रोजेक्ट में `.env.example` कॉन्फ़िगरेशन सैंपल फ़ाइल जोड़ें, जब प्रोजेक्ट डिप्लॉय होता है तो `.env.example` की फ़ाइल को `.env` के रूप में कॉपी करें, और मौजूदा वातावरण के हिसाब से `.env` में कॉन्फ़िगरेशन को संशोधित करें, इससे प्रोजेक्ट को विभिन्न environments में अलग-अलग कॉन्फ़िगर करने की सुविधा रहती है।

> **ध्यान दें**
> `vlucas/phpdotenv` PHP TS version (Thread Safe version) में बग हो सकता है, कृपया NTS version (Non-Thread Safe version) का उपयोग करें।
> वर्तमान में php कौन सी version है यह `php -v` कमांड से देखा जा सकता है।

## अधिक जानकारी

https://github.com/vlucas/phpdotenv
