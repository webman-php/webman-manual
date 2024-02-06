# Hızlı Başlangıç

webman modeli [Eloquent ORM](https://laravel.com/docs/7.x/eloquent) üzerine kurulmuştur. Her veritabanı tablosu, o tablo ile etkileşime girmek için bir "model"e sahiptir. Model aracılığıyla veritabanındaki verileri sorgulayabilir, yeni kayıtlar ekleyebilirsiniz.

Başlamadan önce, `config/database.php` dosyasında veritabanı bağlantısının yapılandırıldığından emin olun.

> Not: Eloquent ORM, model gözlemcilerini desteklemek için ek olarak `composer require "illuminate/events"` [örnek](#model-observers) gerektirir.

## Örnek
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Modelle ilişkilendirilen tablo adı
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Yeniden tanımlanan birincil anahtar, varsayılan olarak id'dir
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Zaman damgasının otomatik olarak yönetilip yönetilmediğini gösterir
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Tablo Adı
Özel veri tablolarını belirtmek için model üzerinde table özelliğini tanımlayarak yapabilirsiniz:
```php
class User extends Model
{
    /**
     * Modelle ilişkilendirilen tablo adı
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Birincil Anahtar
Eloquent ayrıca her veri tablosunun id adında bir birincil anahtara sahip olduğunu varsayacaktır. Sözleşmeyi geçersiz kılmak için korumalı $primaryKey özelliğini tanımlayabilirsiniz.
```php
class User extends Model
{
    /**
     * Yeniden tanımlanan birincil anahtar, varsayılan olarak id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent, birincil anahtarın artan bir tamsayı değeri olduğunu varsayar, bu da varsayılan olarak anahtarın otomatik olarak int türüne dönüştürüleceği anlamına gelir. Artan olmayan veya sayı olmayan bir birincil anahtar kullanmak istiyorsanız, $incrementing özelliğini false olarak ayarlamanız gerekir.
```php
class User extends Model
{
    /**
     * Modelin birincil anahtarının artan olup olmadığını gösterir
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Eğer birincil anahtarınız tamsayı değilse, model üzerinde korumalı $keyType özelliğini string olarak ayarlamanız gerekir:
```php
class User extends Model
{
    /**
     * Otomatik artan ID'nin "tipi".
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Zaman Damgası
Varsayılan olarak, Eloquent, veri tablonuzda created_at ve updated_at sütunlarının varlığını bekler. Eloquent'in bu iki sütunu otomatik olarak yönetmesini istemiyorsanız, modeldeki $timestamps özelliğini false olarak ayarlayın:
```php
class User extends Model
{
    /**
     * Zaman damgasını otomatik olarak yönetip yönetmeyeceğini gösterir
     *
     * @var bool
     */
    public $timestamps = false;
}
```
Zaman damgasının formatını özelleştirmeniz gerekiyorsa, modelinizde $dateFormat özelliğini ayarlayın. Bu özellik, tarih özniteliklerinin veritabanında saklanma şeklini ve modelin bir diziyi veya JSON formatını serileştirmesini belirler:
```php
class User extends Model
{
    /**
     * Zaman damgasının saklanma formatı
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Eğer depolama zaman damgasını özelleştirmeniz gerekiyorsa, modelde CREATED_AT ve UPDATED_AT sabitlerinin değerlerini ayarlayabilirsiniz:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Veritabanı Bağlantısı
Varsayılan olarak, Eloquent modeli, uygulamanızın yapılandırdığı varsayılan veritabanı bağlantısını kullanır. Modelin farklı bir bağlantı belirtmesi gerekiyorsa, $connection özelliğini ayarlayın:
```php
class User extends Model
{
    /**
     * Modelin bağlantı adı
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Varsayılan Özellik Değerleri
Modelin bazı özelliklerine varsayılan değerler belirtmek istiyorsanız, modelde $attributes özelliğini tanımlayabilirsiniz:
```php
class User extends Model
{
    /**
     * Modelin varsayılan özellik değerleri.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Model Sorgusu
Modeli ve ilişkili veritabanı tablosunu oluşturduktan sonra, veritabanından veri sorgulayabilirsiniz. Her Eloquent modelini güçlü bir sorgu oluşturucu olarak düşünebilirsiniz, ve onunla ilişkili veri tablosunu daha hızlı sorgulayabilirsiniz. Örneğin:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> İpucu: Eloquent modelleri aynı zamanda bir sorgu oluşturucu olduğundan, [Sorgu Oluşturucu](queries.md) bölümünde bulunan tüm yöntemleri Eloquent sorgularında da kullanabilirsiniz.

## Ek Kısıtlamalar
Eloquent'in all yöntemi tüm sonuçları modelden döndürecektir. Her Eloquent modeli bir sorgu oluşturucu olarak çalıştığı için sorgu koşulları ekleyebilir ve ardından get yöntemini kullanarak sorgu sonuçlarını alabilirsiniz:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Modeli Yeniden Yükleme
fresh ve refresh yöntemlerini kullanarak modeli yeniden yükleyebilirsiniz. fresh yöntemi modeli yeniden veritabanından alacaktır. Mevcut model örneği etkilenmeyecektir:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh yöntemi, modeli veritabanındaki yeni veriyle tekrar doldurur. Ayrıca, yüklenmiş ilişkiler de yeniden yüklenecektir:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Koleksiyon
Eloquent'in all ve get yöntemleri birden fazla sonuç sorgulayabilir ve bir `Illuminate\Database\Eloquent\Collection ` örneği döndürecektir. `Collection` sınıfı, Eloquent sonuçlarını işlemek için birçok yardımcı işlev sağlar:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Cursor Kullanımı
cursor yöntemi, veritabanını dolaşmak için bir imleç kullanmanıza izin verir ve yalnızca bir kez sorgu yapar. Büyük miktarda veri işlerken, cursor yöntemi bellek kullanımını büyük ölçüde azaltabilir:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursor, `Illuminate\Support\LazyCollection` örneğini döndürür. [Lazy koleksiyonlar](https://laravel.com/docs/7.x/collections#lazy-collections) Laravel koleksiyonunun çoğu yöntemini kullanmanıza olanak tanır ve her seferinde belleğe yalnızca tek bir model yükler:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Seçmeler Alt Sorgusu
Eloquent, gelişmiş alt sorgu desteği sağlar, ilişkili tablodaki bilgileri tek bir sorgu ifadesiyle alabilirsiniz. Örneğin, varış yeri tablosu destinations ve varış yerine yönelik uçuşlar tablosu flights örneğimiz var. flights tablosu varış yerine ne zaman ulaşacağını gösteren bir arrival_at alanı içerir.

Subquery özelliği aracılığıyla sağlanan select ve addSelect yöntemleri sayesinde, tüm varış yerlerini tek bir ifadeyle sorgulayabilir ve her varış yerine en son uçağın adını alabiliriz:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Alt Sorguya Göre Sıralama
Ek olarak, sorgu oluşturucunun orderBy işlevi de alt sorguyu destekler. Bu özellik sayesinde tüm varış yerlerini uçağın varış yerine ulaştığı en son zamana göre sıralayabiliriz. Ayrıca, bu veritabanı üzerinde tek bir sorgu yapabilir:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Tekil Model / Koleksiyon Arama
Tüm kayıtları belirtilen veri tablosundan sorgulamanın yanı sıra, find, first veya firstWhere yöntemlerini kullanarak tek bir kaydı sorgulayabilirsiniz. Bu yöntemler, bir model örneği döndürür, model koleksiyonu döndürmez:
```php
// Birincil anahtar ile bir model bulun...
$flight = app\model\Flight::find(1);

// Belirli bir sorgu koşulunu karşılayan ilk modeli bul...
$flight = app\model\Flight::where('active', 1)->first();

// Belirli bir sorgu koşulunu karşılayan ilk modelin hızlı uygulamasını bul...
$flight = app\model\Flight::firstWhere('active', 1);
```

Şu şekilde de birincil anahtar dizisi kullanarak find yöntemini çağırabilirsiniz, bu size eşleşen kayıtların bir koleksiyonunu döndürecektir:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Bazen ilk sonucu bulamadığınızda başka bir eylem gerçekleştirmek isteyebilirsiniz. firstOr yöntemi, sonucu bulduğunda ilk sonucu döndürür, sonuç bulunamazsa verilen geri çağrıyı çalıştırır. Geri çağrının dönüş değeri, firstOr yönteminin dönüş değeri olacaktır:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOr yöntemi aynı şekilde alan dizisi alarak sorgulama yapar:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "Bulunamadı" İstisnası
Bazen bir model bulunamadığında istisna fırlatmak isteyebilirsiniz. Bu özellikle kontrolörlerde ve rotalarda çok yararlıdır. findOrFail ve firstOrFail yöntemleri sorgulanan ilk sonucu alır ve bulunamazsa Illuminate\Database\Eloquent\ModelNotFoundException istisnasını fırlatır:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```
## Toplama sorgusu
Ayrıca, sorgu oluşturucu tarafından sağlanan count, sum ve max yöntemlerini ve diğer toplama işlevlerini kullanarak toplama işlemleri gerçekleştirebilirsiniz. Bu yöntemler sadece uygun bir ölçekli değer döndürecektir, bir model örneği değil:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Ekleme
Bir kayıt eklemek için önce yeni bir model örneği oluşturmalısınız, örneğe özellikler atamalısınız ve ardından save yöntemini çağırmalısınız:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Kullanıcı tablosuna yeni bir kayıt ekle
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // İstek doğrulaması

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at ve updated_at zaman damgaları otomatik olarak ayarlanacaktır (modelde $timestamps özelliği true olduğunda) ve manuel olarak atanması gerekmez.

## Güncelleme
save yöntemi ayrıca varolan modeli güncellemek için de kullanılabilir. Bir modeli güncellemek için, öncelikle bulmanız, güncellemek istediğiniz özellikleri ayarlamanız ve ardından save yöntemini çağırmanız gerekir. Yine, updated_at zaman damgası otomatik olarak güncelleneceğinden manuel olarak atama yapmanıza gerek yoktur:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Toplu Güncelleme
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Özellik Değişikliğini Kontrol Etme
Eloquent, modelin iç durumunu kontrol etmek ve özelliklerinin orijinal yüklenme durumundan nasıl değiştiğini belirlemek için isDirty, isClean ve wasChanged yöntemlerini sağlar.
isDirty yöntemi, modelin yüklenmesinden sonra herhangi bir özelliğin değiştirilip değiştirilmediğini belirler. Belirli bir özellik için değişip değişmediğini belirlemek için belirli bir özellik adı iletebilirsiniz. isClean yöntemi isDirty'nin zıttıdır, isteğe bağlı olarak özellik parametresi alır:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
wasChanged yöntemi, mevcut istek döngüsü sırasında en son modeli kaydederken herhangi bir özelliğin değiştirilip değiştirilmediğini belirler. Belirli bir özelliğin değişip değişmediğini görmek için özellik adını da iletebilirsiniz:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```

## Toplu Atama
Yeni modelleri kaydetmek için create yöntemini kullanabilirsiniz. Bu yöntem model örneğini döndürecektir. Ancak kullanmadan önce, tüm Eloquent modelleri varsayılan olarak toplu atamaya izin verilmediğinden, model üzerinde fillable veya guarded özelliklerini belirtmelisiniz.

Kullanıcılar istenmeyen HTTP parametreleri bildirip veritabanını değiştirmeye gerek olmayan alanlarda güvenlik açığı olabilir. Örneğin, kötü niyetli bir kullanıcı HTTP isteği üzerinden is_admin parametresini iletebilir ve ardından bu parametreyi create yöntemine iletirse, bu işlem kullanıcının kendisini yönetici olarak yükseltmesine neden olabilir.

Bu nedenle, başlamadan önce hangi özelliklerin toplu atamaya uygun olduğunu modelde tanımlamanız gerekir. Bu, modele $fillable özelliği üzerinden yapılabilir. Örneğin, Flight modelinin name özelliğinin toplu atamaya uygun olmasını sağlamak için:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Toplu atamaya uygun olan özellikler.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
Uygun toplu atama özelliklerini belirledikten sonra, create yöntemiyle veritabanına yeni veri girişi yapabilirsiniz. create yöntemi kaydedilen model örneğini döndürecektir:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
Bir model örneğiniz zaten varsa, fill yöntemine bir dizi ileterek değer atayabilirsiniz:
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable, toplu atamanın "beyaz listesi" olarak düşünülebilir, ayrıca $guarded özelliğini kullanabilirsiniz. $guarded özelliği, toplu atamaya izin verilmeyen özelliklerin bir dizisini içerir. Yani, $guarded işlevsel olarak bir "siyah liste" gibi davranır. Not: $fillable veya $guarded ikisinden yalnızca birini kullanabilirsiniz, aynı anda kullanılamaz. Aşağıdaki örnekte price özelliği dışında kalan tüm özellikler toplu atamaya uygun olacaktır:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Toplu atamaya izin verilmeyen özellikler.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Tüm özelliklerin toplu atamaya uygun olmasını istiyorsanız, $guarded'ı boş bir dizi olarak tanımlayabilirsiniz:
```php
/**
 * Toplu atamaya izin verilmeyen özellikler.
 *
 * @var array
 */
protected $guarded = [];
```

## Diğer Oluşturma Yöntemleri
firstOrCreate / firstOrNew
İşte toplu atama için kullanabileceğiniz iki yöntem: firstOrCreate ve firstOrNew. firstOrCreate yöntemi, belirtilen anahtar/değer çiftleriyle veritabanındaki verileri eşleştirecektir. Veritabanında model bulunamazsa, ilk parametrenin özelliklerini ve isteğe bağlı ikinci parametrenin özelliklerini içeren bir kayıt ekler.

firstOrNew yöntemi, firstOrCreate yöntemi gibi belirtilen özelliklere göre veritabanında kaydı arar. Ancak, firstOrNew yöntemi eşleşen model bulamazsa yeni bir model örneği döndürür. Unutulmamalıdır ki, firstOrNew yöntemi döndürülen model örneği henüz veritabanına kaydedilmemiştir, bunu kaydetmek için manuel olarak save yöntemini çağırmanız gerekir:
```php
// Name adına göre uçuş ara, bulunamazsa oluştur...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Name adına göre uçuş bul veya name, delayed ve arrival_time özelliklerine göre bir model oluştur...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Name adına göre uçuş ara, bulunamazsa bir model örneği oluştur...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Name adına göre uçuş ara, bulunamazsa name ve delayed, arrival_time özelliklerine sahip bir model örneği oluştur...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

Bunun yanında, varolan modeli güncellemek veya var olmayan durumda yeni bir model oluşturmak istediğiniz durumlarla da karşılaşabilirsiniz. Bunun için updateOrCreate yöntemi bulunmaktadır. firstOrCreate yöntemi gibi, updateOrCreate yöntemi modeli kalıcı hale getirir, bu nedenle save() yöntemini çağırmak zorunda kalmazsınız:
```php
// Oakland'dan San Diego'ya bir uçuş varsa, fiyatını 99 dolara ayarla.
// Eğer eşleşen bir model bulunamazsa oluştur.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## Model Silme

Bir model örneğinin delete yöntemini çağırarak silebilirsiniz:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Anahtarla Model Silme
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Sorgu ile Model Silme
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Model Kopyalama
replicate yöntemini kullanarak henüz veritabanına kaydedilmemiş yeni bir örnek kopyalayabilirsiniz. Model örnekleri birçok ortak özelliği paylaştığında, bu yöntem çok kullanışlıdır.
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();

```

## Model Karşılaştırma
Bazı durumlarda iki modelin "aynı" olup olmadığını belirlemek isteyebilirsiniz. is yöntemi iki modelin aynı anahtarı, tabloyu ve veritabanı bağlantısına sahip olup olmadığını hızlı bir şekilde doğrulamak için kullanılabilir:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Model Gözlemciler
[Laravel'deki Model Olayları ve Gözlemci](https://learnku.com/articles/6657/model-events-and-observer-in-laravel) kullanımı hakkında bilgi için buraya göz atabilirsiniz.

Not: Eloquent ORM'nin model gözlemcilerini desteklemesi için ek olarak "illuminate/events" komutunu içeren bir composer gereksinimi bulunmaktadır.
```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```
