# Uygulama Eklentileri
Her bir uygulama eklentisi, `{ana proje}/plugin` dizini altına yerleştirilmiş tamamlanmış bir uygulamadır.

> **İpucu**
> Yerelde bir uygulama eklentisi oluşturmak için `php webman app-plugin:create {plugin_adı}` komutunu (webman/console>=1.2.16 gerektirir) kullanabilirsiniz, örneğin `php webman app-plugin:create cms` komutu aşağıdaki dizin yapısını oluşturacaktır

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Görüldüğü gibi bir uygulama eklentisi, webman ile aynı dizin yapısına ve yapılandırma dosyalarına sahiptir. Aslında bir uygulama eklentisi geliştirmek, webman projesi geliştirmekle neredeyse aynı deneyime sahiptir, sadece aşağıdaki birkaç konuya dikkat etmek gerekmektedir.

## Ad Alanları
Eklenti dizini ve adlandırma PSR4 standartlarına uygun olmalıdır, çünkü eklentiler plugin dizini altına yerleştirilir, bu nedenle ad alanları genellikle plugin ile başlar, örneğin `plugin\cms\app\controller\UserController`, burada cms eklentinin kaynak kodu ana dizinidir.

## URL Erişimi
Uygulama eklentisi URL adresi yolu her zaman `/app` ile başlar, örneğin `plugin\cms\app\controller\UserController` için URL adresi `http://127.0.0.1:8787/app/cms/user`'dır.

## Statik Dosyalar
Statik dosyalar `plugin/{eklenti}/public` dizinine yerleştirilir, örneğin `http://127.0.0.1:8787/app/cms/avatar.png` adresine erişmek aslında `plugin/cms/public/avatar.png` dosyasını almak anlamına gelir.

## Yapılandırma Dosyaları
Eklentinin yapılandırması, normal webman projesiyle aynıdır, ancak genellikle eklentinin yapılandırması yalnızca o eklenti için geçerlidir, ana projeyi genellikle etkilemez.
Örneğin `plugin.cms.app.controller_suffix` değeri sadece eklentinin denetleyici ek soneki üzerinde etkilidir, ana projeyi etkilemez.
Örneğin `plugin.cms.app.controller_reuse` değeri sadece eklentinin denetleyiciyi yeniden kullanıp kullanmama durumunu etkiler, ana projeyi etkilemez.
Örneğin `plugin.cms.middleware` değeri sadece eklentinin ara yazılımını etkiler, ana projeyi etkilemez.
Örneğin `plugin.cms.view` değeri sadece eklentinin kullandığı görünümü etkiler, ana projeyi etkilemez.
Örneğin `plugin.cms.container` değeri sadece eklentinin kullandığı konteynırı etkiler, ana projeyi etkilemez.
Örneğin `plugin.cms.exception` değeri sadece eklentinin istisna işleme sınıfını etkiler, ana projeyi etkilemez.

Ancak, çünkü yönlendirme globaldir, bu nedenle eklenti yapılandırmanın yönlendirme de global düzeyde etkili olacağını unutmamak gerekmektedir.

## Yapılandırmayı Almak
Belirli bir eklenti yapılandırmasını almak için `config('plugin.{eklenti}.{belirli_yapılandırma}');` yöntemi kullanılır, örneğin `config('plugin.cms.app')` ile `plugin/cms/config/app.php` dosyasının tüm yapılandırmasını alabilirsiniz.
Aynı şekilde ana proje veya diğer eklentiler, `config('plugin.cms.xxx')` kullanarak cms eklentisinin yapılandırmasını alabilir.

## Desteklenmeyen Yapılandırmalar
Uygulama eklentileri `server.php`, `session.php` yapılandırmalarını desteklemez, `app.request_class`, `app.public_path`, `app.runtime_path` yapılandırmalarını desteklemez.

## Veritabanı
Eklenti kendi veritabanını yapılandırabilir, örneğin `plugin/cms/config/database.php` dosyası şu içeriğe sahiptir
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql bağlantı adıdır
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'veritabanı',
            'username'    => 'kullanıcı_adı',
            'password'    => 'parola',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin bağlantı adıdır
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'veritabanı',
            'username'    => 'kullanıcı_adı',
            'password'    => 'parola',
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
Ana projenin veritabanını kullanmak istenirse, direkt olarak kullanılabilir, örneğin
```php
use support\Db;
Db::table('user')->first();
// Varsayalım ki ana projede admin bağlantısı da yapılandırılmış
Db::connection('admin')->table('admin')->first();
```

> **İpucu**
> thinkorm'un kullanımı da benzerdir

## Redis
Redis kullanımı veritabanıyla benzerdir, örneğin `plugin/cms/config/redis.php` dosyası şu içeriğe sahiptir
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
Kullanım şekli
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
Aynı şekilde, ana projenin Redis yapılandırmasını yeniden kullanmak istenirse
```php
use support\Redis;
Redis::get('key');
// Varsayalım ki ana projede cache bağlantısı da yapılandırılmış
Redis::connection('cache')->get('key');
```

## Günlük
Günlük sınıfının kullanımı veritabanı kullanımına benzer.

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Ana projenin günlük yapılandırmasını yeniden kullanmak istiyorsanız, doğrudan şunu kullanabilirsiniz:

```php
use support\Log;
Log::info('Günlük içeriği');
// Varsayalım ana projenin 'test' adında bir günlük yapılandırması var
Log::channel('test')->info('Günlük içeriği');
```

# Uygulama Eklentisi Kurulumu ve Kaldırılması
Uygulama eklentisi kurulumu, eklenti dizinini `{ana proje}/plugin` dizinine kopyalamanız yeterlidir; yeniden yükleme veya yeniden başlatma gereklidir.
Kaldırma işlemi, ilgili eklenti dizinini `{ana proje}/plugin` dizininden doğrudan silerek gerçekleştirilebilir.
