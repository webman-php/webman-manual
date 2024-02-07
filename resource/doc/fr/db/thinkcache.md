## ThinkCache

### Installation de ThinkCache
`composer require -W webman/think-cache`

Après l'installation, il est nécessaire de ***restart*** (rechargement invalide)

> [webman/think-cache](https://www.workerman.net/plugin/15) est en fait un plugin d'installation automatisée de `toptink/think-cache`.

> **Remarque**
> toptink/think-cache ne prend pas en charge PHP 8.1

### Fichier de configuration

Le fichier de configuration se situe dans `config/thinkcache.php`

### Utilisation

```php
<?php
namespace app\controller;
 
use support\Request;
use think\facade\Cache;
 
class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

### Documentation d'utilisation de Think-Cache

[Adresse de la documentation de ThinkCache](https://github.com/top-think/think-cache)
