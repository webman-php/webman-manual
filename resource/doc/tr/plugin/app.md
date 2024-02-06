# Uygulama Eklentileri
Her uygulama eklentisi, bir tam uygulamadır ve kaynak kodları `{ana proje}/plugin` dizinindedir.

> **İpucu**
> `php webman app-plugin:create {eklenti_adı}` komutunu kullanarak (webman/console>=1.2.16 gereklidir), yerelde bir uygulama eklentisi oluşturabilirsiniz,
> örneğin `php webman app-plugin:create cms` komutu aşağıdaki dizin yapısını oluşturur.

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Görüldüğü gibi, bir uygulama eklentisi, webman ile aynı dizin yapısına ve yapılandırma dosyalarına sahiptir. Aslında bir uygulama eklentisi geliştirmek, webman projesi geliştirmekle neredeyse aynı deneyimi sunar, yalnızca aşağıdaki bazı konulara dikkat etmek gerekir.

## Ad Alanı
Eklenti dizini ve adlandırma PSR4 standartlarına uygun olmalıdır, çünkü eklentiler genellikle plugin dizini altına yerleştirildiği için ad alanları genellikle plugin ile başlar, örneğin `plugin\cms\app\controller\UserController` şeklinde isimlendirilir, burada cms eklentinin kaynak ana dizinidir.

## URL Erişimi
Uygulama eklentisi URL adresi yolu her zaman `/app` ile başlar, örneğin `plugin\cms\app\controller\UserController` için URL adresi `http://127.0.0.1:8787/app/cms/user` şeklindedir.

## Statik Dosyalar
Statik dosyalar genellikle `plugin/{eklenti}/public` altına yerleştirilir, örneğin `http://127.0.0.1:8787/app/cms/avatar.png` adresine erişmek aslında `plugin/cms/public/avatar.png` dosyasını almak anlamına gelir.

## Yapılandırma Dosyaları
Eklentin yapılandırması normal webman projesi yapılandırması ile aynıdır, ancak eklentin yapılandırması genellikle yalnızca ilgili eklenti üzerinde etkilidir, genellikle ana projeye etki etmez.
Örneğin `plugin.cms.app.controller_suffix` değeri sadece eklentinin denetleyici soneki üzerinde etki eder, ana projeye etki etmez.
Örneğin `plugin.cms.app.controller_reuse` değeri sadece eklentinin denetleyicisinin tekrar kullanılıp kullanılmayacağını etkiler, ana projeye etki etmez.
Örneğin `plugin.cms.middleware` değeri sadece eklentinin orta yazılımlarını etkiler, ana projeye etki etmez.
Örneğin `plugin.cms.view` değeri sadece eklentinin kullandığı görünümü etkiler, ana projeye etki etmez.
Örneğin `plugin.cms.container` değeri sadece eklentinin kullandığı konteynırı etkiler, ana projeye etki etmez.
Örneğin `plugin.cms.exception` değeri sadece eklentinin istisna işleme sınıfını etkiler, ana projeye etki etmez.

Ancak, yönlendirme genellikle genel olarak kabul edildiği için, eklenti yapılandırmasının yönlendirmeleri de genel olarak kabul edilir.

## Yapılandırmayı Almak
Belirli bir eklenti yapılandırmasını almak için `config('plugin.{eklenti}.{belirli_yapılandırma}');` yöntemini kullanabilirsiniz, örneğin `config('plugin.cms.app')` ile `plugin/cms/config/app.php` dosyasının tüm yapılandırmalarını alabilirsiniz.
Aynı şekilde, ana proje veya diğer eklentiler de `config('plugin.cms.xxx')` ile cms eklentisinin yapılandırmasını alabilir.

## Desteklenmeyen Yapılandırmalar
Uygulama eklentileri `server.php` ve `session.php` yapılandırmalarını desteklemez, ayrıca `app.request_class`, `app.public_path` ve `app.runtime_path` yapılandırmalarını desteklemez.

## Veritabanı
Eklentiler kendi veritabanlarını yapılandırabilir, örneğin `plugin/cms/config/database.php` içeriği aşağıdaki gibidir
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql bağlantı adı
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'veritabanı',
            'username'    => 'kullanıcı_adı',
            'password'    => 'şifre',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin bağlantı adı
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'veritabanı',
            'username'    => 'kullanıcı_adı',
            'password'    => 'şifre',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Kullanım şekli `Db::connection('plugin.{eklenti}.{bağlantı_adı}');` şeklindedir, örneğin
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Eğer ana projenin veritabanını kullanmak isterseniz, doğrudan kullanabilirsiniz, örneğin
```php
use support\Db;
Db::table('user')->first();
// Varsayalım ki ana projede admin adında başka bir bağlantı ayarlamış olsun
Db::connection('admin')->table('admin')->first();
```

> **İpucu**
> thinkorm da benzer bir kullanıma sahiptir

## Redis
Redis, veritabanı kullanımı ile benzer şekilde kullanılır, örneğin `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
Kullanımı şu şekildedir
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Aynı şekilde, ana projenin Redis yapılandırmasını yeniden kullanmak istiyorsanız
```php
use support\Redis;
Redis::get('key');
// Varsayalım ki ana projede cache adında başka bir bağlantı ayarlamış olsun
Redis::connection('cache')->get('key');
```

## Günlük (Log)
Günlük kullanımı da veritabanı kullanımıyla benzerdir
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Eğer ana projenin günlük yapılandırmasını yeniden kullanmak istiyorsanız, doğrudan kullanabilirsiniz
```php
use support\Log;
Log::info('Log content');
// Varsayalım ki ana projede test adında başka bir günlük yapılandırması ayarlamış olsun
Log::channel('test')->info('Log content');
```

# Uygulama Eklentisi Kurulumu ve Kaldırılması
Uygulama eklentisini kurarken, eklenti dizinini `{ana proje}/plugin` dizinine kopyalamanız yeterlidir, etkin olabilmesi için reload veya restart yapmanız gerekir.
Kaldırmak istediğinizde, ilgili eklenti dizinini doğrudan `{ana proje}/plugin` dizininden silebilirsiniz.
