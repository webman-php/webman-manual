# vlucas/phpdotenv

## संकेतन
`vlucas/phpdotenv` एक वातावरणीय वेरिएबल लोडिंग कंपोनेंट है, जो विभिन्न वातावरण (जैसे डेवलपमेंट, परीक्षण आदि) के कॉन्फ़िगरेशन को पृथक करने के लिए उपयोग किया जाता है।

## प्रोजेक्ट लिंक

https://github.com/vlucas/phpdotenv

## स्थापना
 
```php
composer require vlucas/phpdotenv
 ```

## उपयोग

#### प्रोजेक्ट रूट फ़ोल्डर में `.env` फ़ाइल नई बनाएं
**.env**
```plaintext
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### कॉन्फ़िगरेशन फ़ाइल में बदलाव करें
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
> `.env` फ़ाइल को `.gitignore` सूची में शामिल करना सिफ़र करना, कोड रि‍थकोष में सबमिट कि‍जीयेगा। कोड रि‍थकोष में एक `.env.example` कॉन्िफ़िगरेशन सैंपल फ़ाइल जोड़ें, जब प्रोजेक्ट डिप्लाइ किया जाए, तो  - `.env.example` को `.env` के रूप में कॉपी करें, और वर्तमान पर्यावरण के अनुसार `.env` में कॉन्फ़िगरेशन को संशोधित करें, इस प्रकार से प्रोजेक्ट को विभिन्न पर्यावरणों में विभिन्न कॉन्फ़िगरेशन लोड करने की अनुमति होगी।

> **ध्यान दें**
> `vlucas/phpdotenv` PHP TS संस्करण (स्थानीय सुरक्षित संस्करण) में बग हो सकता है, कृपया NTS संस्करण (गैर-स्थानीय सुरक्षित संस्करण) का उपयोग करें।
> वर्तमान php संस्करण को निम्न द्वारा देखा जा सकता है `php -v` कमांड को चलाकर

## अधिक जानकारी

https://github.com/vlucas/phpdotenv
