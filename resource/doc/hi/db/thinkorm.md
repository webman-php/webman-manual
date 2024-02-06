## थिंकORM

### थिंकORM का इंस्टॉल करें

`कंजमूर रिक्वाईर -W webman/think-orm`

इंस्टॉलेशन के बाद रिस्टार्ट करने की आवश्यकता होती है (रीलोड नहीं)।

> **सुझाव**
> यदि इंस्टॉलेशन असफल हो जाती है, तो यह संभावना है कि आपने कंजमूर प्रॉक्सी का उपयोग किया है, कृपया `कंजमूर कंफ़िग -जी --अनसेट repos.packagist` कमांड चलाकर कंजमूर प्रॉक्सी को नष्ट करें।

> [webman/think-orm](https://www.workerman.net/plugin/14) वास्तव में एक `toptink/think-orm` की स्वचालित इंस्टॉलेशन करने वाला प्लगइन है, यदि आपका webman संस्करण `1.2` से कम है तो यह प्लगइन इस्तेमाल नहीं कर सकते हैं, कृपया [मैन्युअलता से think-orm की इंस्टॉलेशन और सेटअप](https://www.workerman.net/a/1289) लें।

### कॉन्फ़िग फ़ाइल
वास्तविक स्थिति के अनुसार `कंफ़िग / thinkorm.php` फ़ाइल में बदलाव करें।

### उपयोग

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

### मॉडल बनाएं

ThinkOrm मॉडल `think\Model` से वंशीकृत होता है, जैसा कि नीचे दिखाया गया है
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * मॉडल के साथ जुड़ी गई तालिका।
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * तालिका के साथ जुड़ा प्राथमिक कुंजी।
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

आप नीचे दिए गए कमांड का उपयोग करके thinkorm पर आधारित मॉडल बना सकते हैं
```
php कंजमूर make:model तालिका नाम
```

> **सुझाव**
> यह कमांड इंस्टॉल करने की आवश्यकता होती है `webman/console`, इंस्टॉलेशन कमांड है `कंजमूर रिक्वाईर webman/console ^1.2.13`

> **ध्यान दें**
> make:model कमांड यदि प्रमुख प्रोजेक्ट में `illuminate/database` का उपयोग करते हुए पता लगाता है, तो यह `illuminate/database` पर आधारित मॉडल फ़ाइलें बनाएगा, और think-orm पर आधारित नही, इस समय आप tp नामक एक पैरामीटर सहित टाइप करके think-orm पर आधारित मॉडल प्रत्ययित कर सकते हैं, कमांड ऐसा होगा `php कंजमूर make:model तालिका नाम tp` (अगर यह प्रभावित नहीं हो रहा है तो कृपया `कंजमूर/कंसोल` को अपग्र
