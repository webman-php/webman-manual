# விசாரி உருவாக்கி
## அனைத்து வரிகளைப் பெறுக
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

## குறிப்பிட்ட நொக்குகளைப் பெறுக
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## ஒரு வரியை பெறுக
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## ஒரு வரியை சுமமாக பெறுக
```php
$titles = Db::table('roles')->pluck('title');
```
அடையாளத்தை குறிப்பிட்ட ஐடி புலத்தாக பயன்படுத்தும்
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## ஒரு மதிப்பை (நொக்கு) பெறுக
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## இலவசமாக சேர்க்க
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## பகுதிகளை பெறுக
நீங்கள் ஆனால் அநேகமாகப் படிக்கப்படும் ஆனது, அவைகளை ஒருசேன் தேடல் போது அவைகளைத் திருப்தி செய்யலாம். உதாரணமாக, நாங்கள் முழுமையாக பயன்பாடுகள் பட்டியலை 100 பதிவிகளாக பிரத்தியேகமாக ஒரு சிறிய பகுதியாக விரும்பும் போன்றதை இடைவெளி செய்யலாம்:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
நீங்கள் சேன்சிகள் உள்ளீடு செய்யுவதன் மூலம் அவைகளை மறுக்கலாம்.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // பதிவுகளை மூடுங்கள்...

    return false;
});
```

> அப்படியானால்: மன்னிக்கவும், நீக்கத்தை கூறவில்லை, எனவே சில பதிவுகள் முன்னும் மதிப்பாயிவில் இல்லை

## ஒழுகட்டுமுறை
விசாரி உருவாக்கி இன் கோபங்களில் பல ஒழுகட்டுமுறைகளைக் கிரமாக அறிக்கின்றது, குறிப்பிட்டவைகள், max, min, avg, sum போன்று.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## பதிவுகள் உள்ளது அல்லது இல்லை என்பதைக் கண்டறி
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## உயர்ந்த மூல விநியோகம்
செயலி உள்ளிட்ட சொல்லில் ஒழுகட்டுமுறை பயன்படுத்தித் தேவையான, உயர்ந்த முறையிலேயே அவைகளைப் பெற முடியும். நீங்கள் போந்துள்ளபோது, முழுநேரத்திற்கு பதிவிகளை ஒருசேன் பெறக் கூடும், அந்த தரவை மீத்திருப்தி செய்ய வளவு போன்று, நீங்கள் `chunkById` முறையை பயன்படுத்தலாம்:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
நீங்கள் ஒரு செயினை மூடுவிக்கும் குறிக்க 100 பதிவுகள் சேன் பெற.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // பதிவுகளை மூடுங்கள்...

    return false;
});
```

> குறிக்க  : உத்தியரம் செய்வதைத் தவிரவேண்டாம், அத்தகைய வெற்றியிறங்கல்கள் உருவாகவும்

## ஒரு மூல விதி
போல்
```php
selectRaw($expression, $bindings = [])
```
ஆனால் நீங்கள் கட்டுப்பட்ட மூல விநியோகத்தை ஆனது பயன்படுத்தலாம். நீங்கள் `selectRaw()` ஐப் பயன்படுத்தி ஒரு மூல விநியோகத்தை உருவாக்கலாம்:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

அதேபோன்றி, `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` மூல விநியோகத்தை உருவாக்கும்.

`Db::raw($value)` மற்றும் ஒரு மூல விநியோகத்தை உருவாக்கும், ஆனால் அதில் ஒரு சேர்க்கை செய்யுவதை தானாகவே உண்டு, SQL பகுதிகளைக் கொண்டி மூலத்திற்கு பயன்படுத்தும் போன்றதை நினைவாக வைத்துக் கொள்ள வேண்டும்.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## இணைவு சொல்
```php
// இணைவு
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// leftJoin            
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// rightJoin
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// crossJoin    
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

## ஒருபோல் செய்துட
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Where வாக்கியம்
புதுப்பி
```php
where($column, $operator = null, $value = null)
```
முதல் மாறியின் பெயர், இரவச்சி என்றால் யாவும் ஒரு தரவுகரிப் போடும் சத்தம், மூன்றாவது வேறுபாடுகளில் அத் மாறியின் மதிப்பை மேலும் ஒன்று மழியத்திற்கே.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// பலவர்களின் எண் என்ற சிலை மதிப்பு இல்லாமல் இருந்தால், அப் வாருந்தால் பெற்றதே அத      
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

நீங்கள் where செயலிக்கு நியாகரினி படிவம் வாக்கியங்களைத் திரும்பினீர்கள்:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

அல்லது வாரு செயலிக்கு அதே அலராணுகள்:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

நீங்கள் orWhere செயலிக்கு முதல் ஆர்களிடம் ஒரு மூன்றுடன் பொருளிக்களைத் தள்ளலாக:
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

whereBetween / orWhereBetween செயலிக்கு புலகண்டாக்களின் மதிகள் பட்டிகளின் இடைவேளையில் உள்ளனப்படும்:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween வாக்கியம் வாக்கியம் வாக்கியம் வாக்கியம் வாக்கியம்:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn செயலிக்கு புலகண்டீடுகளில் புலகண்ட்டின் மதிப்புகளும் உள்ளன:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull செயலிக்கு சுட்டிகின் மதிப்பில் பூலின் பேரி:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull வாக்கியம் வாக்கியம் வாக்கியம் வாக்கியம் வாக்கியம் வாக்கியம்:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime வாக்கியம்கின் மதிப்புகளை கொடுக்குவதற்கு பயன்படுத்தப்படும்:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn வாக்கியம் வாக்கியம் நாளப்பொ: வி, யெ, யத்துக்கு மதிப்புகளை ஒனமாகப் பார்க்கிறது:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();

// நீங்கள் ஒரு படிவக் குபாயின்ன விருப்பப்படுக்கொள்ளலாம்
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();

// whereColumn வாக்கியம் அலாத்து படிவப்புறம்
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

பெயருக்கும் பரிவுகுப்புகுபிள்
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

## orderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## விரச்
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> விரச் தனிப்பட்ட ஒனைநிலைஅரவு ஆளுக்கு அதிக வினை வருபான் கடாச் ஓவ்வை

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// நீங்கள் groupBy செயலிக்கு பல உத்தரவுகளைத்தூக்கலாக
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## ஓஃப்ஃட் / லிமிட்
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## இடைக்குறி
ஒற்
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
பலவர்கள்
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);

```

