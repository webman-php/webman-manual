# Sorgu Oluşturucu
## Tüm satırları al
```php
<?php
namespace app\controller;

kullanmak destek\Request;
kullanmak destek\Db;

class UserController
{
    genel işlev tüm(Request $istek)
    {
        $kullanıcılar = Db::table('kullanıcılar')->al();
        return görünüm('kullanıcı/hepsi', ['kullanıcılar' => $kullanıcılar]);
    }
}
```

## Belirli sütunları al
```php
$kullanıcılar = Db::table('kullanıcı')->seç('ad', 'email as user_email')->al();
```

## Bir satır al
```php
$kullanıcı = Db::table('kullanıcılar')->nerede('ad', 'John')->ilk();
```

## Bir sütun al
```php
$başlıklar = Db::table('roller')->pluk('başlık');
```
Belirli bir indeks olarak id sütununu belirt
```php
$roller = Db::table('roller')->pluk('başlık', 'id');

foreach ($roller olarak $id => $başlık) {
    echo $başlık;
}
```

## Tek bir değer (alan) al
```php
$email = Db::table('kullanıcılar')->nerede('ad', 'John')->değer('email');
```

## Tekrarları Kaldır
```php
$email = Db::table('kullanıcı')->seç('rumuz')->farklı()->al();
```

## Sonuçları Parçala
Eğer binlerce veya daha fazla veritabanı kaydını işlemek gerekiyorsa, bu kayıtları tek seferde okumak zaman alıcı olabilir ve bellek limitinin aşılmasına neden olabilir, bu durumda chunkById metodunu kullanmayı düşünebilirsiniz. Bu metod, sonuç kümesini küçük parçalara böler ve bunları kapanış fonksiyonuna ileterek işlem yapmanıza olanak tanır. Örneğin, tüm kullanıcılar tablosu verilerini bir seferde 100 kayıt işleyecek şekilde kesip parçalayabiliriz:
```php
Db::table('kullanıcılar')->sırala('id')->chunkById(100, function ($kullanıcılar) {
    foreach ($kullanıcılar olarak $kullanıcı) {
        //
    }
});
```
Parçaları almayı durdurmak için false döndürebilirsiniz.
```php
Db::table('kullanıcılar')->sırala('id')->chunkById(100, function ($kullanıcılar) {
    // Kayıtları işleyin...

    return false;
});
```

> Not: Geri aramada veri silmeyin, bu bazı kayıtların sonuç kümesine dahil edilmemesine neden olabilir

## Birleştirme
Sorgu oluşturucu ayrıca sayma, max, min, avg, sum vb. gibi çeşitli birleştirme yöntemleri sağlar.
```php
$kullanıcılar = Db::table('kullanıcılar')->say();
$fiyat = Db::table('siparişler')->maks('fiyat');
$fiyat = Db::table('siparişler')->nerede('sonlandırıldı', 1)->ortalama('fiyat');
```

## Kayıt var mı yok mu kontrolü
```php
return Db::table('siparişler')->nerede('sonlandırıldı', 1)->var();
return Db::table('siparişler')->nerede('sonlandırıldı', 1)->yok();
```

## Doğal İfade
Prototip
```php
selectRaw($ifade, $bağlantılar = [])
```
Bazı durumlarda sorguda doğal ifade kullanmanız gerekebilir. Bir doğal ifade oluşturmak için `selectRaw()` yi kullanabilirsiniz:

```php
$siparişler = Db::table('siparişler')
                ->selectRaw('fiyat * ? as vergili_fiyat', [1.0825])
                ->al();
```

Aynı şekilde `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` ve `groupByRaw()` doğal ifade yöntemleri de sağlanmaktadır.

`Db::raw($değer)` da bir doğal ifade oluşturmak için kullanılır, ancak bağlama parametre işlevi yoktur, kullanırken SQL enjeksiyonu konusunda dikkatli olunması gerekir.
```php
$siparişler = Db::table('siparişler')
                ->seç('departman', Db::raw('SUM(fiyat) as toplam_satış'))
                ->groupBy('departman')
                ->havingRaw('SUM(fiyat) > ?', [2500])
                ->al();
```

## Join İfadesi
```php
// join
$kullanıcılar = Db::table('kullanıcılar')
            ->join('iletisim', 'kullanıcılar.id', '=', 'iletisim.kullanıcı_id')
            ->join('siparişler', 'kullanıcılar.id', '=', 'siparişler.kullanıcı_id')
            ->seç('kullanıcılar.*', 'iletisim.telefon', 'siparişler.fiyat')
            ->al();

// leftJoin            
$kullanıcılar = Db::table('kullanıcılar')
            ->leftJoin('gönderiler', 'kullanıcılar.id', '=', 'gönderiler.kullanıcı_id')
            ->al();

// rightJoin
$kullanıcılar = Db::table('kullanıcılar')
            ->rightJoin('gönderiler', 'kullanıcılar.id', '=', 'gönderiler.kullanıcı_id')
            ->al();

// crossJoin    
$kullanıcılar = Db::table('boyutlar')
            ->crossJoin('renkler')
            ->al();
```

