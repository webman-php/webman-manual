## ThinkORM

### ThinkORM'un Kurulumu

`composer require -W webman/think-orm`

Kurulumdan sonra restart(reload geçersiz) yapmanız gereklidir.

> **İpucu**
> Kurulum başarısız olursa, muhtemelen composer proxy kullandığınızdan kaynaklanmıştır, deneyin `composer config -g --unset repos.packagist` komutunu çalıştırarak composer proxy'yi kaldırın.

> [webman/think-orm](https://www.workerman.net/plugin/14) aslında `toptink/think-orm`'ın otomatik olarak kurulması için bir eklentidir. Eğer webman sürümünüz `1.2`'den düşükse bu eklentiyi kullanamazsınız, bu durumda [think-orm'u manuel olarak kurma ve yapılandırma](https://www.workerman.net/a/1289) yazısına bakabilirsiniz.

### Yapılandırma Dosyası
Gerçek duruma göre `config/thinkorm.php` yapılandırma dosyasını düzenleyin.

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

ThinkOrm modeli `think\Model`'den miras alır, aşağıdaki gibi bir örnekle;

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

Ayrıca aşağıdaki komutu kullanarak thinkorm tabanlı model oluşturabilirsiniz;

```shell
php webman make:model tablo_adı
```

> **İpucu**
> Bu komut için `webman/console`'ın yüklenmesi gereklidir, kurulum komutu `composer require webman/console ^1.2.13` şeklindedir.

> **Dikkat**
> make:model komutu ana projenin `illuminate/database` kullandığını algılarsa, `illuminate/database` tabanlı model dosyaları oluşturur, think-orm tabanlı oluşturmak için tp parametresini ekleyerek komutu şu şekilde çalıştırabilirsiniz `php webman make:model tablo_adı tp` (Eğer çalışmazsa `webman/console`'ı güncelleyin)
