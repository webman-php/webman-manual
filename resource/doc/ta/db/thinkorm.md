## திங்க்ORM

### திங்க்ORM நிறுவவும்

`composer require -W webman/think-orm`

நிறுவிய பின் மீண்டும் ஏற்றுமதியின் பொத்தாயமாக இருக்க வேண்டும் (reload பயனக்கானது வேறு)

> **எச்சரிக்கை**
> நீங்கள் காம்போசர் புரோகிடையை பயன்படுத்தியிருந்தால், `composer config -g --unset repos.packagist` ஐ இயக்கி காம்போசர் புரோகிடையை இல்லையானதாக மாற்றவும்

> [webman/think-orm](https://www.workerman.net/plugin/14) வான்னுருநணைவாதமாக்கும் `toptink/think-orm` ஐ ஆன்மாக்க ஒரு தானியங்கி நிறுவுகின்றது, உங்கள் webman பதிப்பு `1.2` க்குக்குள் நிறைய ஆன்மாக்கம் பயன்படுத்த முடியாது என்பதை பார்க்கவும் [தினமானமாக நிறுவுகின்றது மற்றும் கட்டமை செய்வதை](https://www.workerman.net/a/1289) படிவத்தை பார்க்கவும்.

### கட்டமை கோப்பு
வாரிபணியின் நிலையான நெறிமுறைகளை மாற்ற கோப்பு `config/thinkorm.php` ஐ திருத்தவும்

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

### மாதிரி உருவாக்க
திங்க்ORM மாதிரிய் `think\Model` ஐக் கொண்டிருக்கிறது, போன்று உள்ளது
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * அமைப்பு உட்பொதி கொண்டு உள்ள அட்டவணை.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * அடிப்படை விசாரித்த தொகுப்பினுடைய முக்கிய விசாரித்த கொண்டு உள்ளது.
     *
     * var string
     */
    protected $pk = 'id';

    
}
```

நீங்கள் இதே வழியில் மாதிரி வெற்றியாக் உருவாக்கலாம்
```
php webman make:model அட்டவணைப் பெயர்
```

> **எச்சரிக்கை**
> இந்த கொள்கைனை நிறுத்தி`webman/console` ஐ நிறுவியிருந்தான்கற்று, நிறுத்த கொள்கை `composer require webman/console ^1.2.13` ஆகும்

> **குறிக்கோவது**
> make:model கொள்கை முன்பஅத்தியான மாதிரியாய் `illuminate/database` ஐ பயன்படுத்துகிறதாக கணிக்கப்பட்டதாக காணப்படகும், அதில்லாமல் think-orm மாதிரி உருவாக்குவதில், இந்த பரமெட்டரை சேர்த்து இயக்க உதவுகின்றது, கொள்கைக்குப் பதிலளிப்பவார் `php webman make:model அட்டவணைப் பெயர் tp` (இதன் வேறுபட்டதானதை அறியக்கூடாது அப்படியானது `webman/console` ஐ மேம்படுத்துவதைந்தை)
