# क्वेरी बिल्डर
## सभी पंक्तियाँ प्राप्त करें
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function all(Request $request)
    {
        $users = Db::table('users')->get();
        return view('user/all', ['users' => $users]);
    }
}
```

## निर्दिष्ट स्तंभ प्राप्त करें
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## एक पंक्ति प्राप्त करें
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## एक स्तंभ प्राप्त करें
```php
$titles = Db::table('roles')->pluck('title');
```
निर्दिष्ट id फील्ड का मान सूची के रूप में उपयोग करें
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## एकल मान (स्ट्रिंग) प्राप्त करना
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## डुप्लिकेट निकालें
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## खंड परिणाम
यदि आपको हजारों डेटाबेस रिकॉर्ड का सामना करना पड़ता है, तो इन डेटा को एकबार में पढ़ना समय लेने वाला हो सकता है और आंतरिक स्मृति का पार्श्वप्ति हो सकती है, इससे ज्यादा आप orderBy('id')->chunkById(100, function ($users) {...}) का उपयोग कर सकते हैं।
यह विधि एक छोटे भाग को एक पंक्तियों का परिणाम प्राप्त करता है, और इसे पारदर्शी फ़ंक्शन को सामान्‍य बनाने के लिए भेजता है।
उदाहरण के लिए, हम सभी सदस्य तालिका डेटा को 1 समेत 100 रिकॉर्ड के छोटे भागों में कटौती कर सकते हैं:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
आप एक बंद करने के लिए false को वापसी में वापस भेजकर खंड परिणाम जारी करने के बारे में जान सकते हैं।
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // रिकॉर्ड की प्रक्रिया करें...

    return false;
});
```

> ध्यान दें: वापसी में कॉलबैक में डेटा को हटाएं नहीं, इससे कुछ रिकॉर्ड परिणाम सेट में नहीं शामिल हो सकते हैं।

## एग्रीगेट
क्वेरी बिल्डर भी गिनती, अधिकतम, न्यूनतम, औसत, योग आदि जैसे विभिन्न एग्रीगेट फ़ंक्शन प्रदान करता है।
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## रिकॉर्ड की उपस्थिति की जांच
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## मूल व्यक्तिगत अभिव्यंति
शुद्ध रूप
```php
selectRaw($expression, $bindings = [])
```
कभी-कभी आपको क्वेरी में मूल अभिव्यंति का उपयोग करना होता है। आप `selectRaw()` का उपयोग करके एक मूल अभिव्यंति बना सकते हैं:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

इसी तरह, `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` मूल अभिव्यंति विध
## इंजेक्शन
एकल इंजेक्शन डेटा
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
बहुत सारे इंजेक्शन डेटा
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## स्वचालित आईडी
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> ध्यान दें: पोस्टग्रेसक्यूएल का उपयोग करते समय, insertGetId प्रक्रिया पूर्वनिर्धारित रूप से ऑटो इंक्रीमेंट फील्ड की नामकरण करती है। यदि आपको अन्य "क्रम" से आईडी प्राप्त करनी है, तो insertGetId प्रक्रिया को दूसरे पैरामीटर के रूप में फील्ड नाम पास कर सकते हैं।

## अपडेट
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## अपडेट या नया डालना
कभी-कभी आपको डेटाबेस में मौजूदा रिकॉर्ड को अपडेट करना होता है, या यदि कोई मेल नहीं है तो नया रिकॉर्ड बनाना होता है:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert प्रक्रिया सबसे पहले पहले पैरामीटर की कुंजी और मान का उपयोग कर कोई मिलता-जुलता डेटाबेस रिकॉर्ड ढूँढ़ती है। यदि रिकॉर्ड मिल जाता है, तो दूसरे पैरामीटर में दिए गए मान से रिकॉर्ड को अपडेट किया जाएगा। यदि रिकॉर्ड नहीं मिलता है, तो एक नया रिकॉर्ड बनाया जाएगा, जिसमें दोनों एरे का डेटा होगा।

## स्वत: इंक्रीमेंट और डिक्रीमेंट
इन दोनों प्रकार की प्रक्रियाएँ कम से कम एक पैरामीटर प्राप्त करती हैं: संशोधित करने वाला स्तंभ। दूसरा पैरामीटर वैकल्पिक है, और स्तंभ को बढ़ाने या घटाने के मात्र को नियंत्रित करने के लिए है:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
आप ऑपरेशन के दौरान अद्वितीय स्तंभ को भी निर्दिष्ट कर सकते हैं:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## हटाना
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
यदि आप तालिका को साफ करना चाहते हैं, तो आप truncate प्रक्रिया का उपयोग कर सकते हैं, जो सभी पंक्तियाँ हटा देता है, और ऑटो इंक्रीमेंट आईडी को शून्य पर रीसेट कर देता है:
```php
Db::table('users')->truncate();
```

## डिप्रेसनलॉक
क्वेरी बिल्डर में कुछ "डिप्रेसनलॉक" कार्य भी शामिल हैं, जो आपको "साझा लॉक" को लागू करने में मदद कर सकते हैं। यदि आप किसी "साझा लॉक" को चुनना चाहते हैं, तो आप sharedLock प्रक्रिया का उपयोग कर सकते हैं। साझा लॉक डेटा कॉलम को संशोधित होने से रोकता है, जब तक ट्रांजैक्शन सबमिट नहीं हो जाता है:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
या फिर, आप lockForUpdate प्रक्रिया का उपयोग कर सकते हैं। "अपडेट" लॉक का उपयोग करके आप दूसरे साझा लॉक में पकड़े गए पंक्तियों से बच सकते हैं या उन्हें चुन सकते हैं:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## डिबग
आप डेबगिंग के लिए dd या dump प्रक्रिया का उपयोग कर सकते हैं। dd प्रक्रिया का उपयोग करके डेबग जानकारी दिखाई जा सकती है, और फिर रिक्वेस्ट की प्रक्रिया रोक दी जाती है। dump प्रक्रिया भी डेबग जानकारी दिखा सकती है, लेकिन फिर रिक्वेस्ट रोकी नहीं जाती है:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **ध्यान दें**
> डिबगिंग के लिए `symfony/var-dumper` इंस्टॉल करने की आवश्यकता होती है, कमांड `composer require symfony/var-dumper` है।
