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
या नीचे दिए गए कोड ब्लॉक को नीचे दिए गए तरीके से बदला जा सकता है।

```php
$users = Db::table('users')->select('name', 'email as user_email')->get();
```

```php
$user = Db::table('users')->where('name', 'John')->first();
```

```php
$titles = Db::table('roles')->pluck('title');
```

```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```

```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Records का प्रोसेस करें...

    return false;
});
```

```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

```php
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();
```

```php
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();
```

```php
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();
```

```php
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## WHERE स्टेटमेंट

सिग्नेचर
```php
where($column, $operator = null, $value = null)
```
पहला पैरामीटर कॉलमन नाम है, दूसरा पैरामीटर किसी भी डेटाबेस सिस्टम द्वारा समर्थित ऑपरेटर है, और तीसरा खाचा कॉलमन की मूल्य को तुलना करने के लिए है।
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// जब ऑपरेटर बराबर हो तो यहां छोड़ा जा सकता है, इसलिए यह वाक्यांश पिछले से अपने प्रभावों को दिखाता है
$users = Db::table('users')->where('votes', 100)->get();

$users = Db::table('users')
                ->where('votes', '>=', 100)
                ->get();

$users = Db::table('users')
                ->where('votes', '<>', 100)
                ->get();

$users = Db::table('users')
                ->where('name', 'like', 'T%')
                ->get();
```

आप where फ़ंक्शन को कंडीशन ऐरे के रूप में भी पास कर सकते हैं:

```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

orWhere मेथड और where मेथड को आयतन के रूप में पैरामीटर प्राप्त होते हैं:

```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

आप orWhere मेथड को पहले पैरामीटर के रूप में एक क्लोजर पास कर सकते हैं।

```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$users = Db::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();
```

whereBetween / orWhereBetween मेथड फ़ील्ड मूल्य की जांच करता है कि दिए गए दो मूल्यों के बीच है:

```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween मेथड फ़ील्ड मूल्य की जांच करता है कि दिए गए दो मूल्यों के बाहर है:

```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn मेथड फ़ील्ड के मूल्य का यह सत्यापन करता है कि वह निर्दिष्ट गण में मौजूद है:

```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull मेथड निर्दिष्ट फ़ील्ड का मूल्य NULL होना चाहिए का सत्यापन करता है:

```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull मेथड निर्दिष्ट फ़ील्ड का मूल्य NULL नहीं होना चाहिए का सत्यापन करता है:

```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime मेथड तारीख के साथ फ़ील्ड का मूल्य की तुलना करने के लिए है:

```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn मेथड दो फ़ील्ड के मूल्य का तुलना करने के लिए है:

```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// आप एक तुलनात्मक ऑपरेटर भी पास कर सकते हैं
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn मेथड आरेय को भी पास कर सकता है
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();
```

पैरामीटर समूहीकरण

```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
               $query->where('votes', '>', 100)
                     ->orWhere('title', '=', 'Admin');
           })
           ->get();
```

whereExists

```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$users = Db::table('users')
           ->whereExists(function ($query) {
               $query->select(Db::raw(1))
                     ->from('orders')
                     ->whereRaw('orders.user_id = users.id');
           })
           ->get();
```

## क्रमबद्ध

```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## यादृच्छिक क्रम

```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> यादृच्छिक क्रम त्रुटि और सर्वर पर प्रदर्शन पर प्रभाव डाल सकता है, इसका प्रयोग करना अनुशंसित नहीं है

## ग्रुपबाय / हैविंग

```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// आप groupBy मेथड को एक से अधिक पैरामीटर पास कर सकते हैं
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```
## आफ़सेट / सीमा
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## सम्मिलित
एक विलंब डालें
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
एकाधिक डालें
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

> ध्यान दें: PostgreSQL का उपयोग करते समय, `insertGetId` विधि डिफ़ॉल्ट रूप से `id` को स्वचालित रूप से बढ़ाने वाले क्षेत्र के रूप में लाएगी। यदि आप अन्य "क्रम" से ID प्राप्त करना चाहते हैं, तो `insertGetId` विधि में पहले के पैरामीटर के रूप में क्षेत्र नाम को भेज सकते हैं।

## अपडेट
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## अपडेट या अनुप्रवेश
कभी-कभी आपको डेटाबेस में मौजूद रिकॉर्ड को अपडेट करना चाहिए, या अगर संबंधित रिकॉर्ड मौजूद नहीं है तो उसे बनाना चाहिए:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
`updateOrInsert` विधि पहले पैरामीटर की कुंजी और मूल्य से डेटाबेस रिकॉर्ड की खोज करने का प्रयास करेगी। यदि रिकॉर्ड मौजूद है, तो दूसरे पैरामीटर में मूल्यों का उपयोग करके रिकॉर्ड को अपडेट करेगी। यदि रिकॉर्ड नहीं मिलता, तो नया रिकॉर्ड डालेगी, जो दोनों सरणियों के एक संग्रह की जानकारी होती है।

## स्वचालित और स्वचालित घटाना
इन दोनों विधियों को कम से कम एक पैरामीटर देना चाहिए: एडिट करने वाला स्तंभ। दूसरा पैरामीटर वैकल्पिक है, जो स्तंभ को वृद्धि या घटाने की मात्रा को नियंत्रित करता है:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
आप ऑपरेशन के दौरान अपडेट करना चाहते हैं, तो आप स्तंभ को स्पष्ट कर सकते हैं:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## हटाएं
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
यदि आप तालिका को साफ करना चाहते हैं, तो आप `truncate` विधि का उपयोग कर सकते हैं, जो सभी पंक्तियाँ हटा देगा और स्वचालित अभिवृद्धि आईडी को शून्य पर रीसेट कर देगा:
```php
Db::table('users')->truncate();
```

## पूर्वाभिक ताला
क्वेरी बिल्डर में एक कुछ "बेतुकी" ताला लगाने में मदद करने वाले फ़ंक्शन भी शामिल हैं। यदि आप क्वेरी में "साझा ताला" लागू करना चाहते हैं, तो आप `sharedLock` विधि का उपयोग कर सकते हैं। साझा ताला उस स्तंभ को बदलने से रोक सकता है, जो क्वेरी के सत्यापन के लिए प्राप्त होता है, जब तक सौदा समाप्त नहीं होता:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
या, आप `lockForUpdate` विधि का उपयोग कर सकते हैं। "अपडेट" ताला का उपयोग करने से अन्य साझा ताला से पंक्तियाँ को बदलने या चयन करने से बचाया जा सकता है:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## डिबग
आप `dd` या `dump` विधि का उपयोग करके क्वेरी परिणाम या SQL वाक्यांश निकाल सकते हैं। `dd` विधि का उपयोग करके डीबग जानकारी प्रदर्शित की जा सकती है, और फिर इस अनुरोध को रोक दिया जाएगा। `dump` विधि भी डीबग जानकारी प्रदर्शित करती है, लेकिन अनुरोध को रोक नहीं करती है:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **ध्यान दें**
> डिबग के लिए `symfony/var-dumper` स्थापित करना आवश्यक है, यहां तक कि आदेश `composer require symfony/var-dumper` है।
