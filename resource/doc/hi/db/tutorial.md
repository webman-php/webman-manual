# त्वरित शुरू

वेबमैन डेटाबेस डिफ़ॉल्ट रूप से [Illuminate/database](https://github.com/illuminate/database) का उपयोग करता है, यानी [लारावेल डेटाबेस](https://learnku.com/docs/laravel/8.x/database/9400), जिसका उपयोग भी लारावेल में किया जाता है।

आप अन्य डेटाबेस कॉम्पोनेंट का उपयोग करने के लिए [अन्य](others.md) अध्याय का उपयोग कर सकते हैं, जैसे ThinkPHP या अन्य डेटाबेस का उपयोग करना।

## स्थापना

`कंपोजर स्थापित करें -डब्ल्यू illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

स्थापना के बाद पुन: शुरू करने की आवश्यकता होगी (रिलोड वैध नहीं है)

> **टिप्पणी**
> यदि पेजिनेशन, डेटाबेस इवेंट, SQL प्रिंट की आवश्यकता नहीं है, तो केवल निम्न अमल करना होगा
> `कंपोजर स्थापित करें -डब्ल्यू illuminate/database`

## डेटाबेस सेटअप
`config/database.php`
```php
return [
    // डिफ़ॉल्ट डेटाबेस
    'default' => 'mysql',

    // विभिन्न डाटाबेस सेटअप
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
