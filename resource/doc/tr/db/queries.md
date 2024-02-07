# Sorgu Oluşturucu
## Tüm satırları alın
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

## Belirli sütunları alın
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Bir satır alın
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Bir sütun alın
```php
$titles = Db::table('roles')->pluck('title');
```
Belirli bir id alanını index olarak belirtin
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Tek bir değeri (sütunu) alın
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Tekrar edenleri kaldırın
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Sonuçları parçalara ayırın
Eğer binlerce kayıtla uğraşmanız gerekiyorsa, tüm verileri bir seferde almak zaman alır ve genellikle hafızayı aşırı yükler, bu durumda chunkById yöntemini düşünebilirsiniz. Bu yöntem, sonuç kümesini küçük parçalar halinde alır ve bunları bir kapanış fonksiyonuna işlemek üzere iletilir. Örneğin, tüm kullanıcılar tablosunu 100 kayıtlık küçük parçalara bölebiliriz:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Parçaları almayı durdurmak için kapanış fonksiyonunda false döndürebilirsiniz.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Kayıtları işleyin...

    return false;
});
```
> Not: Geri çağrıda veri silmeyin, bu bazı kayıtların sonuç kümesine dahil edilmemesine neden olabilir

## Birleştirme
Sorgu oluşturucu, count, max, min, avg, sum vb. gibi çeşitli birleştirme yöntemleri de sağlar.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Kaydın var olup olmadığını kontrol edin
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Doğal İfade
Prototip
```php
selectRaw($expression, $bindings = [])
```
Ara sıra sorguda doğal ifadeler kullanmanız gerekebilir. `selectRaw()` ile doğal bir ifade oluşturabilirsiniz:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```
Aynı şekilde, `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` doğal ifade yöntemleri de mevcuttur.

`Db::raw($value)` ayrıca bir doğal ifade oluşturmak için kullanılabilir, ancak bağlantı işlevselliği bulunmamaktadır, kullanırken SQL enjeksiyonuna dikkat etmelisiniz.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Birleştirme İfadeleri
```php
// join
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

## Birleştirme İfadeleri
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Where İfadesi
Prototip
```php
where($sütun, $operatör = null, $değer = null)
```
İlk parametre sütun adı, ikinci parametre herhangi bir veritabanı sisteminin desteklediği bir operatör ve üçüncüsü ise sütunun karşılaştırılacak değeri.
```php
$users = Db::table('kullanıcılar')->where('oylar', '=', 100)->get();

// Operatör eşittir olduğunda, bu yüzden bu ifade öncekiyle aynı etkiye sahiptir
$users = Db::table('kullanıcılar')->where('oylar', 100)->get();

$users = Db::table('kullanıcılar')
                ->where('oylar', '>=', 100)
                ->get();

$users = Db::table('kullanıcılar')
                ->where('oylar', '<>', 100)
                ->get();

$users = Db::table('kullanıcılar')
                ->where('ad', 'like', 'T%')
                ->get();
```

Ayrıca where fonksiyonuna bir dizi koşul geçirebilirsiniz:
```php
$users = Db::table('kullanıcılar')->where([
    ['durum', '=', '1'],
    ['abone', '<>', '1'],
])->get();
```

orWhere yöntemi where yöntemi ile aynı parametreleri alır:
```php
$users = Db::table('kullanıcılar')
                    ->where('oylar', '>', 100)
                    ->orWhere('ad', 'John')
                    ->get();
```

orWhere yöntemine bir kapanışı birinci parametre olarak geçirebilirsiniz:
```php
// SQL: select * from kullanıcılar where oylar > 100 or (ad = 'Abigail' and oylar > 50)
$users = Db::table('kullanıcılar')
            ->where('oylar', '>', 100)
            ->orWhere(function($sorgu) {
                $sorgu->where('ad', 'Abigail')
                      ->where('oylar', '>', 50);
            })
            ->get();
