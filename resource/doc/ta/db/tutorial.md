# விரைவான தொடக்கம்

webman தரவுத்தளம் இயலும் இடமற்ற [illuminate/database](https://github.com/illuminate/database) ஐப் பயன்படுத்துகிறது, அது லாரவல் தரவுத்தளம் மாணவர்களுடன் பயன்பாடு செய்யும். 

உங்கள் கருவிகளை பயன்படுத்தி [பிற தரவுத்தள பொருட்கள் பயன்படுத்துவதில்](others.md) முதலில் சந்திக்க முடியும்.

## நிறுவுவது

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

நிறுவும் பிறகு restart மென்பொருளை restart செய்ய வேண்டும் (திரும்பப் போதாது)

> **குறிப்பு**
> பக்கான, தரவுத்தள நிகழ்வுகள், SQL அச்சங்களை அச்சிடும் போன்ற பொருள்களை தேவையில்லை என்றால்,
> `composer require -W illuminate/database` ஐச் செயல்படுத்திவிடலாம்.

## தரவுத்தள அமைப்பு
`config/database.php`
```php
return [
    // இயல்புநிலை தரவுத்தளம்
    'default' => 'mysql',

    // வேரு தரவுத்தள அமைப்புகள்
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

## பயன்பாடு
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
        return response("hello $name");
    }
}
```
