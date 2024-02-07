## Medoo

Medoo hafif bir veritabanı işlem eklentisidir, [Medoo resmi web sitesi](https://medoo.in/).

## Kurulum
`composer require webman/medoo`

## Veritabanı Yapılandırması
Yapılandırma dosyası, `config/plugin/webman/medoo/database.php` konumundadır.

## Kullanım
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

> **İpucu**
> `Medoo::get('user', '*', ['uid' => 1]);`
> şuyla aynıdır
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Birden Fazla Veritabanı Yapılandırması

**Yapılandırma**
`config/plugin/webman/medoo/database.php` içine yeni bir yapılandırma ekleyin, key isteğe bağlıdır, burada `other` kullanılmıştır.

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
    // Burada yeni bir 'other' yapılandırması eklendi
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

**Kullanım**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Detaylı Belgeler
Bkz. [Medoo resmi belgeler](https://medoo.in/api/select)