## சுயும் ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> கவறைய்: பயன்படுத்தும்போதே insertGetId வெளுய்யும்போதே id ஐ ஒரோக்கு அத்தாராப் ஊரோ-அவ்வரோ விரச் போதற்கு. நீங்கள் லெயோலாமா arc 'களிடைஇடு'க உண்மை 'இடு'வாடிறி அமை்யுகாம் வார்த்தை எனக்கேதுகுய்லட் கவற ட்றேழுகுக் இடு

## நான் விரச்
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## இறுதியாக்கியால் அலர்ாணல்
சமையுள் நீங்கள் ஒருாத் துஙாஇறுறுப்பங்கிக்கட நெஅபல ஏற்ஷிரித் - தவடிபஒுக்கல்கம்காரபஹுப் தெரி க்காரினீரா :


```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
துத்கான்-இறுதியூசிங்ல்ய்-அலாத்து மெதாட் அதுூவ் மெதாட்-டூப் ஒப்ுர்ஙிராவல் ா஫்-வாலூஸ் வாள்ட் இன் தியூ ரேகார்ட். வாலூஸ்-ரேகார்ட் இது் டிஸ் დ் து-அர் மேதாட்் இன் 2 அర்ரேய ஒப்்-இந்தாய் 2 அர்ற்றோவிெஸ் 2 அர்ரேய பி ூவ் கேடாக் இன்கேட்.

## இறுவ்வூர்வும்இறுவ்வூ
தேசிஙிஇறுவ்வூசி தேஸம்த்து எட்டி-க்கம் ஒர் மாறி செயலிக்கு பண்ற்த் தேசிதுங்க்-அட்க்காㄅ்:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
நீங்கள் orதை இருந்து பிராச்ஞபர்க்ஸ் திங்சு அப்ட்ூன் ட்பார்-ட்஖்ೂத்ன்:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```
## நீக்கு
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
நீங்கள் ஒரு அட்டவணையை குறிப்பிட்டு மீண்டும் தெரியவில்லை என்றால், truncate அமைப்பைப் பயன்படுத்தலாம். அவர் அனைத்து வரிகளையும் நீக்கி, மற்றும் சுயம் அதிகரிப்பதாக மாற்றியும் இடுகை ID ஐ பூஜ்ஜி மரத்து:
```php
Db::table('users')->truncate();
```

## சொத்துநிஹர்வை
வினவு உருவாக்கி மாற்றுபடி ஒண்டைக்கண பிடித்த அளவை உள்ளடக்கிற செயலாக்காளர் போப் "ஒப் லாக்" செயல்படுத்த உதவி செய்யலாம். ஏதாவது "பகிரத் லாக்" உள்ளடக்கும் தரவு பத்தியலாக்கமும் அது ஆதாரமாக பணிவிடப்படுயும்:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
அல்லது, "ப் லாக் படோர் பிரியேஷ்" செயல்படுத்தலாம். "புடேடு" படோர் மூலம் இன்னமாது கூட்டம் அல்லது ஆதாரம் மாற்றப்பட்டுவைக்கப்படாது:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## முறைசெயல்
நீங்கள் dd அல்லது டம்ப் மெதட்டோம் செயல்படுத்தி வினவு முடிவு அல்லது SQL பல மற்றும் விவரங்களைக் காட்ட முடிவு பெறக்கூடியிருக்கும். செயல்பாடுகள் காண்க:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **குறிப்பு**
> முறைகளை பரிசீலனை 'சிம்போனி/வர்-டம்பெர்' பாடத்தை நிறைய கிரோம் தேவைப்படும், அத்தகைய குறிக்கப்பட்டுவைக்கப்பட்டுவைக்கப்படுகிறது, என் கட்டளை 'கம்பாட் ரிகவை வேறு செய்து' என்றும் அவர் நன்றாக சொல்லும்.
