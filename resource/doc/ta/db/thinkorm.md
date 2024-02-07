## திங்ORM

### திங்ஆர்எம் நிறுவற்கள்

`composer require -W webman/think-orm`

நிறுவற்கள் பிறகு மீளமைக்க(reload செய்ய மாறும்)

> **குறிப்பு**
> நீங்கள் காம்போஸர் மதிப்பைப் பயன்படுத்தியிருந்தால், `composer config -g --unset repos.packagist` கட்டவும் என்ற கட்டளை இயக்கி முயன்று பார்க்கவும்

> [webman/think-orm](https://www.workerman.net/plugin/14) என்பது வானின்விடா`toptink/think-orm` ஐ தானாக நிறுவிக்கும் ஒரு செயலி, உங்கள் webman பதிப்பு `1.2` - க்கு குறைவானதானாக உள்ளதா என்பதை அனுமதிக்காததால் பின் கட்டுரை[கைபேசி நிறுவுக்குறிகள் think-orm](https://www.workerman.net/a/1289) ஐ பார்ங்கள். 

### கட்டுப்பாட்டு கோவை
உங்கள் உணர்வு உருவாக்க கோவை கோவை `config/thinkorm.php` ஐ மாற்றவும்

### பயன்பாடு
```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### மாதிரி உருவாக்கல்

ThinkOrm மாதிரி`think\Model` - ஐ போல் பிரிக்கின்றது
```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

நீங்கள் thinkorm படிப்பீடுகளின் மேலி `make:model` உபயோகிக்கலாம்
```php
php webman make:model அட்டைக்கோவை
```

> **குறிப்பு**
> இந்த கூண்டு `webman/console` - ஐ நிறுவ வேண்டும், நிறுவுவதற்கு கூண்டு `composer require webman/console ^1.2.13` ஆகும்

> **குறிப்பு**
> மாதிரி செய்தியைக் காணவும் `illuminate/database` உபயோகிக்கும் முக்கிய திட்டத்தை அடைந்திருப்பதானால், திங்-ஓஆர்எம் உபயோகித்துகொண்டு மாதிரி கோவையை உருவாக்குகின்றது, இந்த போக்கிற்கு `tp` ஒன்றை ஒதுக்க முடியும்`make:model` என்னை நெறிமுடிக்க(http://இயலவில்இயலும்உழைப்புவழியே`-இல் மாற்றம்இல்லைஎன்று உறுதி செய்தால்)
