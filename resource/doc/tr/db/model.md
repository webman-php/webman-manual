# Hızlı Başlangıç

webman modeli [Eloquent ORM](https://laravel.com/docs/7.x/eloquent)'ye dayanmaktadır. Her veritabanı tablosu, o tablo ile etkileşimde bulunmak için bir "model"e sahiptir. Model kullanarak veri tablosundaki verileri sorgulayabilir ve yeni kayıtlar ekleyebilirsiniz.

Başlamadan önce, `config/database.php` dosyasında veritabanı bağlantısının yapılandırıldığından emin olun.

> Not: Eloquent ORM, model izleyicisini desteklemek için `composer require "illuminate/events"`'ın ekstra olarak içe aktarılmasını gerektirir. [Örnek](#model-izleyicisi)

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
     * Varsayılan olarak yeniden tanımlanmış anahtar
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Zaman damgasını otomatik olarak sürdürüp sürdürmeyeceğini gösterir
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Tablo Adı
Özel veri tablosunu belirtmek için model üzerinde table özelliğini tanımlayabilirsiniz:
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

## Anahtar
Eloquent, her veri tablosunun id adında bir anahtar sütunu olduğunu varsayar. Anlaşmayı geçersiz kılmak için korumalı $primaryKey özelliğini tanımlayabilirsiniz.
```php
class User extends Model
{
    /**
     * Varsayılan olarak yeniden tanımlanmış anahtar
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent, anahtarın artan bir tamsayı değeri olduğunu varsayar, bu da varsayılan olarak anahtarın otomatik olarak int türüne dönüştürüleceği anlamına gelir. Artan olmayan veya sayı olmayan bir anahtar kullanmak istiyorsanız, genel $incrementing özelliğini false olarak ayarlamalısınız.
```php
class User extends Model
{
    /**
     * Model anahtarının artan olup olmadığını gösterir
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Anahtarınız bir tamsayı değilse, model üzerinde korumalı $keyType özelliğini string olarak ayarlamanız gerekecektir:
```php
class User extends Model
{
    /**
     * Otomatik artan ID'nin "tipi"
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Zaman Damgası
Varsayılan olarak, Eloquent, veri tablonuzda created_at ve updated_at sütunlarının bulunduğunu varsayar. Eloquent'in bu iki sütunu otomatik olarak yönetmesini istemiyorsanız, modeldeki $timestamps özelliğini false olarak ayarlamalısınız:
```php
class User extends Model
{
    /**
     * Zaman damgasının otomatik olarak sürdürülüp sürdürülmeyeceğini gösterir
     *
     * @var bool
     */
    public $timestamps = false;
}
```

Zaman damgasının depolanma biçimini ve modelin dizi veya JSON olarak serileştirilme biçimini belirlemek için modelinizde $dateFormat özelliğini ayarlayabilirsiniz:
```php
class User extends Model
{
    /**
     * Zaman damgası depolama formatı
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Zaman damgası alanlarını özelleştirmeniz gerekiyorsa, modeldeki CREATED_AT ve UPDATED_AT sabitlerinin değerlerini ayarlayarak depolama alanlarını özelleştirebilirsiniz:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Veritabanı Bağlantısı
Varsayılan olarak, Eloquent modelleri uygulama yapılandırmasında belirtilen varsayılan veritabanı bağlantısını kullanacaktır. Model için farklı bir bağlantı belirtmek istiyorsanız, $connection özelliğini ayarlayın:
```php
class User extends Model
{
    /**
     * Model bağlantı adı
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Varsayılan Özellik Değerleri
Modelin bazı özellikler için varsayılan değerleri tanımlamak istiyorsanız, model üzerinde $attributes özelliğini tanımlayabilirsiniz:
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

## Model Sorgulama
Model ve ilişkili veritabanı tablosu oluşturduktan sonra, veritabanından veri sorgulayabilirsiniz. Her Eloquent modelini güçlü bir sorgu oluşturucu olarak düşünebilirsiniz, bu sayede ilişkili veri tablosunu daha hızlı sorgulayabilirsiniz. Örneğin:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> İpucu: Eloquent modeli aynı zamanda bir sorgu oluşturucu olduğundan, tüm [Sorgu Oluşturucu](queries.md)'nun kullanılabilir yöntemlerini de incelemelisiniz. Bu yöntemleri Eloquent sorgusunda kullanabilirsiniz.

## Ek Kısıtlama
Eloquent'in tüm yöntemleri, tüm sonuçları döndürür. Her Eloquent modeli bir sorgu oluşturucu olarak hizmet ettiğinden, sorgu koşullarını ekleyebilir ve ardından get yöntemini kullanarak sorgu sonuçlarını alabilirsiniz:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Modeli Yeniden Yükleme
Modeli yeniden yüklemek için fresh ve refresh yöntemlerini kullanabilirsiniz. fresh yöntemi modeli veritabanından yeniden getirecektir. Mevcut model örneği etkilenmeyecektir:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh yöntemi, mevcut modele yeni verileri yeniden atar. Ayrıca, yüklenen ilişkiler de yeniden yüklenecektir:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Koleksiyon
Eloquent'in all ve get yöntemleri birden çok sonuç getirebilir ve `Illuminate\Database\Eloquent\Collection` örneği döndürür. `Collection` sınıfı, Eloquent sonuçlarını işlemek için birçok yardımcı yöntem sağlar:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```
## Cursor Kullanımı
Cursor yöntemi, veritabanında bir döngü oluşturmak için kullanılan ve yalnızca bir kez sorgu yapan bir yöntemdir. Büyük veri setleriyle çalışırken cursor yöntemi, bellek kullanımını büyük ölçüde azaltabilir:
```php
foreach (app\model\User::where('sex', 1)->cursor() as $user) {
    //
}
```

Cursor, `Illuminate\Support\LazyCollection` örneği döndürür. [Tembel koleksiyonlar](https://laravel.com/docs/7.x/collections#lazy-collections) Laravel koleksiyonlarının çoğunun yöntemlerini kullanmanıza olanak tanır ve her seferinde yalnızca tek bir modeli belleğe yükler:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Alt Sorgularıyla Seçim Yapma
Eloquent, gelişmiş alt sorgu desteği sağlar. İlgili tablolardan bilgi almak için tek bir sorgu kullanabilirsiniz. Örneğin, varış noktası tablosu olan "destinations" ve varış noktasına uçan uçuşlar tablosu olan "flights" olduğunu varsayalım. Uçuşlar tablosu, varış noktasına ne zaman ulaştığını gösteren bir "arrived_at" alanı içerir.

Alt sorgu özelliğini kullanarak, tek bir ifadeyle tüm varış noktalarını ve her varış noktasına en son uçan uçuşun adını sorgulayabiliriz:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Alt Sorgulara Göre Sıralama
Ayrıca, sorgu oluşturucusunun orderBy işlevi de alt sorguları destekler. Bu özellik sayesinde tüm varış noktalarını en son uçuşun varış zamanına göre sıralamak mümkün olur. Aynı şekilde, bu, veritabanında yalnızca tek bir sorgu yapılır:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Tekil Model / Koleksiyon Sorgulama
Belirli bir veri tablosundan tüm kayıtları almanın yanı sıra, find, first veya firstWhere yöntemlerini kullanarak tek bir kayıt alabilirsiniz. Bu yöntemler, bir model örneği döndürür ve bir model koleksiyonu döndürmez:
```php
// Bir modeli anahtar ile bulma...
$flight = app\model\Flight::find(1);

// Belirli bir sorgu koşuluna uyan ilk modeli bulma...
$flight = app\model\Flight::where('active', 1)->first();

// Belirli bir sorgu koşuluna uyan ilk modeli hızlı bir şekilde bulma...
$flight = app\model\Flight::firstWhere('active', 1);
```

Ayrıca, anahtar dizisini argüman olarak kullanarak find yöntemini çağırabilirsiniz. Bu, eşleşen kayıtları bir koleksiyon olarak döndürecektir:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Bazen, ilk sonucu bulamadığınızda diğer bir işlemi gerçekleştirmek isteyebilirsiniz. firstOr yöntemi sonucu bulduğunda ilk sonucu döndürür, sonuç bulunamazsa belirtilen geri çağrıyı çalıştırır. Geri çağrının dönüş değeri, firstOr yönteminin dönüş değeri olacaktır:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOr yöntemi aynı şekilde sütun dizisini sorgulamak için de kullanılabilir:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "Bulunamadı" İstisnası
Bazı durumlarda, model bulunamadığında istisna fırlatmanız gerekebilir. Bu, kontrollerde ve rotalarda oldukça kullanışlıdır. findOrFail ve firstOrFail yöntemleri, aranan ilk sonucu getirir ve bulunamazsa Illuminate\Database\Eloquent\ModelNotFoundException istisnası fırlatır:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## Koleksiyon Getirme
Ayrıca, sorgu oluşturucusunun count, sum ve max yöntemleri gibi diğer koleksiyon işlevlerini kullanarak koleksiyonları işleyebilirsiniz. Bu yöntemler, bir model örneği döndürmez; uygun bir skaler değer döndürür:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Ekleme
Bir kayıt eklemek için önce yeni bir model örneği oluşturun, özelliklerini ayarlayın ve ardından save yöntemini kullanın:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Kullanıcı tablosuna yeni bir kayıt ekler
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // İsteği doğrula

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at ve updated_at zaman damgaları otomatik olarak ayarlanacaktır (modelin $timestamps özelliği true olduğunda) ve manuel olarak ayarlamanıza gerek yoktur.

## Güncelleme
save yöntemi aynı zamanda mevcut olan veritabanı kayıtlarını güncellemek için de kullanılabilir. Bir modeli güncellemek için önce bulun ve güncellenecek özellikleri ayarlayın, ardından save yöntemini çağırın. Ayrıca updated_at zaman damgası otomatik olarak güncelleneceği için manuel olarak ayarlamanıza gerek yoktur:
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
## Özellik Değişikliklerini Kontrol Etme
Eloquent, modelin iç durumunu kontrol etmek ve özelliklerinin başlangıçtan bu yana nasıl değiştiğini belirlemek için isDirty, isClean ve wasChanged yöntemlerini sağlar. 
isDirty yöntemi, modelin yüklenmesinden bu yana herhangi bir özelliğin değiştirilip değiştirilmediğini belirler. Belirli bir özellik adını ileterek belirli bir özelliğin kirli olup olmadığını belirleyebilirsiniz. isClean yöntemi isDirty'ye zıt olarak çalışır ve opsiyonel özellik parametrelerini kabul eder:
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
wasChanged yöntemi, mevcut istek döngüsünde modelin son kaydedildiğinde herhangi bir özelliğin değişip değişmediğini belirler. Ayrıca, belirli bir özelliğin değiştirilip değiştirilmediğini görmek için özellik adını iletebilirsiniz:
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
Yeni bir modeli kaydetmek için create yöntemini kullanabilirsiniz. Bu yöntem bir model örneği döndürecektir. Ancak, kullanmadan önce, tüm Eloquent modellerinin varsayılan olarak toplu atamaya izin veremeyeceği için model üzerinde fillable veya guarded özelliğini belirtmeniz gerekir. 
Kullanıcı istemeden HTTP parametreleri ekler ve bu parametreler veritabanında değiştirmeniz gerekmeyen alanları değiştirirse, toplu atama açığı oluşabilir. Örneğin: Kötü niyetli bir kullanıcı HTTP isteği ile is_admin parametresini ekleyebilir ve bunu create yöntemine geçirerek kendisini yönetici konumuna getirebilir.

Bu yüzden başlamadan önce modelde hangi özelliklerin toplu olarak atandığını belirtmelisiniz. Bunun için model üzerinde $fillable özelliği kullanabilirsiniz. Örneğin: Flight modelinde name özelliğinin toplu atamaya izin verilmesini sağlamak için:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Toplu atamaya izin verilen özellikler.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
Toplu atamaya izin verilen özellikleri ayarladıktan sonra create yöntemini kullanarak yeni veriyi veritabanına ekleyebilirsiniz. Create yöntemi kaydedilen model örneğini döndürecektir:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
Eğer zaten bir model örneğiniz varsa, fill yöntemine bir dizi parametresi geçerek değer atayabilirsiniz:
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable, toplu atamanın "izin verilenler listesi" olarak düşünülebilir; ayrıca $guarded özelliğini kullanabilirsiniz. $guarded, toplu atamaya izin verilmeyen bir diziyi içerir, yani $guarded işlevsel olarak bir "karaliste" gibi davranır. Dikkat: $fillable veya $guarded özelliklerinden yalnızca birini kullanabilirsiniz, aynı anda kullanamazsınız. Aşağıdaki örnekte, price özelliği dışındaki tüm özelliklere toplu atama izni verilir:
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

Eğer tüm özelliklere toplu atama izni vermek istiyorsanız, $guarded'ı boş bir dizi olarak tanımlayabilirsiniz:
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
Burada, oluşturmak veya güncellemek için kullanabileceğiniz iki toplu atama yöntemi bulunmaktadır: firstOrCreate ve firstOrNew. firstOrCreate yöntemi, belirli bir anahtar/değer çiftiyle veritabanında veri eşleştirmeye çalışacaktır. Model veritabanında bulunamazsa, verilen ilk parametre özelliklerini ve isteğe bağlı olarak ikinci parametre özelliklerini içeren bir kayıt oluşturacaktır.

firstOrNew yöntemi, firstOrCreate yöntemi gibi belirtilen özelliklere göre veritabanında kayıt arayacaktır. Ancak, firstOrNew yöntemi eşleşen model bulunamazsa yeni bir model örneği döndürecektir. Unutmayın ki firstOrNew tarafından döndürülen model örneği henüz veritabanına kaydedilmemiştir, kaydetmek için save yöntemini elle çağırmanız gerekir:
```php
// name'e göre uçuş ara, bulunamazsa oluştur...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// name ve delayed özellikleri ve arrival_time özelliği ile uçuş ara, bulunamazsa oluştur...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// name'e göre uçuş ara, bulunamazsa örnek oluştur...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// name ve delayed özellikleri ve arrival_time özelliği ile uçuş ara, bulunamazsa örnek oluştur...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

Ayrıca, mevcut bir modeli güncellemek veya oluşturmak isteyebilirsiniz. Bu durumu tek adımda gerçekleştirmek için updateOrCreate yöntemini kullanabilirsiniz. firstOrCreate yöntemi gibi, updateOrCreate modeli kalıcı hale getirir, bu nedenle save() yöntemini çağırmak gerekmez:
```php
// OakLand'dan San Diego'ya bir uçuş bulunursa, fiyatı 99 dolar olarak belirtin.
// Eşleşen bir model bulunamazsa yeni bir tane oluşturun.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Modeli Silme
Model örneği üzerinde delete yöntemini çağırarak bir örneği silebilirsiniz:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Anahtarına Göre Modeli Silme
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```
## Model Sorgusu İle Silme
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Model Kopyalama
Replicate yöntemini kullanarak henüz veritabanına kaydedilmemiş yeni bir örnek kopyalayabilirsiniz. Model örnekleri birçok ortak özelliği paylaştığında, bu yöntem çok kullanışlıdır.
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Örnek Sokak',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'fatura'
]);

$billing->save();
```

## Model Karşılaştırma
Bazı durumlarda, iki modelin "aynı" olup olmadığını kontrol etmek gerekebilir. is yöntemi, iki modelin aynı anahtara, tabloya ve veritabanı bağlantısına sahip olup olmadığını hızlı bir şekilde doğrulamak için kullanılabilir:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Model Gözlemcileri
Laravel'deki Model Event ve Gözlemci hakkında daha fazla bilgi için [Laravel'deki Model Event ve Gözlemci](https://learnku.com/articles/6657/model-events-and-observer-in-laravel) bölümünü inceleyebilirsiniz.

Not: Eloquent ORM'ın model gözlemcilerini desteklemesi için "illuminate/events" paketinin ayrıca yüklenmesi gerekir.
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