## Birleştirme İfadesi
```php
$ilk = Db::table('kullanıcılar')
            ->neredeNull('ad');

$kullanıcılar = Db::table('kullanıcılar')
            ->neredeNull('soyadı')
            ->birleş($ilk)
            ->al();
```

## Where İfadesi
Prototip 
```php
nerede($kolon, $operatör = null, $değer = null)
```
İlk parametre sütun adı, ikinci parametre herhangi bir veritabanı sistem tarafından desteklenen operatör, üçüncüsü ise sütunun karşılaştırılacak değeri.
```php
$kullanıcılar = Db::table('kullanıcılar')->nerede('oy', '=', 100)->al();

// Operatör eşittir olduğunda bu ifadeyle aynı etkiye sahip olduğundan, yukarıdaki ifadeyle aynı işlemi yapar
$kullanıcılar = Db::table('kullanıcılar')->nerede('oy', 100)->al();

$kullanıcılar = Db::table('kullanıcılar')
                ->nerede('oy', '>=', 100)
                ->al();

$kullanıcılar = Db::table('kullanıcılar')
                ->nerede('oy', '<>', 100)
                ->al();

$kullanıcılar = Db::table('kullanıcılar')
                ->nerede('ad', 'like', 'T%')
                ->al();
```

where fonksiyonuna bir dizi koşulunu iletin:
```php
$kullanıcılar = Db::table('kullanıcılar')->nerede([
    ['durum', '=', '1'],
    ['abone', '<>', '1'],
])->al();
```

orWhere fonksiyonu ve where fonksiyonu aynı parametreleri alır:
```php
$kullanıcılar = Db::table('kullanıcılar')
                    ->nerede('oy', '>', 100)
                    ->veya('ad', 'John')
                    ->al();
```

orWhere fonksiyonuna bir kapanış fonksiyonu ilk parametre olarak iletilebilir:
```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$kullanıcılar = Db::table('kullanıcılar')
            ->nerede('oy', '>', 100)
            ->veya(function($sorgu) {
                $sorgu->nerede('ad', 'Abigail')
                      ->nerede('oy', '>', 50);
            })
            ->al();
```

whereBetween / orWhereBetween fonksiyonu, alan değerinin verilen iki değer arasında olup olmadığını doğrular:
```php
$kullanıcılar = Db::table('kullanıcılar')
           ->whereBetween('oy', [1, 100])
           ->al();
```

whereNotBetween / orWhereNotBetween fonksiyonu, alan değerinin verilen iki değer arasında olmadığını doğrular:
```php
$kullanıcılar = Db::table('kullanıcılar')
                    ->whereNotBetween('oy', [1, 100])
                    ->al();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn fonksiyonu, alanın değerlerinin belirli bir dizi içinde olması gerektiğini doğrular:
```php
$kullanıcılar = Db::table('kullanıcılar')
                    ->neredeGirişte('id', [1, 2, 3])
                    ->al();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull fonksiyonu, belirtilen alanın NULL olması gerektiğini doğrular:
```php
$kullanıcılar = Db::table('kullanıcılar')
                    ->whereNull('güncellenme_tarihi')
                    ->al();
```

whereNotNull fonksiyonu, belirtilen alanın NULL olmaması gerektiğini doğrular:
```php
$kullanıcılar = Db::table('kullanıcılar')
                    ->whereNotNull('güncellenme_tarihi')
                    ->al();
```

whereDate / whereMonth / whereDay / whereYear / whereTime fonksiyonları, alanın değerini belirtilen tarihle karşılaştırır:
```php
$kullanıcılar = Db::table('kullanıcılar')
                ->whereDate('oluşturma_tarihi', '2016-12-31')
                ->al();
```

whereColumn / orWhereColumn fonksiyonu, iki alanın değerinin eşit olup olmadığını kontrol eder:
```php
$kullanıcılar = Db::table('kullanıcılar')
                ->whereColumn('ad', 'soyad')
                ->al();
                
// Karşılaştırma operatörü de iletilir
$kullanıcılar = Db::table('kullanıcılar')
                ->whereColumn('güncellenme_tarihi', '>', 'oluşturma_tarihi')
                ->al();
                
// whereColumn fonksiyonuna bir dizi de geçebilirsiniz
$kullanıcılar = Db::table('kullanıcılar')
                ->whereColumn([
                    ['ad', '=', 'soyad'],
                    ['güncellenme_tarihi', '>', 'oluşturma_tarihi'],
                ])->al();

```

