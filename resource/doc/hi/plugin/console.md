# कमांड लाइन

Webman कमांड-लाइन घटक

## स्थापना
```
composer require webman/console
```

## विषय सूची

### कोड जनरेशन
- [make:controller](#make-controller) - नियंत्रक क्लास जनरेट करें
- [make:model](#make-model) - डेटाबेस टेबल से मॉडल क्लास जनरेट करें
- [make:crud](#make-crud) - पूर्ण CRUD जनरेट करें (मॉडल + नियंत्रक + सत्यापनकर्ता)
- [make:middleware](#make-middleware) - मिडलवेयर क्लास जनरेट करें
- [make:command](#make-command) - कंसोल कमांड क्लास जनरेट करें
- [make:bootstrap](#make-bootstrap) - बूटस्ट्रैप इनिशियलाइज़ेशन क्लास जनरेट करें
- [make:process](#make-process) - कस्टम प्रोसेस क्लास जनरेट करें

### बिल्ड और डिप्लॉयमेंट
- [build:phar](#build-phar) - प्रोजेक्ट को PHAR आर्काइव के रूप में पैक करें
- [build:bin](#build-bin) - प्रोजेक्ट को स्टैंडअलोन बाइनरी के रूप में पैक करें
- [install](#install) - Webman इंस्टॉलेशन स्क्रिप्ट चलाएं

### उपयोगिता कमांड
- [version](#version) - Webman फ्रेमवर्क संस्करण प्रदर्शित करें
- [fix-disable-functions](#fix-disable-functions) - php.ini में अक्षम फ़ंक्शन ठीक करें
- [route:list](#route-list) - सभी पंजीकृत रूट प्रदर्शित करें

### ऐप प्लगइन प्रबंधन (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - नया ऐप प्लगइन बनाएं
- [app-plugin:install](#app-plugin-install) - ऐप प्लगइन इंस्टॉल करें
- [app-plugin:uninstall](#app-plugin-uninstall) - ऐप प्लगइन अनइंस्टॉल करें
- [app-plugin:update](#app-plugin-update) - ऐप प्लगइन अपडेट करें
- [app-plugin:zip](#app-plugin-zip) - ऐप प्लगइन को ZIP के रूप में पैक करें

### प्लगइन प्रबंधन (plugin:*)
- [plugin:create](#plugin-create) - नया Webman प्लगइन बनाएं
- [plugin:install](#plugin-install) - Webman प्लगइन इंस्टॉल करें
- [plugin:uninstall](#plugin-uninstall) - Webman प्लगइन अनइंस्टॉल करें
- [plugin:enable](#plugin-enable) - Webman प्लगइन सक्षम करें
- [plugin:disable](#plugin-disable) - Webman प्लगइन अक्षम करें
- [plugin:export](#plugin-export) - प्लगइन सोर्स कोड एक्सपोर्ट करें

### सेवा प्रबंधन
- [start](#start) - Webman वर्कर प्रोसेस शुरू करें
- [stop](#stop) - Webman वर्कर प्रोसेस रोकें
- [restart](#restart) - Webman वर्कर प्रोसेस पुनः आरंभ करें
- [reload](#reload) - डाउनटाइम के बिना कोड रीलोड करें
- [status](#status) - वर्कर प्रोसेस स्थिति देखें
- [connections](#connections) - वर्कर प्रोसेस कनेक्शन जानकारी प्राप्त करें

## कोड जनरेशन

<a name="make-controller"></a>
### make:controller

नियंत्रक क्लास जनरेट करें।

**उपयोग:**
```bash
php webman make:controller <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | नियंत्रक नाम (सफ़िक्स के बिना) |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | निर्दिष्ट प्लगइन निर्देशिका में नियंत्रक जनरेट करें |
| `--path` | `-P` | कस्टम नियंत्रक पथ |
| `--force` | `-f` | फ़ाइल मौजूद हो तो ओवरराइट करें |
| `--no-suffix` | | "Controller" सफ़िक्स जोड़ें नहीं |

**उदाहरण:**
```bash
# app/controller में UserController बनाएं
php webman make:controller User

# प्लगइन में बनाएं
php webman make:controller AdminUser -p admin

# कस्टम पथ
php webman make:controller User -P app/api/controller

# मौजूदा फ़ाइल ओवरराइट करें
php webman make:controller User -f

# "Controller" सफ़िक्स के बिना बनाएं
php webman make:controller UserHandler --no-suffix
```

**जनरेट की गई फ़ाइल संरचना:**
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

**नोट:**
- नियंत्रक डिफ़ॉल्ट रूप से `app/controller/` में रखे जाते हैं
- कॉन्फ़िग से नियंत्रक सफ़िक्स स्वचालित रूप से जोड़ा जाता है
- फ़ाइल मौजूद होने पर ओवरराइट पुष्टि के लिए प्रॉम्प्ट करता है (अन्य कमांड के समान)

<a name="make-model"></a>
### make:model

डेटाबेस टेबल से मॉडल क्लास जनरेट करें। Laravel ORM और ThinkORM का समर्थन करता है।

**उपयोग:**
```bash
php webman make:model [name]
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | No | मॉडल क्लास नाम, इंटरैक्टिव मोड में छोड़ा जा सकता है |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | निर्दिष्ट प्लगइन निर्देशिका में मॉडल जनरेट करें |
| `--path` | `-P` | लक्ष्य निर्देशिका (प्रोजेक्ट रूट के सापेक्ष) |
| `--table` | `-t` | टेबल नाम निर्दिष्ट करें; टेबल नाम कन्वेंशन का पालन न करे तो अनुशंसित |
| `--orm` | `-o` | ORM चुनें: `laravel` या `thinkorm` |
| `--database` | `-d` | डेटाबेस कनेक्शन नाम निर्दिष्ट करें |
| `--force` | `-f` | मौजूदा फ़ाइल ओवरराइट करें |

**पथ नोट:**
- डिफ़ॉल्ट: `app/model/` (मुख्य ऐप) या `plugin/<plugin>/app/model/` (प्लगइन)
- `--path` प्रोजेक्ट रूट के सापेक्ष है, जैसे `plugin/admin/app/model`
- `--plugin` और `--path` दोनों उपयोग करते समय, दोनों को एक ही निर्देशिका की ओर इंगित करना चाहिए

**उदाहरण:**
```bash
# app/model में User मॉडल बनाएं
php webman make:model User

# टेबल नाम और ORM निर्दिष्ट करें
php webman make:model User -t wa_users -o laravel

# प्लगइन में बनाएं
php webman make:model AdminUser -p admin

# कस्टम पथ
php webman make:model User -P plugin/admin/app/model
```

**इंटरैक्टिव मोड:** नाम छोड़ने पर इंटरैक्टिव फ्लो में प्रवेश करता है: टेबल चुनें → मॉडल नाम दर्ज करें → पथ दर्ज करें। समर्थन: अधिक देखने के लिए एंटर, खाली मॉडल बनाने के लिए `0`, टेबल फ़िल्टर करने के लिए `/keyword`।

**जनरेट की गई फ़ाइल संरचना:**
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

`@property` एनोटेशन टेबल संरचना से स्वचालित रूप से जनरेट होते हैं। MySQL और PostgreSQL का समर्थन करता है।

<a name="make-crud"></a>
### make:crud

डेटाबेस टेबल से एक साथ मॉडल, नियंत्रक और सत्यापनकर्ता जनरेट करें, पूर्ण CRUD क्षमता बनाएं।

**उपयोग:**
```bash
php webman make:crud
```

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--table` | `-t` | टेबल नाम निर्दिष्ट करें |
| `--model` | `-m` | मॉडल क्लास नाम |
| `--model-path` | `-M` | मॉडल निर्देशिका (प्रोजेक्ट रूट के सापेक्ष) |
| `--controller` | `-c` | नियंत्रक क्लास नाम |
| `--controller-path` | `-C` | नियंत्रक निर्देशिका |
| `--validator` | | सत्यापनकर्ता क्लास नाम (`webman/validation` आवश्यक) |
| `--validator-path` | | सत्यापनकर्ता निर्देशिका (`webman/validation` आवश्यक) |
| `--plugin` | `-p` | निर्दिष्ट प्लगइन निर्देशिका में फ़ाइलें जनरेट करें |
| `--orm` | `-o` | ORM: `laravel` या `thinkorm` |
| `--database` | `-d` | डेटाबेस कनेक्शन नाम |
| `--force` | `-f` | मौजूदा फ़ाइलें ओवरराइट करें |
| `--no-validator` | | सत्यापनकर्ता जनरेट न करें |
| `--no-interaction` | `-n` | गैर-इंटरैक्टिव मोड, डिफ़ॉल्ट उपयोग करें |

**निष्पादन फ्लो:** `--table` निर्दिष्ट न होने पर इंटरैक्टिव टेबल चयन में प्रवेश करता है; मॉडल नाम डिफ़ॉल्ट रूप से टेबल नाम से अनुमानित; नियंत्रक नाम डिफ़ॉल्ट रूप से मॉडल नाम + नियंत्रक सफ़िक्स; सत्यापनकर्ता नाम डिफ़ॉल्ट रूप से सफ़िक्स के बिना नियंत्रक नाम + `Validator`। डिफ़ॉल्ट पथ: मॉडल `app/model/`, नियंत्रक `app/controller/`, सत्यापनकर्ता `app/validation/`; प्लगइन के लिए: `plugin/<plugin>/app/` के अंतर्गत संबंधित उपनिर्देशिकाएँ।

**उदाहरण:**
```bash
# इंटरैक्टिव जनरेशन (टेबल चयन के बाद चरणबद्ध पुष्टि)
php webman make:crud

# टेबल नाम निर्दिष्ट करें
php webman make:crud --table=users

# टेबल नाम और प्लगइन निर्दिष्ट करें
php webman make:crud --table=users --plugin=admin

# पथ निर्दिष्ट करें
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# सत्यापनकर्ता जनरेट न करें
php webman make:crud --table=users --no-validator

# गैर-इंटरैक्टिव + ओवरराइट
php webman make:crud --table=users --no-interaction --force
```

**जनरेट की गई फ़ाइल संरचना:**

मॉडल (`app/model/User.php`):
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

नियंत्रक (`app/controller/UserController.php`):
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

सत्यापनकर्ता (`app/validation/UserValidator.php`):
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
        'id' => 'प्राथमिक कुंजी',
        'username' => 'उपयोगकर्ता नाम'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**नोट:**
- `webman/validation` इंस्टॉल या सक्षम न होने पर सत्यापनकर्ता जनरेशन छोड़ दिया जाता है (`composer require webman/validation` से इंस्टॉल करें)
- सत्यापनकर्ता `attributes` डेटाबेस फ़ील्ड कमेंट से स्वचालित रूप से जनरेट होते हैं; कोई कमेंट न होने पर `attributes` नहीं बनते
- सत्यापनकर्ता त्रुटि संदेश i18n का समर्थन करते हैं; भाषा `config('translation.locale')` से चुनी जाती है

<a name="make-middleware"></a>
### make:middleware

मिडलवेयर क्लास जनरेट करें और `config/middleware.php` में स्वचालित रूप से पंजीकृत करें (प्लगइन के लिए `plugin/<plugin>/config/middleware.php`)。

**उपयोग:**
```bash
php webman make:middleware <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | मिडलवेयर नाम |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | निर्दिष्ट प्लगइन निर्देशिका में मिडलवेयर जनरेट करें |
| `--path` | `-P` | लक्ष्य निर्देशिका (प्रोजेक्ट रूट के सापेक्ष) |
| `--force` | `-f` | मौजूदा फ़ाइल ओवरराइट करें |

**उदाहरण:**
```bash
# app/middleware में Auth मिडलवेयर बनाएं
php webman make:middleware Auth

# प्लगइन में बनाएं
php webman make:middleware Auth -p admin

# कस्टम पथ
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**जनरेट की गई फ़ाइल संरचना:**
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

**नोट:**
- डिफ़ॉल्ट रूप से `app/middleware/` में रखा जाता है
- क्लास नाम स्वचालित रूप से मिडलवेयर कॉन्फ़िग फ़ाइल में जोड़ा जाता है सक्रियण के लिए

<a name="make-command"></a>
### make:command

कंसोल कमांड क्लास जनरेट करें।

**उपयोग:**
```bash
php webman make:command <command-name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `command-name` | Yes | कमांड नाम `group:action` प्रारूप में (जैसे `user:list`) |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | निर्दिष्ट प्लगइन निर्देशिका में कमांड जनरेट करें |
| `--path` | `-P` | लक्ष्य निर्देशिका (प्रोजेक्ट रूट के सापेक्ष) |
| `--force` | `-f` | मौजूदा फ़ाइल ओवरराइट करें |

**उदाहरण:**
```bash
# app/command में user:list कमांड बनाएं
php webman make:command user:list

# प्लगइन में बनाएं
php webman make:command user:list -p admin

# कस्टम पथ
php webman make:command user:list -P plugin/admin/app/command

# मौजूदा फ़ाइल ओवरराइट करें
php webman make:command user:list -f
```

**जनरेट की गई फ़ाइल संरचना:**
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

**नोट:**
- डिफ़ॉल्ट रूप से `app/command/` में रखा जाता है

<a name="make-bootstrap"></a>
### make:bootstrap

बूटस्ट्रैप इनिशियलाइज़ेशन क्लास जनरेट करें। प्रोसेस शुरू होने पर `start` मेथड स्वचालित रूप से कॉल होती है, आमतौर पर ग्लोबल इनिशियलाइज़ेशन के लिए।

**उपयोग:**
```bash
php webman make:bootstrap <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | बूटस्ट्रैप क्लास नाम |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | निर्दिष्ट प्लगइन निर्देशिका में जनरेट करें |
| `--path` | `-P` | लक्ष्य निर्देशिका (प्रोजेक्ट रूट के सापेक्ष) |
| `--force` | `-f` | मौजूदा फ़ाइल ओवरराइट करें |

**उदाहरण:**
```bash
# app/bootstrap में MyBootstrap बनाएं
php webman make:bootstrap MyBootstrap

# ऑटो-सक्षम के बिना बनाएं
php webman make:bootstrap MyBootstrap no

# प्लगइन में बनाएं
php webman make:bootstrap MyBootstrap -p admin

# कस्टम पथ
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# मौजूदा फ़ाइल ओवरराइट करें
php webman make:bootstrap MyBootstrap -f
```

**जनरेट की गई फ़ाइल संरचना:**
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

**नोट:**
- डिफ़ॉल्ट रूप से `app/bootstrap/` में रखा जाता है
- सक्षम करने पर, क्लास `config/bootstrap.php` में जोड़ा जाता है (प्लगइन के लिए `plugin/<plugin>/config/bootstrap.php`)

<a name="make-process"></a>
### make:process

कस्टम प्रोसेस क्लास जनरेट करें और ऑटो-स्टार्ट के लिए `config/process.php` में लिखें।

**उपयोग:**
```bash
php webman make:process <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्रोसेस क्लास नाम (जैसे MyTcp, MyWebsocket) |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | निर्दिष्ट प्लगइन निर्देशिका में जनरेट करें |
| `--path` | `-P` | लक्ष्य निर्देशिका (प्रोजेक्ट रूट के सापेक्ष) |
| `--force` | `-f` | मौजूदा फ़ाइल ओवरराइट करें |

**उदाहरण:**
```bash
# app/process में बनाएं
php webman make:process MyTcp

# प्लगइन में बनाएं
php webman make:process MyProcess -p admin

# कस्टम पथ
php webman make:process MyProcess -P plugin/admin/app/process

# मौजूदा फ़ाइल ओवरराइट करें
php webman make:process MyProcess -f
```

**इंटरैक्टिव फ्लो:** क्रम में पूछता है: पोर्ट पर लिसन करें? → प्रोटोकॉल प्रकार (websocket/http/tcp/udp/unixsocket) → लिसन एड्रेस (IP:port या unix socket पथ) → प्रोसेस काउंट। HTTP प्रोटोकॉल बिल्ट-इन या कस्टम मोड भी पूछता है।

**जनरेट की गई फ़ाइल संरचना:**

नॉन-लिसनिंग प्रोसेस (केवल `onWorkerStart`):
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

TCP/WebSocket लिसनिंग प्रोसेस संबंधित `onConnect`, `onMessage`, `onClose` कॉलबैक टेम्पलेट जनरेट करते हैं।

**नोट:**
- डिफ़ॉल्ट रूप से `app/process/` में रखा जाता है; प्रोसेस कॉन्फ़िग `config/process.php` में लिखा जाता है
- कॉन्फ़िग कुंजी क्लास नाम का snake_case है; पहले से मौजूद होने पर विफल होता है
- HTTP बिल्ट-इन मोड `app\process\Http` प्रोसेस फ़ाइल पुनः उपयोग करता है, नई फ़ाइल जनरेट नहीं करता
- समर्थित प्रोटोकॉल: websocket, http, tcp, udp, unixsocket

## बिल्ड और डिप्लॉयमेंट

<a name="build-phar"></a>
### build:phar

वितरण और डिप्लॉयमेंट के लिए प्रोजेक्ट को PHAR आर्काइव के रूप में पैक करें।

**उपयोग:**
```bash
php webman build:phar
```

**शुरू करें:**

बिल्ड निर्देशिका में जाएं और चलाएं

```bash
php webman.phar start
```

**नोट:**
* पैक किया गया प्रोजेक्ट रीलोड का समर्थन नहीं करता; कोड अपडेट के लिए रीस्टार्ट उपयोग करें

* बड़ी फ़ाइल साइज़ और मेमोरी उपयोग से बचने के लिए, config/plugin/webman/console/app.php में exclude_pattern और exclude_files कॉन्फ़िगर करके अनावश्यक फ़ाइलें बाहर करें।

* webman.phar चलाने पर लॉग और अस्थायी फ़ाइलों के लिए उसी स्थान पर runtime निर्देशिका बनती है।

* यदि आपका प्रोजेक्ट .env फ़ाइल उपयोग करता है, .env को webman.phar के समान निर्देशिका में रखें।

* webman.phar Windows पर कस्टम प्रोसेस का समर्थन नहीं करता

* कभी भी उपयोगकर्ता-अपलोड की गई फ़ाइलें phar पैकेज के अंदर संग्रहीत न करें; phar:// के माध्यम से उपयोगकर्ता अपलोड पर ऑपरेट करना खतरनाक है (phar डेसीरियलाइज़ेशन कमजोरी)। उपयोगकर्ता अपलोड phar के बाहर अलग से डिस्क पर संग्रहीत होने चाहिए। नीचे देखें।

* यदि आपके व्यवसाय को public निर्देशिका में फ़ाइलें अपलोड करने की आवश्यकता है, public निर्देशिका को webman.phar के समान स्थान पर निकालें और config/app.php कॉन्फ़िगर करें:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
हेल्पर फ़ंक्शन public_path($relative_path) का उपयोग करके वास्तविक public निर्देशिका पथ प्राप्त करें।


<a name="build-bin"></a>
### build:bin

एम्बेडेड PHP रनटाइम के साथ प्रोजेक्ट को स्टैंडअलोन बाइनरी के रूप में पैक करें। लक्ष्य वातावरण में PHP इंस्टॉलेशन की आवश्यकता नहीं।

**उपयोग:**
```bash
php webman build:bin [version]
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `version` | No | PHP संस्करण (जैसे 8.1, 8.2), डिफ़ॉल्ट वर्तमान PHP संस्करण, न्यूनतम 8.1 |

**उदाहरण:**
```bash
# वर्तमान PHP संस्करण उपयोग करें
php webman build:bin

# PHP 8.2 निर्दिष्ट करें
php webman build:bin 8.2
```

**शुरू करें:**

बिल्ड निर्देशिका में जाएं और चलाएं

```bash
./webman.bin start
```

**नोट:**
* दृढ़ता से अनुशंसित: स्थानीय PHP संस्करण बिल्ड संस्करण से मेल खाना चाहिए (जैसे स्थानीय PHP 8.1 → 8.1 के साथ बिल्ड) अनुकूलता समस्याओं से बचने के लिए
* बिल्ड PHP 8 सोर्स डाउनलोड करता है लेकिन स्थानीय रूप से इंस्टॉल नहीं करता; स्थानीय PHP वातावरण को प्रभावित नहीं करता
* webman.bin वर्तमान में केवल x86_64 Linux पर चलता है; macOS पर समर्थित नहीं
* पैक किया गया प्रोजेक्ट रीलोड का समर्थन नहीं करता; कोड अपडेट के लिए रीस्टार्ट उपयोग करें
* .env डिफ़ॉल्ट रूप से पैक नहीं होता (config/plugin/webman/console/app.php में exclude_files द्वारा नियंत्रित); शुरू करते समय .env को webman.bin के समान निर्देशिका में रखें
* webman.bin निर्देशिका में लॉग फ़ाइलों के लिए runtime निर्देशिका बनती है
* webman.bin बाहरी php.ini नहीं पढ़ता; कस्टम php.ini सेटिंग्स के लिए, config/plugin/webman/console/app.php में custom_ini उपयोग करें
* config/plugin/webman/console/app.php के माध्यम से अनावश्यक फ़ाइलें बाहर करके बड़े पैकेज साइज़ से बचें
* बाइनरी बिल्ड Swoole कोरूटीन का समर्थन नहीं करता
* कभी भी उपयोगकर्ता-अपलोड की गई फ़ाइलें बाइनरी पैकेज के अंदर संग्रहीत न करें; phar:// के माध्यम से ऑपरेट करना खतरनाक है (phar डेसीरियलाइज़ेशन कमजोरी)। उपयोगकर्ता अपलोड पैकेज के बाहर अलग से डिस्क पर संग्रहीत होने चाहिए।
* यदि आपके व्यवसाय को public निर्देशिका में फ़ाइलें अपलोड करने की आवश्यकता है, public निर्देशिका को webman.bin के समान स्थान पर निकालें और config/app.php नीचे के अनुसार कॉन्फ़िगर करें, फिर पुनः बिल्ड करें:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Webman फ्रेमवर्क इंस्टॉलेशन स्क्रिप्ट निष्पादित करें (`\Webman\Install::install()` कॉल करता है), प्रोजेक्ट इनिशियलाइज़ेशन के लिए।

**उपयोग:**
```bash
php webman install
```

## उपयोगिता कमांड

<a name="version"></a>
### version

workerman/webman-framework संस्करण प्रदर्शित करें।

**उपयोग:**
```bash
php webman version
```

**नोट:** `vendor/composer/installed.php` से संस्करण पढ़ता है; पढ़ने में असमर्थ होने पर विफलता लौटाता है।

<a name="fix-disable-functions"></a>
### fix-disable-functions

php.ini में `disable_functions` ठीक करें, Webman के लिए आवश्यक फ़ंक्शन हटाएं।

**उपयोग:**
```bash
php webman fix-disable-functions
```

**नोट:** `disable_functions` से निम्नलिखित फ़ंक्शन (और उनके प्रीफ़िक्स मैच) हटाता है: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`। php.ini न मिलने या `disable_functions` खाली होने पर छोड़ देता है। **सीधे php.ini फ़ाइल संशोधित करता है**; बैकअप अनुशंसित।

<a name="route-list"></a>
### route:list

टेबल प्रारूप में सभी पंजीकृत रूट प्रदर्शित करें।

**उपयोग:**
```bash
php webman route:list
```

**आउटपुट उदाहरण:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**आउटपुट कॉलम:** URI, Method, Callback, Middleware, Name। Closure कॉलबैक "Closure" के रूप में प्रदर्शित होते हैं।

## ऐप प्लगइन प्रबंधन (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

नया ऐप प्लगइन बनाएं, `plugin/<name>` के अंतर्गत पूर्ण निर्देशिका संरचना और बेस फ़ाइलें जनरेट करें।

**उपयोग:**
```bash
php webman app-plugin:create <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन नाम; `[a-zA-Z0-9][a-zA-Z0-9_-]*` से मेल खाना चाहिए, `/` या `\` नहीं हो सकता |

**उदाहरण:**
```bash
# foo नाम का ऐप प्लगइन बनाएं
php webman app-plugin:create foo

# हाइफ़न के साथ प्लगइन बनाएं
php webman app-plugin:create my-app
```

**जनरेट की गई निर्देशिका संरचना:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, आदि
├── api/Install.php  # इंस्टॉल/अनइंस्टॉल/अपडेट हुक
├── public/
└── install.sql
```

**नोट:**
- प्लगइन `plugin/<name>/` के अंतर्गत बनाया जाता है; निर्देशिका पहले से मौजूद होने पर विफल होता है

<a name="app-plugin-install"></a>
### app-plugin:install

ऐप प्लगइन इंस्टॉल करें, `plugin/<name>/api/Install::install($version)` निष्पादित करें।

**उपयोग:**
```bash
php webman app-plugin:install <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन नाम; `[a-zA-Z0-9][a-zA-Z0-9_-]*` से मेल खाना चाहिए |

**उदाहरण:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

ऐप प्लगइन अनइंस्टॉल करें, `plugin/<name>/api/Install::uninstall($version)` निष्पादित करें।

**उपयोग:**
```bash
php webman app-plugin:uninstall <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन नाम |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--yes` | `-y` | पुष्टि छोड़ें, सीधे निष्पादित करें |

**उदाहरण:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

ऐप प्लगइन अपडेट करें, क्रम में `Install::beforeUpdate($from, $to)` और `Install::update($from, $to, $context)` निष्पादित करें।

**उपयोग:**
```bash
php webman app-plugin:update <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन नाम |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--from` | `-f` | प्रारंभ संस्करण, डिफ़ॉल्ट वर्तमान संस्करण |
| `--to` | `-t` | लक्ष्य संस्करण, डिफ़ॉल्ट वर्तमान संस्करण |

**उदाहरण:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

ऐप प्लगइन को ZIP फ़ाइल के रूप में पैक करें, आउटपुट `plugin/<name>.zip` पर।

**उपयोग:**
```bash
php webman app-plugin:zip <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन नाम |

**उदाहरण:**
```bash
php webman app-plugin:zip foo
```

**नोट:**
- स्वचालित रूप से `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__`, आदि बाहर करता है

## प्लगइन प्रबंधन (plugin:*)

<a name="plugin-create"></a>
### plugin:create

नया Webman प्लगइन बनाएं (Composer पैकेज रूप), `config/plugin/<name>` कॉन्फ़िग निर्देशिका और `vendor/<name>` प्लगइन सोर्स निर्देशिका जनरेट करें।

**उपयोग:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन पैकेज नाम `vendor/package` प्रारूप में (जैसे `foo/my-admin`); Composer पैकेज नामकरण का पालन करना चाहिए |

**उदाहरण:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**जनरेट संरचना:**
- `config/plugin/<name>/app.php`: प्लगइन कॉन्फ़िग (`enable` स्विच शामिल)
- `vendor/<name>/composer.json`: प्लगइन पैकेज परिभाषा
- `vendor/<name>/src/`: प्लगइन सोर्स निर्देशिका
- प्रोजेक्ट रूट `composer.json` में स्वचालित रूप से PSR-4 मैपिंग जोड़ता है
- ऑटोलोडिंग रिफ्रेश करने के लिए `composer dumpautoload` चलाता है

**नोट:**
- नाम `vendor/package` प्रारूप में होना चाहिए: लोअरकेस अक्षर, संख्या, `-`, `_`, `.`, और एक `/` अवश्य होना चाहिए
- `config/plugin/<name>` या `vendor/<name>` पहले से मौजूद होने पर विफल होता है
- तर्क और `--name` दोनों अलग-अलग मानों के साथ प्रदान करने पर त्रुटि

<a name="plugin-install"></a>
### plugin:install

प्लगइन इंस्टॉलेशन स्क्रिप्ट निष्पादित करें (`Install::install()`), प्लगइन संसाधनों को प्रोजेक्ट निर्देशिका में कॉपी करें।

**उपयोग:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन पैकेज नाम `vendor/package` प्रारूप में (जैसे `foo/my-admin`) |

**विकल्प:**

| Option | Description |
|--------|-------------|
| `--name` | विकल्प के रूप में प्लगइन नाम निर्दिष्ट करें; यह या तर्क उपयोग करें |

**उदाहरण:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

प्लगइन अनइंस्टॉलेशन स्क्रिप्ट निष्पादित करें (`Install::uninstall()`), प्रोजेक्ट से प्लगइन संसाधन हटाएं।

**उपयोग:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन पैकेज नाम `vendor/package` प्रारूप में |

**विकल्प:**

| Option | Description |
|--------|-------------|
| `--name` | विकल्प के रूप में प्लगइन नाम निर्दिष्ट करें; यह या तर्क उपयोग करें |

**उदाहरण:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

प्लगइन सक्षम करें, `config/plugin/<name>/app.php` में `enable` को `true` सेट करें।

**उपयोग:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन पैकेज नाम `vendor/package` प्रारूप में |

**विकल्प:**

| Option | Description |
|--------|-------------|
| `--name` | विकल्प के रूप में प्लगइन नाम निर्दिष्ट करें; यह या तर्क उपयोग करें |

**उदाहरण:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

प्लगइन अक्षम करें, `config/plugin/<name>/app.php` में `enable` को `false` सेट करें।

**उपयोग:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन पैकेज नाम `vendor/package` प्रारूप में |

**विकल्प:**

| Option | Description |
|--------|-------------|
| `--name` | विकल्प के रूप में प्लगइन नाम निर्दिष्ट करें; यह या तर्क उपयोग करें |

**उदाहरण:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

प्रोजेक्ट से प्लगइन कॉन्फ़िग और निर्दिष्ट निर्देशिकाओं को `vendor/<name>/src/` में एक्सपोर्ट करें, और पैकेजिंग और रिलीज़ के लिए `Install.php` जनरेट करें।

**उपयोग:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**तर्क:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | प्लगइन पैकेज नाम `vendor/package` प्रारूप में |

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--name` | | विकल्प के रूप में प्लगइन नाम निर्दिष्ट करें; यह या तर्क उपयोग करें |
| `--source` | `-s` | एक्सपोर्ट करने के लिए पथ (प्रोजेक्ट रूट के सापेक्ष); कई बार निर्दिष्ट किया जा सकता है |

**उदाहरण:**
```bash
# प्लगइन एक्सपोर्ट करें, डिफ़ॉल्ट config/plugin/<name> शामिल
php webman plugin:export foo/my-admin

# अतिरिक्त रूप से app, config आदि एक्सपोर्ट करें
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**नोट:**
- प्लगइन नाम Composer पैकेज नामकरण का पालन करना चाहिए (`vendor/package`)
- यदि `config/plugin/<name>` मौजूद है और `--source` में नहीं है, तो स्वचालित रूप से एक्सपोर्ट सूची में जोड़ा जाता है
- एक्सपोर्ट किया गया `Install.php` `pathRelation` शामिल करता है `plugin:install` / `plugin:uninstall` द्वारा उपयोग के लिए
- `plugin:install` और `plugin:uninstall` के लिए प्लगइन `vendor/<name>` में मौजूद होना चाहिए, `Install` क्लास और `WEBMAN_PLUGIN` कॉन्स्टेंट के साथ

## सेवा प्रबंधन

<a name="start"></a>
### start

Webman वर्कर प्रोसेस शुरू करें। डिफ़ॉल्ट DEBUG मोड (फोरग्राउंड)।

**उपयोग:**
```bash
php webman start
```

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | DAEMON मोड में शुरू करें (बैकग्राउंड) |

<a name="stop"></a>
### stop

Webman वर्कर प्रोसेस रोकें।

**उपयोग:**
```bash
php webman stop
```

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | ग्रेसफुल स्टॉप; बाहर निकलने से पहले वर्तमान अनुरोध पूर्ण होने तक प्रतीक्षा करें |

<a name="restart"></a>
### restart

Webman वर्कर प्रोसेस पुनः आरंभ करें।

**उपयोग:**
```bash
php webman restart
```

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | पुनः आरंभ के बाद DAEMON मोड में चलाएं |
| `--graceful` | `-g` | पुनः आरंभ से पहले ग्रेसफुल स्टॉप |

<a name="reload"></a>
### reload

डाउनटाइम के बिना कोड रीलोड करें। कोड अपडेट के बाद हॉट-रीलोड के लिए।

**उपयोग:**
```bash
php webman reload
```

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | ग्रेसफुल रीलोड; रीलोड से पहले वर्तमान अनुरोध पूर्ण होने तक प्रतीक्षा करें |

<a name="status"></a>
### status

वर्कर प्रोसेस चल रही स्थिति देखें।

**उपयोग:**
```bash
php webman status
```

**विकल्प:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--live` | `-d` | विवरण दिखाएं (लाइव स्थिति) |

<a name="connections"></a>
### connections

वर्कर प्रोसेस कनेक्शन जानकारी प्राप्त करें।

**उपयोग:**
```bash
php webman connections
```