```

whereBetween / orWhereBetween yöntemleri alanın değerinin belirtilen iki değer arasında olup olmadığını kontrol eder:
```php
$users = Db::table('kullanıcılar')
           ->whereBetween('oylar', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween yöntemleri alanın değerinin belirtilen iki değer arasında olmadığını kontrol eder:
```php
$users = Db::table('kullanıcılar')
                    ->whereNotBetween('oylar', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn yöntemleri alanın değerinin belirtilen dizinin içinde olması gerektiğini kontrol eder:
```php
$users = Db::table('kullanıcılar')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull yöntemleri belirtilen alanın NULL olması gerektiğini kontrol eder:
```php
$users = Db::table('kullanıcılar')
                    ->whereNull('güncelleme_tarihi')
                    ->get();
```

whereNotNull yöntemi belirtilen alanın NULL olmaması gerektiğini kontrol eder:
```php
$users = Db::table('kullanıcılar')
                    ->whereNotNull('güncelleme_tarihi')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime yöntemleri alanın değerini belirtilen tarih ile karşılaştırmak için kullanılır:
```php
$users = Db::table('kullanıcılar')
                ->whereDate('oluşturma_tarihi', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn yöntemleri iki alanın değerini karşılaştırmak için kullanılır:
```php
$users = Db::table('kullanıcılar')
                ->whereColumn('ilk_isim', 'son_isim')
                ->get();
                
// Bir karşılaştırma operatörü de geçirebilirsiniz
$users = Db::table('kullanıcılar')
                ->whereColumn('güncelleme_tarihi', '>', 'oluşturma_tarihi')
                ->get();
                
// whereColumn yöntemi ayrıca dizi geçirebilir
$users = Db::table('kullanıcılar')
                ->whereColumn([
                    ['ilk_isim', '=', 'son_isim'],
                    ['güncelleme_tarihi', '>', 'oluşturma_tarihi'],
                ])->get();
```

Parametre gruplama
```php
// select * from kullanıcılar where ad = 'John' and (oylar > 100 or unvan = 'Yönetici')
$users = Db::table('kullanıcılar')
           ->where('ad', '=', 'John')
           ->where(function ($sorgu) {
               $sorgu->where('oylar', '>', 100)
                     ->orWhere('unvan', '=', 'Yönetici');
           })
           ->get();
```

whereExists
```php
// select * from kullanıcılar where exists ( select 1 from siparişler where siparişler.kullanıcı_id = users.id )
$users = Db::table('kullanıcılar')
           ->whereExists(function ($sorgu) {
               $sorgu->select(Db::raw(1))
                     ->from('siparişler')
                     ->whereRaw('siparişler.kullanıcı_id = kullanıcılar.id');
           })
           ->get();
```

## Sıralama
```php
$users = Db::table('kullanıcılar')
                ->orderBy('ad', 'desc')
                ->get();
```

## Rastgele Sıralama
```php
$randomUser = Db::table('kullanıcılar')
                ->inRandomOrder()
                ->first();
```
> Rastgele sıralama sunucu performansını ciddi şekilde etkiler, kullanılması önerilmez

## groupBy / having
```php
$users = Db::table('kullanıcılar')
                ->groupBy('hesap_id')
                ->having('hesap_id', '>', 100)
                ->get();
// groupBy yöntemine birden çok parametre geçirebilirsiniz
$users = Db::table('kullanıcılar')
                ->groupBy('ilk_ad', 'durum')
                ->having('hesap_id', '>', 100)
                ->get();
```

## offset / limit
```php
$users = Db::table('kullanıcılar')
                ->offset(10)
                ->limit(5)
                ->get();
```

## Ekleme
Tek satır ekleme
```php
Db::table('kullanıcılar')->insert(
    ['email' => 'john@example.com', 'oylar' => 0]
);
```
Birden fazla satır ekleme
```php
Db::table('kullanıcılar')->insert([
    ['email' => 'taylor@example.com', 'oylar' => 0],
    ['email' => 'dayle@example.com', 'oylar' => 0]
]);
```

## Artan ID
```php
$id = Db::table('kullanıcılar')->insertGetId(
    ['email' => 'john@example.com', 'oylar' => 0]
);
```

> Not: PostgreSQL kullanırken, insertGetId yöntemi varsayılan olarak id'yi otomatik artan alan adı olarak alır. Diğer bir "dizi"den id almak istiyorsanız, insertGetId yöntemine ikinci parametre olarak alan adını iletebilirsiniz.

## Güncelleme
```php
$etkilenen = Db::table('kullanıcılar')
              ->where('id', 1)
              ->update(['oylar' => 1]);
```
## Güncelleme veya Ekleme
Bazen mevcut bir kaydı veritabanında güncellemek isteyebilir veya eğer eşleşen bir kayıt bulunmuyorsa oluşturmak isteyebilirsiniz:

```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```

`updateOrInsert` metodu ilk parametrenin anahtarlarını ve değerlerini kullanarak veritabanında eşleşen kayıtları bulmaya çalışacaktır. Eğer kayıt mevcutsa, ikinci parametredeki değerleri kullanarak kaydı günceller. Eğer kayıt bulunamazsa, yeni bir kayıt ekler ve yeni kaydın verileri bu iki array'in birleşimidir.

## Artırma & Azaltma
Bu iki method da en az bir parametre alır: değiştirilmesi gereken sütun. İkinci parametre ise isteğe bağlı olup, sütunun ne kadar arttırılacağını ya da azaltılacağını kontrol etmek içindir:
```php
Db::table('users')->increment('votes');
Db::table('users')->increment('votes', 5);
Db::table('users')->decrement('votes');
Db::table('users')->decrement('votes', 5);
```
Ayrıca, işlem sırasında güncellenmesi gereken alanları belirtebilirsiniz:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Silme
```php
Db::table('users')->delete();
Db::table('users')->where('votes', '>', 100)->delete();
```
Eğer tabloyu boşaltmanız gerekiyorsa, tüm satırları silecek ve artan kimliği sıfırlayacak olan `truncate` methodunu kullanabilirsiniz:
```php
Db::table('users')->truncate();
```

## Pessimistic Lock
Sorgu oluşturucu aynı zamanda "pessimistik kilit"i select sorgularında kullanmanıza olanak tanıyan bazı yöntemleri içerir. Bir "paylaşılan kilit" eklemek istiyorsanız, `sharedLock` methodunu kullanabilirsiniz. Paylaşılan kilit, seçilen veri satırlarının işlem tamamlanana kadar değiştirilmesini önler:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Ya da `lockForUpdate` methodunu kullanarak "update" kilidini ekleyebilirsiniz. "Update" kilidi, diğer paylaşılan kilitlerin satırları seçmesini veya değiştirmesini engeller:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Hata Ayıklama
Sorgu sonuçlarını veya SQL ifadelerini görüntülemek için `dd` veya `dump` methodlarını kullanabilirsiniz. `dd` methodunu kullanarak hata ayıklama bilgilerini görebilir ve ardından isteği duraklatabilirsiniz. `dump` methodu da hata ayıklama bilgilerini görüntüleyebilir ancak isteği durdurmayacaktır:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Not**
> Hata ayıklama için `symfony/var-dumper` paketini yüklemeniz gerekmektedir. Yüklemek için `composer require symfony/var-dumper` komutunu kullanabilirsiniz.
