# Bağlam

`support\Context` sınıfı, istek bağlam verilerini depolamak için kullanılır; istek tamamlandığında ilgili bağlam verileri otomatik olarak silinir. Yani bağlam verilerinin ömrü istek ömrünü takip eder. `support\Context`, Fiber, Swoole ve Swow koşullu ortamlarını destekler.

Daha fazla bilgi için [webman Fiber](./fiber.md)'a bakın.

# Arabirim
Bağlam, aşağıdaki arabirimleri sağlar.

## Bağlam verisi ayarlama
```php
Context::set(string $name, $mixed $value);
```

## Bağlam verisi alma
```php
Context::get(string $name = null);
```

## Bağlam verisi silme
```php
Context::delete(string $name);
```

> **Not**
> Çerçeve, istek sonlandıktan sonra bağlam verilerini otomatik olarak yok etmek için Context::destroy() arabirimini otomatik olarak çağırır; iş mantığı manuel olarak Context::destroy() arabirimini çağıramaz.

# Örnek
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# Dikkat
**Koşululu kullanılırken**, **istekle ilgili durum verilerini** global değişkenlere veya statik değişkenlere depolamamalısınız, bu global veri kirliliğine neden olabilir; doğru kullanım, bunları depolamak ve almak için Context'i kullanmaktır.
