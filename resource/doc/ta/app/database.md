# தரவுத்தளத்தை
பிளகின் தரவுத்தளத்தை கட்டமைத்து, உங்கள் தரவுத்தளத்தை கட்டமைக்கலாம், உ஦ாஹரணமாக `plugin/foo/config/database.php` உள்ள உள்ளடக்கம் பொதுவாக இது போன்று இருக்கும்
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlஇணைப்பு பெயர்
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'தரவுத்தளம்',
            'username'    => 'பயனர்பெயர்',
            'password'    => 'கடவுச்சொல்',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // நிர்வாகம் இணைப்பு பெயர்
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'தரவுத்தளம்',
            'username'    => 'பயனர்பெயர்',
            'password'    => 'கடவுச்சொல்',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
மீனாவைகள்வார்`Db::connection('plugin.{பிளகின்}.{இணைப்புபெயர்}');` என்பது, உதாரணமாக
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

முதல் உதாரணத்தால் "admin" உள்ளிருந்து, உங்கள் முதுமை பிரகலிப்பியின் தரவுத்தளத்தைப் பெற, எந்தவேறு பிளகின் உண்மைப் பிரகலிப்பைப் பெற்றுக் கொண்டீர், உதாரணமாக
```php
use support\Db;
Db::table('user')->first();
// எ.கா. மாஸ்டர் பிரகலிப்பியின் அமின் இணைப்பும் உள்ளது
Db::connection('admin')->table('admin')->first();
```

## மாதிரிகளுக்கு தரவுத்தளத்தை கட்டமைத்தல்

நாங்கள் ஒவ்வொரு குறியீட்டிற்குமுன்பு ஐகான் நிர்வாகப் தரவுத்தளத்தை அமைத்து நாம் ஒரு பெயர் ஊக்மை `N${அனைத்துடன்}ட்பொ${சொર்வ்பொ}s` உபாத்தத்தின் பயன்படுத்துவதன் மூலம், உதாரணத்தினூடைய `N${அனைத்துடன்}ர${பொ}${சொર்வ்பொ}s` ஆக்க்கினை
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

இப்படியானால் பிளகின் உள்ள அனைத்து மாதிரிகளும் ஆதாரத்தைக் கப்பப்பான்டு, புளைமனம஍ முதுமை பிரகலிபியும் தரவுத்தளத்தை வழங்கும்ஃன்

## தரவுத்தளத்தை மீண்டும் பயன்படுத்துவது
முன்னுரிமையான பிரகலிப்பின் தரவுத்தள அமைப்பைக் கொண்டிருக்கலாம், இது [வெப்மன்அட்மின்](https://www.workerman.net/plugin/82)ஐ உள்ளடக்கியுள்ளமைந்தாக, அதிகரிப்பின் [வெப்மன்அட்மின்](https://www.workerman.net/plugin/82)ஐ உள்ளடக்கினைக்கிலும் அனுப்பலாம், உதாரணமாக
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
