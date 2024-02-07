# Uygulama Eklentisi Geliştirme Standartları

## Uygulama Eklentisi Gereksinimleri
* Eklentiler telif hakkı ihlali içeren kod, simge veya görüntü içeremez.
* Eklenti kaynak kodu şifrelenmemiş ve eksiksiz olmalıdır.
* Eklenti tam işlevsellik sağlamalı, basit bir işlev olmamalıdır.
* Tam işlevsellik tanıtımı ve belgeler sunulmalıdır.
* Eklentiler alt pazarı içeremez.
* Eklenti içinde herhangi bir metin veya tanıtım bağlantısı bulunmamalıdır.

## Uygulama Eklentisi Tanımlama
Her uygulama eklentisi benzersiz bir tanımlayıcıya sahiptir, bu tanımlayıcı harflerden oluşur. Bu tanımlayıcı, eklentinin kaynak kodunun bulunduğu dizini, sınıfın ad alanını ve eklenti veritabanı tablo öneki etkiler.

Geliştirici, örneğin `foo` olarak bir eklenti tanımlayıcısı kullanıyorsa, eklentinin kaynak kodunun bulunduğu dizin `{main_project}/plugin/foo` olacak, ilgili eklenti sınıfının ad alanı `plugin\foo` ve tablo öneki `foo_` olacaktır.

Tanımlayıcı benzersiz olduğu için, geliştirici geliştirmeye başlamadan önce tanımlayıcının kullanılabilir olup olmadığını kontrol etmelidir. Kontrol adresi [Uygulama Tanımlayıcı Kontrolü](https://www.workerman.net/app/check).

## Veritabanı
* Tablo adları küçük harf `a-z` ve alt çizgi `_` içermelidir.
* Eklenti veri tabloları, eklenti tanımlayıcı ile öneklenmelidir; örneğin foo eklentisinin makale tablosu `foo_article` olmalıdır.
* Tablo anahtarları endeks olarak id olarak belirlenmelidir.
* Depolama motoru olarak innodb motoru kullanılmalıdır.
* Karakter seti olarak utf8mb4_general_ci kullanılmalıdır.
* Veritabanı ORM kullanımı için laravel veya think-orm tercih edilmelidir.
* Zaman alanlarında DateTime kullanımı tavsiye edilir.

## Kod Standartları

#### PSR Standartları
Kodlar PSR4 yükleme standartlarına uygun olmalıdır.

#### Sınıf Adlandırmaları Büyük Harfle Başlayan CamelCase Olmalıdır
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Sınıfın Özellikleri ve Metodları Küçük Harfle Başlayan CamelCase Olmalıdır
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Yetkilendirmeye gerek olmayan metodlar
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
Sınıfın özellikleri ve fonksiyonları, genel bir açıklama, parametreler ve dönüş türü içeren yorumları içermelidir.

#### Girintileme
Kodlar, bir tab yerine 4 boşluk kullanılarak girinti yapılmalıdır.

#### Akış Kontrolü
Akış kontrol anahtar kelimeleri (if for while foreach vb.) ardından bir boşluk bırakılmalı, akış kontrol kodları başlangıç parantezleri bitiş parantezlerinin aynı satırda olacak şekilde olmalıdır.
```php
foreach ($users as $uid => $user) {

}
```

#### Geçici Değişken Adları
Küçük harfle başlayan CamelCase kullanımı önerilir (zorunlu değil)
```php
$articleCount = 100;
```
