# तेज़ शुरू

webman डेटाबेस डिफ़ॉल्ट रूप से [illuminate/database](https://github.com/illuminate/database) का उपयोग करता है, जो कि [laravel डेटाबेस](https://learnku.com/docs/laravel/8.x/database/9400) है, इसका उपयोग लैरेवल के समान है।

बेशक, आप [अन्य डेटाबेस कॉम्पोनेंट का उपयोग](others.md) करने के लिए "उपयोग करने के लिए" अध्याय में ThinkPHP या अन्य डेटाबेस का उपयोग कर सकते हैं।

## स्थापना

`कॉम्पोज़र डिमांड -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

स्थापना के बाद पुनः शुरू करने की आवश्यकता होगी (रीलोड अमान्य है)

> **सूचना**
> यदि पृष्ठभाग, डेटाबेस घटनाएँ, SQL छापा नहीं चाहिए, तो केवल यह कार्रवाई करनी होगी
> `कॉम्पोज़र डिमांड -W illuminate/database`

## डेटाबेस कॉन्फ़िगरेशन
`config/database.php`
```php

return [
    // डिफ़ॉल्ट डेटाबेस
    'default' => 'mysql',

    // विभिन्न डेटाबेस कॉन्फ़िगरेशन
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```


## उपयोग
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("नमस्ते $name");
    }
}
```
