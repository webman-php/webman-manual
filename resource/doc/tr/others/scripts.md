# Özel Betik

Bazı durumlarda geçici betikler yazmamız gerekebilir, bu betikler webman gibi herhangi bir sınıfı veya arayüzü çağırarak veri içe aktarma, veri güncelleme ve istatistik hesaplama gibi işlemleri gerçekleştirebilir. Bu webman'de çok kolay bir işlemdir, örneğin:

**Yeni bir `scripts/update.php` oluşturun** (dizin yoksa kendiniz oluşturun)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Tabii ki, bu tür işlemleri gerçekleştirmek için `webman/console` özelleştirilmiş komutları da kullanabiliriz, bkz. [Command Line](../plugin/console.md)
