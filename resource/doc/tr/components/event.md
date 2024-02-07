# event Olay İşleme

`webman/event`, iş koduna müdahale etmeden bazı iş mantığını gerçekleştirmenizi ve iş modülleri arasındaki bağı çözmenizi sağlayan zarif bir olay mekanizması sunar.

## Yükleme
`composer require webman/event`

## Olayı Abone Olma
Olaylara abone olma, `config/event.php` dosyası aracılığıyla yapılandırılır:
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...diğer olay işleme fonksiyonları...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...diğer olay işleme fonksiyonları...
    ]
];
```
**Açıklama:**
- `user.register`, `user.logout` vb. birinci dereceden olay isimleridir. String türündedir, küçük harflerle ve nokta (.) ile ayrılmış şekilde önerilir.
- Bir olayın birden fazla olay işleme fonksiyonu olabilir. Çağrı sırası yapılandırmaya göredir.

## Olay İşleme Fonksiyonları
Olay işleme fonksiyonları herhangi bir sınıf yöntemi, fonksiyon, kapanış fonksiyonu vb. olabilir.
Örneğin `app/event/User.php` adında bir olay işleme sınıfı oluşturulabilir (dizin mevcut değilse kendiniz oluşturun):
```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## Olayı Yayınlama
`Event::emit($event_name, $data);` kullanarak olayı yayınlama, örneğin:
```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```
> **İpucu**
> `Event::emit($event_name, $data);` parametre olan `$data`, dizi, sınıf örneği, dize vb. olabilir.

## Jokar Olay Dinleme
Jokar abonelik dinleyicisi aynı dinleyiciye birden fazla olayı işlemek için izin verir, örneğin `config/event.php` içinde yapılandırılmıştır:
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
Olay işleme fonksiyonu ikinci parametre olan `$event_data` ile belirli olay adını alabilir:
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // Özel olay adı, örneğin user.register user.logout vb.
        var_export($user);
    }
}
```
## Olay Yayınını Durdurma
Olay işleme fonksiyonu içinde `false` döndürdüğümüzde, olay yayını durur.

## Kapanış İşleviyle Olay İşleme
Olay işleme fonksiyonları bir sınıf yöntemi olabilir, aynı zamanda kapanış fonksiyonu da olabilir, örneğin:
```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```
## Olayları ve Dinleyicileri Görüntüleme
`php webman event:list` komutunu kullanarak proje yapılandırmasında bulunan tüm olayları ve dinleyicileri görebilirsiniz.

## Dikkat Edilmesi Gerekenler
Olay işleme asenkronik değildir; dolayısıyla yavaş işlemler için uygun değildir. Yavaş işlemler için [webman/redis-queue](https://www.workerman.net/plugin/12) gibi bir mesaj kuyruğu kullanılmalıdır.
