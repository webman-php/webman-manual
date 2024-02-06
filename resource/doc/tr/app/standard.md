# Uygulama Eklentisi Geliştirme Standartları

## Uygulama Eklentisi Gereksinimleri
* Eklentiler telif hakkı ihlali içeren kod, simge veya görüntü içeremez
* Eklenti kaynak kodu şifreli olamaz ve tamamlanmış bir kod içermelidir
* Eklenti tamamlanmış bir işlevsellik sunmalıdır, basit bir işlevsellik olmamalıdır
* Tam işlevsellik sunabilmek için eksiksiz bir işlevsellik tanıtımı ve belgesi sunulmalıdır
* Eklenti alt pazar içeremez
* Eklenti içinde herhangi bir metin veya tanıtım bağlantısı bulunmamalıdır

## Uygulama Eklentisi Kimliği
Her uygulama eklentisi benzersiz bir kimliğe sahiptir, bu kimlik harflerden oluşur. Bu kimlik, uygulama eklentisinin kaynak kodunun bulunduğu dizini, sınıfın ad alanını ve eklenti veritabanı tablo öneki etkiler.

Örneğin, geliştirici "foo" olarak bir eklenti kimliği kullandığında, eklenti kaynak kodu dizini `{ana_proje}/plugin/foo` olarak olur, ilgili eklenti ad alanı `plugin\foo` olur ve tablo öneki `foo_` olur.

Kimlik global olarak benzersiz olduğundan, geliştiricilerin geliştirmeden önce kimliğin kullanılabilirliğini kontrol etmeleri gerekmektedir. Kontrol adresi [Uygulama Kimliği Kontrolü](https://www.workerman.net/app/check).

## Veritabanı
* Tablo adları küçük harfli `a-z` ve alt çizgi `_` kullanarak oluşturulmalıdır
* Eklenti veritabanı tabloları eklenti kimliği ile öneklenmelidir, örneğin "foo" eklentinin makale tablosu `foo_article` olmalıdır
* Tablo anahtarları id olarak belirtilmelidir
* Depolama motoru olarak innodb motoru kullanılmalıdır
* Karakter seti olarak utf8mb4_general_ci kullanılmalıdır
* Veritabanı ORM'si olarak laravel veya think-orm kullanılabilir
* Zaman alanları için DateTime kullanılması önerilir

## Kod Standartları

#### PSR Standartları
Kodlar PSR4 yükleme standartlarına uygun olmalıdır

#### Sınıfların Adlandırılması
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Sınıf özellikleri ve yöntemleri küçük harfle başlayan camelCase şeklinde olmalıdır
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Kimlik doğrulamaya ihtiyaç duymayan yöntemler
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Yorumları al
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Yorumlar
Sınıf özellikleri ve fonksiyonları genel bir bakış, parametreler, dönüş türü içermelidir

#### Girintileme
Kodlar 4 boşluk kullanılarak girintilenmelidir, tab kullanılmamalıdır

#### Akış kontrolü
Akış kontrol anahtar kelimesi (if for while foreach gibi) sonrasında bir boşluk bırakılmalıdır, akış kontrol kod bloğu parantezleri bitiş paranteziyle aynı satırda olmalıdır.
```php
foreach ($users as $uid => $user) {

}
```

#### Geçici Değişken İsimleri
Küçük harfle başlayan camelCase şeklinde adlandırılması önerilir (zorunlu değildir)
```php
$articleCount = 100;
```
