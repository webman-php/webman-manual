## थिंकओआरएम (ThinkORM)

### थिंकओआरएम को इंस्टॉल करें

`composer require -W webman/think-orm`

इंस्टॉलेशन के बाद restart यानी कि पुनरारंभ की आवश्यकता होगी (reload कार्यरत नहीं होगा)

> **सुझाव**
> यदि इंस्टॉलेशन विफल हो जाती है, तो यह संभावना है कि आपने कंपोजर प्रोक्सी का उपयोग किया है, कृपया `composer config -g --unset repos.packagist` कमांड को चलाकर कंपोजर प्रोक्सी को निष्क्रिय करें।

> [webman/think-orm](https://www.workerman.net/plugin/14) वास्तव में `toptink/think-orm` को स्वचालित रूप से इंस्टॉल करने वाला एक प्लगइन है। यदि आपकी webman संस्करण `1.2` से कम है तो आप इस प्लगइन का उपयोग नहीं कर सकते हैं। कृपया [मैन्युअल इंस्टॉलेशन और think-orm कॉन्फ़िगर करने](https://www.workerman.net/a/1289) वाले लेख का उपयोग करें।

### कॉन्फ़िग फ़ाइल

वास्तविक स्थिति के अनुसार `config/thinkorm.php` कॉन्फ़िग फ़ाइल को संशोधित करें।

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

### मॉडल बनाएँ

ThinkOrm मॉडल `think\Model` को extend करता है, जैसा कि निम्नलिखित है
```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * मॉडल के साथ संबद्ध तालिका।
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * तालिका के प्राथमिक कुंजी।
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

आप निम्नलिखित कमांड का उपयोग करके thinkorm पर आधारित मॉडल बना सकते हैं
```bash
php webman make:model टेबल_नाम
```

> **सुझाव**
> इस कमांड के लिए `webman/console` की इंस्टॉलेशन की आवश्यकता होती है, नीचे दिए गए कमांड का उपयोग करके इंस्टॉल करें - `composer require webman/console ^1.2.13`

> **ध्यान दें**
> make:model कमांड यदि मुख्य प्रोजेक्ट में `illuminate/database` का उपयोग किया जा रहा है, तो वह `illuminate/database` पर आधारित मॉडल फ़ाइल बनाएगा, अगर ऐसा होता है तो tp पैरामीटर का उपयोग करके थिंक-ओआरएम पर आधारित मॉडल को बनाने के लिए कमांड यह प्रकार होगा - `php webman make:model टेबल_नाम tp` (अगर यह प्रभावी नहीं होता है तो कृपया `webman/console` को अपडेट करें)
