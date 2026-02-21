# Komut Satırı

Webman komut satırı bileşeni

## Kurulum
```
composer require webman/console
```

## İçindekiler

### Kod Üretimi
- [make:controller](#make-controller) - Denetleyici sınıfı oluştur
- [make:model](#make-model) - Veritabanı tablosundan model sınıfı oluştur
- [make:crud](#make-crud) - Tam CRUD oluştur (model + denetleyici + doğrulayıcı)
- [make:middleware](#make-middleware) - Ara katman sınıfı oluştur
- [make:command](#make-command) - Konsol komutu sınıfı oluştur
- [make:bootstrap](#make-bootstrap) - Bootstrap başlatma sınıfı oluştur
- [make:process](#make-process) - Özel süreç sınıfı oluştur

### Derleme ve Dağıtım
- [build:phar](#build-phar) - Projeyi PHAR arşivi olarak paketle
- [build:bin](#build-bin) - Projeyi bağımsız ikili dosya olarak paketle
- [install](#install) - Webman kurulum betiğini çalıştır

### Yardımcı Komutlar
- [version](#version) - Webman çerçeve sürümünü göster
- [fix-disable-functions](#fix-disable-functions) - php.ini'deki devre dışı fonksiyonları düzelt
- [route:list](#route-list) - Tüm kayıtlı rotaları göster

### Uygulama Eklentisi Yönetimi (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Yeni uygulama eklentisi oluştur
- [app-plugin:install](#app-plugin-install) - Uygulama eklentisini kur
- [app-plugin:uninstall](#app-plugin-uninstall) - Uygulama eklentisini kaldır
- [app-plugin:update](#app-plugin-update) - Uygulama eklentisini güncelle
- [app-plugin:zip](#app-plugin-zip) - Uygulama eklentisini ZIP olarak paketle

### Eklenti Yönetimi (plugin:*)
- [plugin:create](#plugin-create) - Yeni Webman eklentisi oluştur
- [plugin:install](#plugin-install) - Webman eklentisini kur
- [plugin:uninstall](#plugin-uninstall) - Webman eklentisini kaldır
- [plugin:enable](#plugin-enable) - Webman eklentisini etkinleştir
- [plugin:disable](#plugin-disable) - Webman eklentisini devre dışı bırak
- [plugin:export](#plugin-export) - Eklenti kaynak kodunu dışa aktar

### Hizmet Yönetimi
- [start](#start) - Webman çalışan süreçlerini başlat
- [stop](#stop) - Webman çalışan süreçlerini durdur
- [restart](#restart) - Webman çalışan süreçlerini yeniden başlat
- [reload](#reload) - Kesintisiz kod yeniden yükleme
- [status](#status) - Çalışan süreç durumunu görüntüle
- [connections](#connections) - Çalışan süreç bağlantı bilgilerini al

## Kod Üretimi

<a name="make-controller"></a>
### make:controller

Denetleyici sınıfı oluşturur.

**Kullanım:**
```bash
php webman make:controller <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Denetleyici adı (sonek olmadan) |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--plugin` | `-p` | Belirtilen eklenti dizininde denetleyici oluştur |
| `--path` | `-P` | Özel denetleyici yolu |
| `--force` | `-f` | Dosya varsa üzerine yaz |
| `--no-suffix` | | "Controller" soneki ekleme |

**Örnekler:**
```bash
# app/controller içinde UserController oluştur
php webman make:controller User

# Eklentide oluştur
php webman make:controller AdminUser -p admin

# Özel yol
php webman make:controller User -P app/api/controller

# Mevcut dosyanın üzerine yaz
php webman make:controller User -f

# "Controller" soneki olmadan oluştur
php webman make:controller UserHandler --no-suffix
```

**Oluşturulan dosya yapısı:**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**Notlar:**
- Denetleyiciler varsayılan olarak `app/controller/` dizinine yerleştirilir
- Yapılandırmadaki denetleyici soneki otomatik eklenir
- Dosya mevcutsa üzerine yazma onayı istenir (diğer komutlarda da aynı)

<a name="make-model"></a>
### make:model

Veritabanı tablosundan model sınıfı oluşturur. Laravel ORM ve ThinkORM desteklenir.

**Kullanım:**
```bash
php webman make:model [name]
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Hayır | Model sınıf adı, etkileşimli modda atlanabilir |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--plugin` | `-p` | Belirtilen eklenti dizininde model oluştur |
| `--path` | `-P` | Hedef dizin (proje köküne göre) |
| `--table` | `-t` | Tablo adını belirt; tablo adı kurala uymuyorsa önerilir |
| `--orm` | `-o` | ORM seçin: `laravel` veya `thinkorm` |
| `--database` | `-d` | Veritabanı bağlantı adını belirt |
| `--force` | `-f` | Mevcut dosyanın üzerine yaz |

**Yol notları:**
- Varsayılan: `app/model/` (ana uygulama) veya `plugin/<plugin>/app/model/` (eklenti)
- `--path` proje köküne göredir, örn. `plugin/admin/app/model`
- Hem `--plugin` hem `--path` kullanıldığında aynı dizini işaret etmelidir

**Örnekler:**
```bash
# app/model içinde User modeli oluştur
php webman make:model User

# Tablo adı ve ORM belirt
php webman make:model User -t wa_users -o laravel

# Eklentide oluştur
php webman make:model AdminUser -p admin

# Özel yol
php webman make:model User -P plugin/admin/app/model
```

**Etkileşimli mod:** Ad atlandığında etkileşimli akışa girer: tablo seç → model adı gir → yol gir. Desteklenenler: Daha fazla görmek için Enter, boş model için `0`, tabloları filtrelemek için `/anahtar kelime`.

**Oluşturulan dosya yapısı:**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (primary key)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

`@property` açıklamaları tablo yapısından otomatik oluşturulur. MySQL ve PostgreSQL desteklenir.

<a name="make-crud"></a>
### make:crud

Veritabanı tablosundan tek seferde model, denetleyici ve doğrulayıcı oluşturur; tam CRUD yeteneği sağlar.

**Kullanım:**
```bash
php webman make:crud
```

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--table` | `-t` | Tablo adını belirt |
| `--model` | `-m` | Model sınıf adı |
| `--model-path` | `-M` | Model dizini (proje köküne göre) |
| `--controller` | `-c` | Denetleyici sınıf adı |
| `--controller-path` | `-C` | Denetleyici dizini |
| `--validator` | | Doğrulayıcı sınıf adı (`webman/validation` gerekir) |
| `--validator-path` | | Doğrulayıcı dizini (`webman/validation` gerekir) |
| `--plugin` | `-p` | Belirtilen eklenti dizininde dosyalar oluştur |
| `--orm` | `-o` | ORM: `laravel` veya `thinkorm` |
| `--database` | `-d` | Veritabanı bağlantı adı |
| `--force` | `-f` | Mevcut dosyaların üzerine yaz |
| `--no-validator` | | Doğrulayıcı oluşturma |
| `--no-interaction` | `-n` | Etkileşimsiz mod, varsayılanları kullan |

**Çalışma akışı:** `--table` belirtilmezse etkileşimli tablo seçimine girer; model adı varsayılan olarak tablo adından çıkarılır; denetleyici adı varsayılan olarak model adı + denetleyici soneki; doğrulayıcı adı varsayılan olarak soneksiz denetleyici adı + `Validator`. Varsayılan yollar: model `app/model/`, denetleyici `app/controller/`, doğrulayıcı `app/validation/`; eklentiler için: `plugin/<plugin>/app/` altındaki ilgili alt dizinler.

**Örnekler:**
```bash
# Etkileşimli oluşturma (tablo seçiminden sonra adım adım onay)
php webman make:crud

# Tablo adı belirt
php webman make:crud --table=users

# Tablo adı ve eklenti belirt
php webman make:crud --table=users --plugin=admin

# Yolları belirt
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Doğrulayıcı oluşturma
php webman make:crud --table=users --no-validator

# Etkileşimsiz + üzerine yaz
php webman make:crud --table=users --no-interaction --force
```

**Oluşturulan dosya yapısı:**

Model (`app/model/User.php`):
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

Denetleyici (`app/controller/UserController.php`):
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

Doğrulayıcı (`app/validation/UserValidator.php`):
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => 'Birincil anahtar',
        'username' => 'Kullanıcı adı'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Notlar:**
- `webman/validation` kurulu veya etkin değilse doğrulayıcı oluşturma atlanır (`composer require webman/validation` ile kurun)
- Doğrulayıcı `attributes` veritabanı alan yorumlarından otomatik oluşturulur; yorum yoksa `attributes` oluşturulmaz
- Doğrulayıcı hata mesajları çoklu dil destekler; dil `config('translation.locale')` üzerinden seçilir

<a name="make-middleware"></a>
### make:middleware

Ara katman sınıfı oluşturur ve `config/middleware.php` dosyasına otomatik kaydeder (eklentiler için `plugin/<plugin>/config/middleware.php`).

**Kullanım:**
```bash
php webman make:middleware <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Ara katman adı |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--plugin` | `-p` | Belirtilen eklenti dizininde ara katman oluştur |
| `--path` | `-P` | Hedef dizin (proje köküne göre) |
| `--force` | `-f` | Mevcut dosyanın üzerine yaz |

**Örnekler:**
```bash
# app/middleware içinde Auth ara katmanı oluştur
php webman make:middleware Auth

# Eklentide oluştur
php webman make:middleware Auth -p admin

# Özel yol
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Oluşturulan dosya yapısı:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**Notlar:**
- Varsayılan olarak `app/middleware/` dizinine yerleştirilir
- Sınıf adı etkinleştirme için ara katman yapılandırma dosyasına otomatik eklenir

<a name="make-command"></a>
### make:command

Konsol komutu sınıfı oluşturur.

**Kullanım:**
```bash
php webman make:command <command-name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `command-name` | Evet | Komut adı, `group:action` formatında (örn. `user:list`) |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--plugin` | `-p` | Belirtilen eklenti dizininde komut oluştur |
| `--path` | `-P` | Hedef dizin (proje köküne göre) |
| `--force` | `-f` | Mevcut dosyanın üzerine yaz |

**Örnekler:**
```bash
# app/command içinde user:list komutu oluştur
php webman make:command user:list

# Eklentide oluştur
php webman make:command user:list -p admin

# Özel yol
php webman make:command user:list -P plugin/admin/app/command

# Mevcut dosyanın üzerine yaz
php webman make:command user:list -f
```

**Oluşturulan dosya yapısı:**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**Notlar:**
- Varsayılan olarak `app/command/` dizinine yerleştirilir

<a name="make-bootstrap"></a>
### make:bootstrap

Bootstrap başlatma sınıfı oluşturur. Süreç başladığında `start` metodu otomatik çağrılır; genellikle global başlatma için kullanılır.

**Kullanım:**
```bash
php webman make:bootstrap <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Bootstrap sınıf adı |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--plugin` | `-p` | Belirtilen eklenti dizininde oluştur |
| `--path` | `-P` | Hedef dizin (proje köküne göre) |
| `--force` | `-f` | Mevcut dosyanın üzerine yaz |

**Örnekler:**
```bash
# app/bootstrap içinde MyBootstrap oluştur
php webman make:bootstrap MyBootstrap

# Otomatik etkinleştirmeden oluştur
php webman make:bootstrap MyBootstrap no

# Eklentide oluştur
php webman make:bootstrap MyBootstrap -p admin

# Özel yol
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Mevcut dosyanın üzerine yaz
php webman make:bootstrap MyBootstrap -f
```

**Oluşturulan dosya yapısı:**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**Notlar:**
- Varsayılan olarak `app/bootstrap/` dizinine yerleştirilir
- Etkinleştirildiğinde sınıf `config/bootstrap.php` dosyasına eklenir (eklentiler için `plugin/<plugin>/config/bootstrap.php`)

<a name="make-process"></a>
### make:process

Özel süreç sınıfı oluşturur ve otomatik başlatma için `config/process.php` dosyasına yazar.

**Kullanım:**
```bash
php webman make:process <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Süreç sınıf adı (örn. MyTcp, MyWebsocket) |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--plugin` | `-p` | Belirtilen eklenti dizininde oluştur |
| `--path` | `-P` | Hedef dizin (proje köküne göre) |
| `--force` | `-f` | Mevcut dosyanın üzerine yaz |

**Örnekler:**
```bash
# app/process içinde oluştur
php webman make:process MyTcp

# Eklentide oluştur
php webman make:process MyProcess -p admin

# Özel yol
php webman make:process MyProcess -P plugin/admin/app/process

# Mevcut dosyanın üzerine yaz
php webman make:process MyProcess -f
```

**Etkileşimli akış:** Sırayla sorar: port dinlensin mi? → protokol türü (websocket/http/tcp/udp/unixsocket) → dinleme adresi (IP:port veya unix socket yolu) → süreç sayısı. HTTP protokolü ayrıca yerleşik veya özel mod sorar.

**Oluşturulan dosya yapısı:**

Dinleme yapmayan süreç (yalnızca `onWorkerStart`):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

TCP/WebSocket dinleyen süreçler ilgili `onConnect`, `onMessage`, `onClose` geri çağırma şablonlarını oluşturur.

**Notlar:**
- Varsayılan olarak `app/process/` dizinine yerleştirilir; süreç yapılandırması `config/process.php` dosyasına yazılır
- Yapılandırma anahtarı sınıf adının snake_case'idir; zaten varsa başarısız olur
- HTTP yerleşik modu `app\process\Http` süreç dosyasını yeniden kullanır, yeni dosya oluşturmaz
- Desteklenen protokoller: websocket, http, tcp, udp, unixsocket

## Derleme ve Dağıtım

<a name="build-phar"></a>
### build:phar

Projeyi dağıtım ve kurulum için PHAR arşivi olarak paketler.

**Kullanım:**
```bash
php webman build:phar
```

**Başlatma:**

Derleme dizinine gidip çalıştırın

```bash
php webman.phar start
```

**Notlar:**
* Paketlenen proje reload desteklemez; kodu güncellemek için restart kullanın

* Büyük dosya boyutu ve bellek kullanımını önlemek için config/plugin/webman/console/app.php dosyasındaki exclude_pattern ve exclude_files ile gereksiz dosyaları hariç tutun.

* webman.phar çalıştırıldığında aynı konumda loglar ve geçici dosyalar için runtime dizini oluşturulur.

* Projeniz .env dosyası kullanıyorsa, .env dosyasını webman.phar ile aynı dizine koyun.

* webman.phar Windows'ta özel süreçleri desteklemez

* Kullanıcı yüklemelerini phar paketi içinde saklamayın; phar:// ile kullanıcı yüklemeleri üzerinde işlem yapmak tehlikelidir (phar serileştirme güvenlik açığı). Kullanıcı yüklemeleri phar dışında diskte ayrı saklanmalıdır. Aşağıya bakın.

* İşiniz public dizinine dosya yüklemesi gerektiriyorsa, public dizinini webman.phar ile aynı konuma çıkarın ve config/app.php dosyasını yapılandırın:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Gerçek public dizin yolunu almak için public_path($relative_path) yardımcı fonksiyonunu kullanın.


<a name="build-bin"></a>
### build:bin

Projeyi gömülü PHP çalışma zamanı ile bağımsız ikili dosya olarak paketler. Hedef ortamda PHP kurulumu gerekmez.

**Kullanım:**
```bash
php webman build:bin [version]
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `version` | Hayır | PHP sürümü (örn. 8.1, 8.2), varsayılan mevcut PHP sürümü, minimum 8.1 |

**Örnekler:**
```bash
# Mevcut PHP sürümünü kullan
php webman build:bin

# PHP 8.2 belirt
php webman build:bin 8.2
```

**Başlatma:**

Derleme dizinine gidip çalıştırın

```bash
./webman.bin start
```

**Notlar:**
* Şiddetle önerilir: Yerel PHP sürümü derleme sürümüyle eşleşmeli (örn. yerel PHP 8.1 → 8.1 ile derle) uyumluluk sorunlarını önlemek için
* Derleme PHP 8 kaynağını indirir ancak yerel olarak kurmaz; yerel PHP ortamını etkilemez
* webman.bin şu anda yalnızca x86_64 Linux'ta çalışır; macOS'ta desteklenmez
* Paketlenen proje reload desteklemez; kodu güncellemek için restart kullanın
* .env varsayılan olarak paketlenmez (config/plugin/webman/console/app.php dosyasındaki exclude_files ile kontrol edilir); başlatırken .env dosyasını webman.bin ile aynı dizine koyun
* webman.bin dizininde log dosyaları için runtime dizini oluşturulur
* webman.bin harici php.ini dosyasını okumaz; özel php.ini ayarları için config/plugin/webman/console/app.php dosyasındaki custom_ini kullanın
* Gereksiz dosyaları config/plugin/webman/console/app.php üzerinden hariç tutarak paket boyutunun büyümesini önleyin
* İkili derleme Swoole eş zamanlılığını desteklemez
* Kullanıcı yüklemelerini ikili paket içinde saklamayın; phar:// ile işlem yapmak tehlikelidir (phar serileştirme güvenlik açığı). Kullanıcı yüklemeleri paket dışında diskte ayrı saklanmalıdır.
* İşiniz public dizinine dosya yüklemesi gerektiriyorsa, public dizinini webman.bin ile aynı konuma çıkarın ve config/app.php dosyasını aşağıdaki gibi yapılandırın, ardından yeniden derleyin:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Proje başlatması için Webman çerçeve kurulum betiğini çalıştırır (`\Webman\Install::install()` çağrısı).

**Kullanım:**
```bash
php webman install
```

## Yardımcı Komutlar

<a name="version"></a>
### version

workerman/webman-framework sürümünü gösterir.

**Kullanım:**
```bash
php webman version
```

**Notlar:** Sürüm `vendor/composer/installed.php` dosyasından okunur; okunamazsa hata döner.

<a name="fix-disable-functions"></a>
### fix-disable-functions

php.ini dosyasındaki `disable_functions` değerini düzeltir; Webman'ın gerektirdiği fonksiyonları kaldırır.

**Kullanım:**
```bash
php webman fix-disable-functions
```

**Notlar:** `disable_functions` değerinden şu fonksiyonları (ve önek eşleşmelerini) kaldırır: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. php.ini bulunamazsa veya `disable_functions` boşsa atlar. **php.ini dosyasını doğrudan değiştirir**; yedekleme önerilir.

<a name="route-list"></a>
### route:list

Tüm kayıtlı rotaları tablo formatında listeler.

**Kullanım:**
```bash
php webman route:list
```

**Çıktı örneği:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Çıktı sütunları:** URI, Method, Callback, Middleware, Name. Closure geri çağırmaları "Closure" olarak gösterilir.

## Uygulama Eklentisi Yönetimi (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Yeni uygulama eklentisi oluşturur; `plugin/<name>` altında tam dizin yapısı ve temel dosyalar üretir.

**Kullanım:**
```bash
php webman app-plugin:create <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti adı; `[a-zA-Z0-9][a-zA-Z0-9_-]*` ile eşleşmeli, `/` veya `\` içeremez |

**Örnekler:**
```bash
# foo adlı uygulama eklentisi oluştur
php webman app-plugin:create foo

# Tireli eklenti oluştur
php webman app-plugin:create my-app
```

**Oluşturulan dizin yapısı:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, vb.
├── api/Install.php  # Kurulum/kaldırma/güncelleme kancaları
├── public/
└── install.sql
```

**Notlar:**
- Eklenti `plugin/<name>/` altında oluşturulur; dizin zaten varsa başarısız olur

<a name="app-plugin-install"></a>
### app-plugin:install

Uygulama eklentisini kurar; `plugin/<name>/api/Install::install($version)` çalıştırır.

**Kullanım:**
```bash
php webman app-plugin:install <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti adı; `[a-zA-Z0-9][a-zA-Z0-9_-]*` ile eşleşmeli |

**Örnekler:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Uygulama eklentisini kaldırır; `plugin/<name>/api/Install::uninstall($version)` çalıştırır.

**Kullanım:**
```bash
php webman app-plugin:uninstall <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti adı |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--yes` | `-y` | Onayı atla, doğrudan çalıştır |

**Örnekler:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Uygulama eklentisini günceller; sırayla `Install::beforeUpdate($from, $to)` ve `Install::update($from, $to, $context)` çalıştırır.

**Kullanım:**
```bash
php webman app-plugin:update <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti adı |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--from` | `-f` | Başlangıç sürümü, varsayılan mevcut sürüm |
| `--to` | `-t` | Hedef sürüm, varsayılan mevcut sürüm |

**Örnekler:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Uygulama eklentisini ZIP dosyası olarak paketler; çıktı `plugin/<name>.zip` dosyasına gider.

**Kullanım:**
```bash
php webman app-plugin:zip <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti adı |

**Örnekler:**
```bash
php webman app-plugin:zip foo
```

**Notlar:**
- `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__` vb. otomatik hariç tutulur

## Eklenti Yönetimi (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Yeni Webman eklentisi oluşturur (Composer paket formunda); `config/plugin/<name>` yapılandırma dizini ve `vendor/<name>` eklenti kaynak dizini üretir.

**Kullanım:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti paket adı, `vendor/package` formatında (örn. `foo/my-admin`); Composer paket adlandırma kurallarına uymalı |

**Örnekler:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Oluşturulan yapı:**
- `config/plugin/<name>/app.php`: Eklenti yapılandırması (`enable` anahtarı dahil)
- `vendor/<name>/composer.json`: Eklenti paket tanımı
- `vendor/<name>/src/`: Eklenti kaynak dizini
- Proje kökündeki `composer.json` dosyasına PSR-4 eşlemesi otomatik eklenir
- Otomatik yüklemeyi yenilemek için `composer dumpautoload` çalıştırılır

**Notlar:**
- Ad `vendor/package` formatında olmalı: küçük harfler, rakamlar, `-`, `_`, `.` ve bir `/` içermeli
- `config/plugin/<name>` veya `vendor/<name>` zaten varsa başarısız olur
- Hem argüman hem `--name` farklı değerlerle verilirse hata oluşur

<a name="plugin-install"></a>
### plugin:install

Eklenti kurulum betiğini çalıştırır (`Install::install()`); eklenti kaynaklarını proje dizinine kopyalar.

**Kullanım:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti paket adı, `vendor/package` formatında (örn. `foo/my-admin`) |

**Seçenekler:**

| Seçenek | Açıklama |
|--------|-------------|
| `--name` | Eklenti adını seçenek olarak belirt; bunu veya argümanı kullanın |

**Örnekler:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Eklenti kaldırma betiğini çalıştırır (`Install::uninstall()`); eklenti kaynaklarını projeden kaldırır.

**Kullanım:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti paket adı, `vendor/package` formatında |

**Seçenekler:**

| Seçenek | Açıklama |
|--------|-------------|
| `--name` | Eklenti adını seçenek olarak belirt; bunu veya argümanı kullanın |

**Örnekler:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Eklentiyi etkinleştirir; `config/plugin/<name>/app.php` dosyasındaki `enable` değerini `true` yapar.

**Kullanım:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti paket adı, `vendor/package` formatında |

**Seçenekler:**

| Seçenek | Açıklama |
|--------|-------------|
| `--name` | Eklenti adını seçenek olarak belirt; bunu veya argümanı kullanın |

**Örnekler:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Eklentiyi devre dışı bırakır; `config/plugin/<name>/app.php` dosyasındaki `enable` değerini `false` yapar.

**Kullanım:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti paket adı, `vendor/package` formatında |

**Seçenekler:**

| Seçenek | Açıklama |
|--------|-------------|
| `--name` | Eklenti adını seçenek olarak belirt; bunu veya argümanı kullanın |

**Örnekler:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Projedeki eklenti yapılandırmasını ve belirtilen dizinleri `vendor/<name>/src/` dizinine dışa aktarır; paketleme ve yayınlama için `Install.php` dosyası oluşturur.

**Kullanım:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Argümanlar:**

| Argüman | Zorunlu | Açıklama |
|----------|----------|-------------|
| `name` | Evet | Eklenti paket adı, `vendor/package` formatında |

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--name` | | Eklenti adını seçenek olarak belirt; bunu veya argümanı kullanın |
| `--source` | `-s` | Dışa aktarılacak yol (proje köküne göre); birden fazla kez belirtilebilir |

**Örnekler:**
```bash
# Eklentiyi dışa aktar, varsayılan olarak config/plugin/<name> dahil
php webman plugin:export foo/my-admin

# Ek olarak app, config vb. dizinleri dışa aktar
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Notlar:**
- Eklenti adı Composer paket adlandırma kurallarına uymalı (`vendor/package`)
- `config/plugin/<name>` mevcutsa ve `--source` içinde değilse otomatik olarak dışa aktarma listesine eklenir
- Dışa aktarılan `Install.php` dosyası `plugin:install` / `plugin:uninstall` tarafından kullanılmak üzere `pathRelation` içerir
- `plugin:install` ve `plugin:uninstall` eklentinin `vendor/<name>` içinde mevcut olmasını, `Install` sınıfı ve `WEBMAN_PLUGIN` sabiti gerektirir

## Hizmet Yönetimi

<a name="start"></a>
### start

Webman çalışan süreçlerini başlatır. Varsayılan DEBUG modu (ön planda).

**Kullanım:**
```bash
php webman start
```

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--daemon` | `-d` | DAEMON modunda başlat (arka planda) |

<a name="stop"></a>
### stop

Webman çalışan süreçlerini durdurur.

**Kullanım:**
```bash
php webman stop
```

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--graceful` | `-g` | Yumuşak durdurma; çıkmadan önce mevcut isteklerin tamamlanmasını bekle |

<a name="restart"></a>
### restart

Webman çalışan süreçlerini yeniden başlatır.

**Kullanım:**
```bash
php webman restart
```

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--daemon` | `-d` | Yeniden başlatmadan sonra DAEMON modunda çalış |
| `--graceful` | `-g` | Yeniden başlatmadan önce yumuşak durdurma |

<a name="reload"></a>
### reload

Kesintisiz kod yeniden yükleme. Kod güncellemelerinden sonra sıcak yeniden yükleme için.

**Kullanım:**
```bash
php webman reload
```

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--graceful` | `-g` | Yumuşak yeniden yükleme; yeniden yüklemeden önce mevcut isteklerin tamamlanmasını bekle |

<a name="status"></a>
### status

Çalışan süreç durumunu görüntüler.

**Kullanım:**
```bash
php webman status
```

**Seçenekler:**

| Seçenek | Kısayol | Açıklama |
|--------|----------|-------------|
| `--live` | `-d` | Ayrıntıları göster (canlı durum) |

<a name="connections"></a>
### connections

Çalışan süreç bağlantı bilgilerini alır.

**Kullanım:**
```bash
php webman connections
```
