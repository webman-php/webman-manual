## ThinkORM

### ThinkORM'un Kurulumu

`composer require -W webman/think-orm`

Kurulumdan sonra yeniden başlatma gereklidir (reload geçersiz).

> **Not**
> Kurulum başarısız olursa, muhtemelen composer vekil kullanıyorsunuzdur. `composer config -g --unset repos.packagist` komutunu çalıştırmayı deneyerek composer vekilini kapatmayı deneyin.

> [webman/think-orm](https://www.workerman.net/plugin/14) aslında `toptink/think-orm`'un otomatik kurulumunu yapan bir eklentidir. Eğer webman sürümünüz `1.2`'den düşükse bu eklentiyi kullanamazsınız, bu durumda [think-orm'u manuel olarak kurma ve yapılandırma](https://www.workerman.net/a/1289) makalesine bakabilirsiniz.

### Yapılandırma Dosyası
Gerçek duruma göre yapılandırma dosyasını `config/thinkorm.php` şeklinde değiştirin.

### Kullanım

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### Model Oluşturma

ThinkOrm modeli `think\Model` sınıfından türetilir, aşağıdaki gibi:

```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

Ayrıca aşağıdaki komut kullanılarak thinkorm tabanlı model oluşturulabilir:

```php
php webman make:model table_name
```

> **Not**
> Bu komutun çalışması için `webman/console` kurulu olmalıdır. Kurulum komutu `composer require webman/console ^1.2.13` şeklindedir.

> **Dikkat**
> Eğer make:model komutu ana projenin `illuminate/database`'i kullandığını tespit ederse, `illuminate/database` tabanlı model dosyası oluşturur, thinkorm yerine. Bu durumda, tp parametresi ekleyerek think-orm modelini zorunlu olarak oluşturabilirsiniz, komut şöyle olur: `php webman make:model table_name tp` (Eğer çalışmazsa, `webman/console` sürümünü güncelleyin).