Parametrelerin gruplar halinde verilmesi
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$kullanıcılar = Db::table('kullanıcılar')
           ->nerede('ad', '=', 'John')
           ->nerede(function ($sorgu) {
               $sorgu->nerede('oy', '>', 100)
                     ->veya('unvan', '=', 'Admin');
           })
           ->al();
```

whereExists
```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$kullanıcılar = Db::table('kullanıcılar')
           ->whereExists(function ($sorgu) {
               $sorgu->seç(Db::raw(1))
                     ->from('siparişler')
                     ->whereRaw('siparişler.user_id = users.id');
           })
           ->al();
```

## orderBy
```php
$kullanıcılar = Db::table('kullanıcılar')
                ->orderBy('ad', 'desc')
                ->al();
```

## Rastgele Sıralama
```php
$rastgeleKullanıcı = Db::table('kullanıcılar')
                ->rastgeleSırala()
                ->ilk();
```
> Rastgele sıralama, sunucu performansını ciddi şekilde etkileyebileceğinden, kullanılması önerilmez

## groupBy / having
```php
$kullanıcılar = Db::table('kullanıcılar')
                ->grupBy('hesap_id')
                ->having('hesap_id', '>', 100)
                ->al();
// grupBy metoduna birden fazla parametre iletebilirsiniz
$kullanıcılar = Db::table('kullanıcılar')
                ->grupBy('ad', 'durum')
                ->having('hesap_id', '>', 100)
                ->al();
```

## ofset / limit
```php
$kullanıcılar = Db::table('kullanıcılar')
                ->ofset(10)
                ->limit(5)
                ->al();
```
## Ekleme
Tek bir satır ekleme
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Çoklu ekleme
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Otomatik Artan Kimlik
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Not: PostgreSQL kullanırken, insertGetId yöntemi otomatik artan alan adı olarak varsayılan olarak id'yi alacaktır. Başka "dizi"den bir ID almak istiyorsanız, insertGetId yöntemine ikinci parametre olarak alan adını iletebilirsiniz.

## Güncelleme
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Güncelle veya Ekle
Bazen mevcut kayıtları güncellemek isteyebilir veya eşleşen bir kayıt yoksa oluşturmak isteyebilirsiniz:

```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert yöntemi ilk parametredeki anahtar ve değer çiftlerini kullanarak eşleşen veritabanı kaydını aramayı deneyecektir. Kayıt bulunursa, ikinci parametredeki değerleri kullanarak kaydı günceller. Kayıt bulunamazsa, yeni bir kayıt ekler ve yeni kaydın verileri iki dizi toplamı olur.

## Artır & Azalt
Bu iki yöntem değiştirilmek istenen sütunu en az bir parametre alır. İkinci parametre isteğe bağlıdır ve sütunun artışını veya azalışını kontrol etmek için kullanılır:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Operasyon sırasında güncellenen alanı belirtebilirsiniz:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Silme
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Tabloyu temizlemeniz gerekiyorsa, tüm satırları silebileceğiniz ve otomatik artan kimliği sıfırlayabileceğiniz truncate yöntemini kullanabilirsiniz:
```php
Db::table('users')->truncate();
```

## Kötümser Kilitleme
Sorgu oluşturucusu, "kötümser kilitleme"yi "seç" sözdiziminde gerçekleştirmenize yardımcı olacak bazı yöntemler içerir. Bir "paylaşılan kilidi" sorgusunda kullanmak istiyorsanız sharedLock yöntemini kullanabilirsiniz. Paylaşılan kilitleme, seçilen veri sütunlarının, işlem tamamlanana kadar değiştirilmesini önler:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Veya lockForUpdate yöntemini kullanabilirsiniz. "Güncelleme" kilidini kullanarak diğer paylaşılan kilitlerin satırın değiştirilmesini veya seçilmesini önlemesini sağlar:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Hata Ayıklama
Sorgu sonuçlarını veya SQL ifadelerini görmek için dd veya dump yöntemlerini kullanabilirsiniz. dd yöntemi hata ayıklama bilgilerini gösterir ve daha sonra isteği durdurur. dump yöntemi de hata ayıklama bilgilerini gösterir ancak isteği durdurmaz:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Not**
> Hata ayıklama için `symfony/var-dumper` kurulu olmalıdır, kurmak için `composer require symfony/var-dumper` komutunu kullanabilirsiniz.
