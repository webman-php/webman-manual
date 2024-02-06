# Özel Betikler

Bazı durumlarda geçici betikler yazmamız gerekebilir. Bu betiklerde webman gibi herhangi bir sınıfı veya arayüzü çağırarak veri aktarma, veri güncelleme ve istatistiksel işlemler gibi işlemleri gerçekleştirebiliriz. Webman'de bunun yapıması çok kolaydır, örneğin:

**Yeni bir `scripts/update.php` oluşturun** (dizin yoksa kendiniz oluşturun)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Tabii ki, aynı işlemi gerçekleştirmek için `webman/console` özel komutlarını kullanabiliriz. Daha fazla bilgi için [Komut Satırı](../plugin/console.md) bölümüne bakınız.
