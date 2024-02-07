## மீடூ

மீடூ என்பது ஒரு எளிதான வடிவமைப்பு தரவு இயக்க செயலி, [மீடூ அதிகாரப்பூர்வ இணைப்பு](https://medoo.in/).


## நிறுவுதல்
`composer require webman/medoo`


## தரவுத்தள அமைப்பு
அமைப்பு கோப்பு இருக்கின்ற உலாவை `config/plugin/webman/medoo/database.php`.


## பயன்பாடு
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **குறிப்பு**
> `Medoo::get('user', '*', ['uid' => 1]);`
> பொருத்தம்படுகின்றது
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`


## பல தரவுத்தள அமைப்பு

**அமைப்பு**  
புகழ்பிரி `/config/plugin/webman/medoo/database.php` இல் ஒரு வசதியை பதிவு செய்து, விசைகள் எதிர்பாக, இத்தளத்தில் `பல`.


```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    // இங்கு பின்னணி அமைப்பைச் சேர்த்தது
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
];
```


**பயன்பாடு**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```


## விரிவான ஆவணதிகை
காணலாம் [மீடூ அதிகாரபூர்வ ஆவணம்](https://medoo.in/api/select)
