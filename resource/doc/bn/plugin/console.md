# কমান্ড লাইন

Webman কমান্ড-লাইন উপাদান

## ইনস্টলেশন
```
composer require webman/console
```

## বিষয়সূচি

### কোড জেনারেশন
- [make:controller](#make-controller) - নিয়ন্ত্রক ক্লাস তৈরি করুন
- [make:model](#make-model) - ডাটাবেস টেবিল থেকে মডেল ক্লাস তৈরি করুন
- [make:crud](#make-crud) - সম্পূর্ণ CRUD তৈরি করুন (মডেল + নিয়ন্ত্রক + যাচাইকারী)
- [make:middleware](#make-middleware) - মিডলওয়্যার ক্লাস তৈরি করুন
- [make:command](#make-command) - কনসোল কমান্ড ক্লাস তৈরি করুন
- [make:bootstrap](#make-bootstrap) - বুটস্ট্র্যাপ ইনিশিয়ালাইজেশন ক্লাস তৈরি করুন
- [make:process](#make-process) - কাস্টম প্রসেস ক্লাস তৈরি করুন

### বিল্ড এবং ডিপ্লয়মেন্ট
- [build:phar](#build-phar) - প্রজেক্টকে PHAR আর্কাইভ হিসেবে প্যাকেজ করুন
- [build:bin](#build-bin) - প্রজেক্টকে স্ট্যান্ডঅ্যালোন বাইনারি হিসেবে প্যাকেজ করুন
- [install](#install) - Webman ইনস্টলেশন স্ক্রিপ্ট চালান

### ইউটিলিটি কমান্ড
- [version](#version) - Webman ফ্রেমওয়ার্ক সংস্করণ প্রদর্শন করুন
- [fix-disable-functions](#fix-disable-functions) - php.ini-তে নিষ্ক্রিয় ফাংশন ঠিক করুন
- [route:list](#route-list) - সমস্ত নিবন্ধিত রুট প্রদর্শন করুন

### অ্যাপ প্লাগইন ব্যবস্থাপনা (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - নতুন অ্যাপ প্লাগইন তৈরি করুন
- [app-plugin:install](#app-plugin-install) - অ্যাপ প্লাগইন ইনস্টল করুন
- [app-plugin:uninstall](#app-plugin-uninstall) - অ্যাপ প্লাগইন আনইনস্টল করুন
- [app-plugin:update](#app-plugin-update) - অ্যাপ প্লাগইন আপডেট করুন
- [app-plugin:zip](#app-plugin-zip) - অ্যাপ প্লাগইনকে ZIP হিসেবে প্যাকেজ করুন

### প্লাগইন ব্যবস্থাপনা (plugin:*)
- [plugin:create](#plugin-create) - নতুন Webman প্লাগইন তৈরি করুন
- [plugin:install](#plugin-install) - Webman প্লাগইন ইনস্টল করুন
- [plugin:uninstall](#plugin-uninstall) - Webman প্লাগইন আনইনস্টল করুন
- [plugin:enable](#plugin-enable) - Webman প্লাগইন সক্রিয় করুন
- [plugin:disable](#plugin-disable) - Webman প্লাগইন নিষ্ক্রিয় করুন
- [plugin:export](#plugin-export) - প্লাগইন সোর্স কোড এক্সপোর্ট করুন

### সেবা ব্যবস্থাপনা
- [start](#start) - Webman ওয়ার্কার প্রসেস চালু করুন
- [stop](#stop) - Webman ওয়ার্কার প্রসেস বন্ধ করুন
- [restart](#restart) - Webman ওয়ার্কার প্রসেস পুনরায় চালু করুন
- [reload](#reload) - ডাউনটাইম ছাড়াই কোড রিলোড করুন
- [status](#status) - ওয়ার্কার প্রসেস স্ট্যাটাস দেখুন
- [connections](#connections) - ওয়ার্কার প্রসেস সংযোগ তথ্য পান

## কোড জেনারেশন

<a name="make-controller"></a>
### make:controller

নিয়ন্ত্রক ক্লাস তৈরি করুন।

**ব্যবহার:**
```bash
php webman make:controller <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | নিয়ন্ত্রক নাম (সাফিক্স ছাড়া) |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | নির্দিষ্ট প্লাগইন ডিরেক্টরিতে নিয়ন্ত্রক তৈরি করুন |
| `--path` | `-P` | কাস্টম নিয়ন্ত্রক পাথ |
| `--force` | `-f` | ফাইল থাকলে ওভাররাইট করুন |
| `--no-suffix` | | "Controller" সাফিক্স যোগ করবেন না |

**উদাহরণ:**
```bash
# Create UserController in app/controller
php webman make:controller User

# Create in plugin
php webman make:controller AdminUser -p admin

# Custom path
php webman make:controller User -P app/api/controller

# Overwrite existing file
php webman make:controller User -f

# Create without "Controller" suffix
php webman make:controller UserHandler --no-suffix
```

**তৈরি ফাইল স্ট্রাকচার:**
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

**নোট:**
- নিয়ন্ত্রকগুলি ডিফল্টভাবে `app/controller/`-এ রাখা হয়
- কনফিগ থেকে নিয়ন্ত্রক সাফিক্স স্বয়ংক্রিয়ভাবে যোগ করা হয়
- ফাইল থাকলে ওভাররাইট নিশ্চিতকরণের জন্য প্রম্পট করা হয় (অন্যান্য কমান্ডের জন্যও একই)

<a name="make-model"></a>
### make:model

ডাটাবেস টেবিল থেকে মডেল ক্লাস তৈরি করুন। Laravel ORM এবং ThinkORM সমর্থন করে।

**ব্যবহার:**
```bash
php webman make:model [name]
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | No | মডেল ক্লাস নাম, ইন্টারঅ্যাক্টিভ মোডে বাদ দেওয়া যেতে পারে |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | নির্দিষ্ট প্লাগইন ডিরেক্টরিতে মডেল তৈরি করুন |
| `--path` | `-P` | টার্গেট ডিরেক্টরি (প্রজেক্ট রুটের সাপেক্ষে) |
| `--table` | `-t` | টেবিল নাম নির্দিষ্ট করুন; কনভেনশন অনুসরণ না করলে সুপারিশ করা হয় |
| `--orm` | `-o` | ORM নির্বাচন করুন: `laravel` বা `thinkorm` |
| `--database` | `-d` | ডাটাবেস সংযোগ নাম নির্দিষ্ট করুন |
| `--force` | `-f` | বিদ্যমান ফাইল ওভাররাইট করুন |

**পাথ নোট:**
- ডিফল্ট: `app/model/` (মেইন অ্যাপ) অথবা `plugin/<plugin>/app/model/` (প্লাগইন)
- `--path` প্রজেক্ট রুটের সাপেক্ষে, যেমন `plugin/admin/app/model`
- `--plugin` এবং `--path` একসাথে ব্যবহার করলে, উভয়কে একই ডিরেক্টরিতে নির্দেশ করতে হবে

**উদাহরণ:**
```bash
# Create User model in app/model
php webman make:model User

# Specify table name and ORM
php webman make:model User -t wa_users -o laravel

# Create in plugin
php webman make:model AdminUser -p admin

# Custom path
php webman make:model User -P plugin/admin/app/model
```

**ইন্টারঅ্যাক্টিভ মোড:** নাম বাদ দিলে ইন্টারঅ্যাক্টিভ ফ্লোতে প্রবেশ করে: টেবিল নির্বাচন করুন → মডেল নাম লিখুন → পাথ লিখুন। সমর্থন: আরও দেখতে এন্টার, খালি মডেল তৈরি করতে `0`, টেবিল ফিল্টার করতে `/keyword`।

**তৈরি ফাইল স্ট্রাকচার:**
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

`@property` অ্যানোটেশন টেবিল স্ট্রাকচার থেকে স্বয়ংক্রিয়ভাবে তৈরি হয়। MySQL এবং PostgreSQL সমর্থন করে।

<a name="make-crud"></a>
### make:crud

ডাটাবেস টেবিল থেকে একবারে মডেল, নিয়ন্ত্রক এবং যাচাইকারী তৈরি করুন, সম্পূর্ণ CRUD ক্ষমতা গঠন করুন।

**ব্যবহার:**
```bash
php webman make:crud
```

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--table` | `-t` | টেবিল নাম নির্দিষ্ট করুন |
| `--model` | `-m` | মডেল ক্লাস নাম |
| `--model-path` | `-M` | মডেল ডিরেক্টরি (প্রজেক্ট রুটের সাপেক্ষে) |
| `--controller` | `-c` | নিয়ন্ত্রক ক্লাস নাম |
| `--controller-path` | `-C` | নিয়ন্ত্রক ডিরেক্টরি |
| `--validator` | | যাচাইকারী ক্লাস নাম (`webman/validation` প্রয়োজন) |
| `--validator-path` | | যাচাইকারী ডিরেক্টরি (`webman/validation` প্রয়োজন) |
| `--plugin` | `-p` | নির্দিষ্ট প্লাগইন ডিরেক্টরিতে ফাইল তৈরি করুন |
| `--orm` | `-o` | ORM: `laravel` বা `thinkorm` |
| `--database` | `-d` | ডাটাবেস সংযোগ নাম |
| `--force` | `-f` | বিদ্যমান ফাইল ওভাররাইট করুন |
| `--no-validator` | | যাচাইকারী তৈরি করবেন না |
| `--no-interaction` | `-n` | নন-ইন্টারঅ্যাক্টিভ মোড, ডিফল্ট ব্যবহার করুন |

**এক্সিকিউশন ফ্লো:** `--table` নির্দিষ্ট না থাকলে ইন্টারঅ্যাক্টিভ টেবিল সিলেকশনে প্রবেশ করে; মডেল নাম ডিফল্টভাবে টেবিল নাম থেকে অনুমান করা হয়; নিয়ন্ত্রক নাম ডিফল্টভাবে মডেল নাম + নিয়ন্ত্রক সাফিক্স; যাচাইকারী নাম ডিফল্টভাবে সাফিক্স ছাড়া নিয়ন্ত্রক নাম + `Validator`। ডিফল্ট পাথ: মডেল `app/model/`, নিয়ন্ত্রক `app/controller/`, যাচাইকারী `app/validation/`; প্লাগইনের জন্য: `plugin/<plugin>/app/`-এর অধীনে সংশ্লিষ্ট সাবডিরেক্টরি।

**উদাহরণ:**
```bash
# Interactive generation (step-by-step confirmation after table selection)
php webman make:crud

# Specify table name
php webman make:crud --table=users

# Specify table name and plugin
php webman make:crud --table=users --plugin=admin

# Specify paths
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Do not generate validator
php webman make:crud --table=users --no-validator

# Non-interactive + overwrite
php webman make:crud --table=users --no-interaction --force
```

**তৈরি ফাইল স্ট্রাকচার:**

মডেল (`app/model/User.php`):
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

নিয়ন্ত্রক (`app/controller/UserController.php`):
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

যাচাইকারী (`app/validation/UserValidator.php`):
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
        'id' => 'প্রাথমিক কী',
        'username' => 'ব্যবহারকারীর নাম'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**নোট:**
- `webman/validation` ইনস্টল বা সক্রিয় না থাকলে যাচাইকারী জেনারেশন বাদ দেওয়া হয় (`composer require webman/validation` দিয়ে ইনস্টল করুন)
- যাচাইকারী `attributes` ডাটাবেস ফিল্ড কমেন্ট থেকে স্বয়ংক্রিয়ভাবে তৈরি হয়; কমেন্ট না থাকলে `attributes` নেই
- যাচাইকারী এরর মেসেজ i18n সমর্থন করে; ভাষা `config('translation.locale')` থেকে নির্বাচিত হয়

<a name="make-middleware"></a>
### make:middleware

মিডলওয়্যার ক্লাস তৈরি করুন এবং `config/middleware.php`-এ স্বয়ংক্রিয়ভাবে নিবন্ধন করুন (প্লাগইনের জন্য `plugin/<plugin>/config/middleware.php`)।

**ব্যবহার:**
```bash
php webman make:middleware <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | মিডলওয়্যার নাম |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | নির্দিষ্ট প্লাগইন ডিরেক্টরিতে মিডলওয়্যার তৈরি করুন |
| `--path` | `-P` | টার্গেট ডিরেক্টরি (প্রজেক্ট রুটের সাপেক্ষে) |
| `--force` | `-f` | বিদ্যমান ফাইল ওভাররাইট করুন |

**উদাহরণ:**
```bash
# Create Auth middleware in app/middleware
php webman make:middleware Auth

# Create in plugin
php webman make:middleware Auth -p admin

# Custom path
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**তৈরি ফাইল স্ট্রাকচার:**
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

**নোট:**
- ডিফল্টভাবে `app/middleware/`-এ রাখা হয়
- ক্লাস নাম স্বয়ংক্রিয়ভাবে মিডলওয়্যার কনফিগ ফাইলে যোগ করা হয় সক্রিয়করণের জন্য

<a name="make-command"></a>
### make:command

কনসোল কমান্ড ক্লাস তৈরি করুন।

**ব্যবহার:**
```bash
php webman make:command <command-name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `command-name` | Yes | কমান্ড নাম `group:action` ফরম্যাটে (যেমন `user:list`) |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | নির্দিষ্ট প্লাগইন ডিরেক্টরিতে কমান্ড তৈরি করুন |
| `--path` | `-P` | টার্গেট ডিরেক্টরি (প্রজেক্ট রুটের সাপেক্ষে) |
| `--force` | `-f` | বিদ্যমান ফাইল ওভাররাইট করুন |

**উদাহরণ:**
```bash
# Create user:list command in app/command
php webman make:command user:list

# Create in plugin
php webman make:command user:list -p admin

# Custom path
php webman make:command user:list -P plugin/admin/app/command

# Overwrite existing file
php webman make:command user:list -f
```

**তৈরি ফাইল স্ট্রাকচার:**
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

**নোট:**
- ডিফল্টভাবে `app/command/`-এ রাখা হয়

<a name="make-bootstrap"></a>
### make:bootstrap

বুটস্ট্র্যাপ ইনিশিয়ালাইজেশন ক্লাস তৈরি করুন। প্রসেস স্টার্ট হলে `start` মেথড স্বয়ংক্রিয়ভাবে কল হয়, সাধারণত গ্লোবাল ইনিশিয়ালাইজেশনের জন্য।

**ব্যবহার:**
```bash
php webman make:bootstrap <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | বুটস্ট্র্যাপ ক্লাস নাম |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | নির্দিষ্ট প্লাগইন ডিরেক্টরিতে তৈরি করুন |
| `--path` | `-P` | টার্গেট ডিরেক্টরি (প্রজেক্ট রুটের সাপেক্ষে) |
| `--force` | `-f` | বিদ্যমান ফাইল ওভাররাইট করুন |

**উদাহরণ:**
```bash
# Create MyBootstrap in app/bootstrap
php webman make:bootstrap MyBootstrap

# Create without auto-enabling
php webman make:bootstrap MyBootstrap no

# Create in plugin
php webman make:bootstrap MyBootstrap -p admin

# Custom path
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Overwrite existing file
php webman make:bootstrap MyBootstrap -f
```

**তৈরি ফাইল স্ট্রাকচার:**
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

**নোট:**
- ডিফল্টভাবে `app/bootstrap/`-এ রাখা হয়
- সক্রিয় করার সময় ক্লাস `config/bootstrap.php`-এ যোগ করা হয় (প্লাগইনের জন্য `plugin/<plugin>/config/bootstrap.php`)

<a name="make-process"></a>
### make:process

কাস্টম প্রসেস ক্লাস তৈরি করুন এবং অটো-স্টার্টের জন্য `config/process.php`-এ লিখুন।

**ব্যবহার:**
```bash
php webman make:process <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্রসেস ক্লাস নাম (যেমন MyTcp, MyWebsocket) |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | নির্দিষ্ট প্লাগইন ডিরেক্টরিতে তৈরি করুন |
| `--path` | `-P` | টার্গেট ডিরেক্টরি (প্রজেক্ট রুটের সাপেক্ষে) |
| `--force` | `-f` | বিদ্যমান ফাইল ওভাররাইট করুন |

**উদাহরণ:**
```bash
# Create in app/process
php webman make:process MyTcp

# Create in plugin
php webman make:process MyProcess -p admin

# Custom path
php webman make:process MyProcess -P plugin/admin/app/process

# Overwrite existing file
php webman make:process MyProcess -f
```

**ইন্টারঅ্যাক্টিভ ফ্লো:** ক্রমান্বয়ে জিজ্ঞাসা করে: পোর্টে লিসেন করবেন? → প্রোটোকল টাইপ (websocket/http/tcp/udp/unixsocket) → লিসেন অ্যাড্রেস (IP:port অথবা unix socket path) → প্রসেস কাউন্ট। HTTP প্রোটোকল বিল্ট-ইন বা কাস্টম মোডও জিজ্ঞাসা করে।

**তৈরি ফাইল স্ট্রাকচার:**

নন-লিসেনিং প্রসেস (শুধুমাত্র `onWorkerStart`):
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

TCP/WebSocket লিসেনিং প্রসেস সংশ্লিষ্ট `onConnect`, `onMessage`, `onClose` কলব্যাক টেমপ্লেট তৈরি করে।

**নোট:**
- ডিফল্টভাবে `app/process/`-এ রাখা হয়; প্রসেস কনফিগ `config/process.php`-এ লিখা হয়
- কনফিগ কী ক্লাস নামের snake_case; ইতিমধ্যে থাকলে ব্যর্থ হয়
- HTTP বিল্ট-ইন মোড `app\process\Http` প্রসেস ফাইল পুনরায় ব্যবহার করে, নতুন ফাইল তৈরি করে না
- সমর্থিত প্রোটোকল: websocket, http, tcp, udp, unixsocket

## বিল্ড এবং ডিপ্লয়মেন্ট

<a name="build-phar"></a>
### build:phar

ডিস্ট্রিবিউশন এবং ডিপ্লয়মেন্টের জন্য প্রজেক্টকে PHAR আর্কাইভ হিসেবে প্যাকেজ করুন।

**ব্যবহার:**
```bash
php webman build:phar
```

**স্টার্ট:**

বিল্ড ডিরেক্টরিতে যান এবং চালান

```bash
php webman.phar start
```

**নোট:**
* প্যাকেজ করা প্রজেক্ট reload সমর্থন করে না; কোড আপডেট করতে restart ব্যবহার করুন

* বড় ফাইল সাইজ এবং মেমরি ব্যবহার এড়াতে, config/plugin/webman/console/app.php-এ exclude_pattern এবং exclude_files কনফিগার করে অপ্রয়োজনীয় ফাইল বাদ দিন।

* webman.phar চালানোর ফলে লগ এবং টেম্পোরারি ফাইলের জন্য একই অবস্থানে runtime ডিরেক্টরি তৈরি হয়।

* আপনার প্রজেক্ট .env ফাইল ব্যবহার করলে, .env webman.phar-এর একই ডিরেক্টরিতে রাখুন।

* webman.phar Windows-এ কাস্টম প্রসেস সমর্থন করে না

* phar প্যাকেজের ভিতরে ইউজার-আপলোড করা ফাইল কখনই স্টোর করবেন না; phar:// দিয়ে ইউজার আপলোডে অপারেট করা বিপজ্জনক (phar ডিসিরিয়ালাইজেশন ভালনারেবিলিটি)। ইউজার আপলোড phar বাইরে আলাদাভাবে ডিস্কে স্টোর করতে হবে। নিচে দেখুন।

* আপনার ব্যবসায় public ডিরেক্টরিতে ফাইল আপলোড করার প্রয়োজন হলে, public ডিরেক্টরি webman.phar-এর একই অবস্থানে এক্সট্রাক্ট করুন এবং config/app.php কনফিগার করুন:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
public_path($relative_path) হেল্পার ফাংশন ব্যবহার করে প্রকৃত public ডিরেক্টরি পাথ পান।


<a name="build-bin"></a>
### build:bin

এম্বেডেড PHP রানটাইম সহ প্রজেক্টকে স্ট্যান্ডঅ্যালোন বাইনারি হিসেবে প্যাকেজ করুন। টার্গেট এনভায়রনমেন্টে PHP ইনস্টলেশনের প্রয়োজন নেই।

**ব্যবহার:**
```bash
php webman build:bin [version]
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `version` | No | PHP সংস্করণ (যেমন 8.1, 8.2), ডিফল্ট বর্তমান PHP সংস্করণ, সর্বনিম্ন 8.1 |

**উদাহরণ:**
```bash
# Use current PHP version
php webman build:bin

# Specify PHP 8.2
php webman build:bin 8.2
```

**স্টার্ট:**

বিল্ড ডিরেক্টরিতে যান এবং চালান

```bash
./webman.bin start
```

**নোট:**
* দৃঢ়ভাবে সুপারিশ: লোকাল PHP সংস্করণ বিল্ড সংস্করণের সাথে মিলতে হবে (যেমন PHP 8.1 লোকাল → 8.1 দিয়ে বিল্ড) সামঞ্জস্য সমস্যা এড়াতে
* বিল্ড PHP 8 সোর্স ডাউনলোড করে কিন্তু লোকালে ইনস্টল করে না; লোকাল PHP এনভায়রনমেন্টকে প্রভাবিত করে না
* webman.bin বর্তমানে শুধুমাত্র x86_64 Linux-এ চলে; macOS-এ সমর্থিত নয়
* প্যাকেজ করা প্রজেক্ট reload সমর্থন করে না; কোড আপডেট করতে restart ব্যবহার করুন
* .env ডিফল্টভাবে প্যাকেজ করা হয় না (config/plugin/webman/console/app.php-এ exclude_files দ্বারা নিয়ন্ত্রিত); স্টার্ট করার সময় .env webman.bin-এর একই ডিরেক্টরিতে রাখুন
* webman.bin ডিরেক্টরিতে লগ ফাইলের জন্য runtime ডিরেক্টরি তৈরি হয়
* webman.bin এক্সটার্নাল php.ini পড়ে না; কাস্টম php.ini সেটিংসের জন্য config/plugin/webman/console/app.php-এ custom_ini ব্যবহার করুন
* config/plugin/webman/console/app.php দিয়ে অপ্রয়োজনীয় ফাইল বাদ দিন বড় প্যাকেজ সাইজ এড়াতে
* বাইনারি বিল্ড Swoole করউটিন সমর্থন করে না
* বাইনারি প্যাকেজের ভিতরে ইউজার-আপলোড করা ফাইল কখনই স্টোর করবেন না; phar:// দিয়ে অপারেট করা বিপজ্জনক (phar ডিসিরিয়ালাইজেশন ভালনারেবিলিটি)। ইউজার আপলোড প্যাকেজ বাইরে আলাদাভাবে ডিস্কে স্টোর করতে হবে।
* আপনার ব্যবসায় public ডিরেক্টরিতে ফাইল আপলোড করার প্রয়োজন হলে, public ডিরেক্টরি webman.bin-এর একই অবস্থানে এক্সট্রাক্ট করুন এবং config/app.php নিচের মতো কনফিগার করুন, তারপর পুনরায় বিল্ড করুন:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Webman ফ্রেমওয়ার্ক ইনস্টলেশন স্ক্রিপ্ট এক্সিকিউট করুন (`\Webman\Install::install()` কল করে), প্রজেক্ট ইনিশিয়ালাইজেশনের জন্য।

**ব্যবহার:**
```bash
php webman install
```

## ইউটিলিটি কমান্ড

<a name="version"></a>
### version

workerman/webman-framework সংস্করণ প্রদর্শন করুন।

**ব্যবহার:**
```bash
php webman version
```

**নোট:** `vendor/composer/installed.php` থেকে সংস্করণ পড়ে; পড়তে না পারলে ব্যর্থতা ফেরত দেয়।

<a name="fix-disable-functions"></a>
### fix-disable-functions

php.ini-তে `disable_functions` ঠিক করুন, Webman-এর জন্য প্রয়োজনীয় ফাংশন সরান।

**ব্যবহার:**
```bash
php webman fix-disable-functions
```

**নোট:** `disable_functions` থেকে নিম্নলিখিত ফাংশন (এবং প্রিফিক্স ম্যাচ) সরায়: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`। php.ini না পাওয়া গেলে বা `disable_functions` খালি থাকলে বাদ দেয়। **সরাসরি php.ini ফাইল পরিবর্তন করে**; ব্যাকআপ সুপারিশ করা হয়।

<a name="route-list"></a>
### route:list

টেবিল ফরম্যাটে সমস্ত নিবন্ধিত রুট তালিকাভুক্ত করুন।

**ব্যবহার:**
```bash
php webman route:list
```

**আউটপুট উদাহরণ:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**আউটপুট কলাম:** URI, Method, Callback, Middleware, Name। Closure কলব্যাক "Closure" হিসেবে প্রদর্শিত হয়।

## অ্যাপ প্লাগইন ব্যবস্থাপনা (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

নতুন অ্যাপ প্লাগইন তৈরি করুন, `plugin/<name>`-এর অধীনে সম্পূর্ণ ডিরেক্টরি স্ট্রাকচার এবং বেস ফাইল জেনারেট করুন।

**ব্যবহার:**
```bash
php webman app-plugin:create <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন নাম; অবশ্যই `[a-zA-Z0-9][a-zA-Z0-9_-]*` মিলতে হবে, `/` বা `\` থাকতে পারবে না |

**উদাহরণ:**
```bash
# Create app plugin named foo
php webman app-plugin:create foo

# Create plugin with hyphen
php webman app-plugin:create my-app
```

**তৈরি ডিরেক্টরি স্ট্রাকচার:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, etc.
├── api/Install.php  # Install/uninstall/update hooks
├── public/
└── install.sql
```

**নোট:**
- প্লাগইন `plugin/<name>/`-এর অধীনে তৈরি হয়; ডিরেক্টরি ইতিমধ্যে থাকলে ব্যর্থ হয়

<a name="app-plugin-install"></a>
### app-plugin:install

অ্যাপ প্লাগইন ইনস্টল করুন, `plugin/<name>/api/Install::install($version)` এক্সিকিউট করুন।

**ব্যবহার:**
```bash
php webman app-plugin:install <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন নাম; অবশ্যই `[a-zA-Z0-9][a-zA-Z0-9_-]*` মিলতে হবে |

**উদাহরণ:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

অ্যাপ প্লাগইন আনইনস্টল করুন, `plugin/<name>/api/Install::uninstall($version)` এক্সিকিউট করুন।

**ব্যবহার:**
```bash
php webman app-plugin:uninstall <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন নাম |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--yes` | `-y` | নিশ্চিতকরণ এড়িয়ে সরাসরি এক্সিকিউট করুন |

**উদাহরণ:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

অ্যাপ প্লাগইন আপডেট করুন, ক্রমান্বয়ে `Install::beforeUpdate($from, $to)` এবং `Install::update($from, $to, $context)` এক্সিকিউট করুন।

**ব্যবহার:**
```bash
php webman app-plugin:update <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin name |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--from` | `-f` | সংস্করণ থেকে, ডিফল্ট বর্তমান সংস্করণ |
| `--to` | `-t` | সংস্করণ পর্যন্ত, ডিফল্ট বর্তমান সংস্করণ |

**উদাহরণ:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

অ্যাপ প্লাগইনকে ZIP ফাইল হিসেবে প্যাকেজ করুন, আউটপুট `plugin/<name>.zip`-এ।

**ব্যবহার:**
```bash
php webman app-plugin:zip <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | Plugin name |

**উদাহরণ:**
```bash
php webman app-plugin:zip foo
```

**নোট:**
- স্বয়ংক্রিয়ভাবে `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__` ইত্যাদি বাদ দেয়

## প্লাগইন ব্যবস্থাপনা (plugin:*)

<a name="plugin-create"></a>
### plugin:create

নতুন Webman প্লাগইন তৈরি করুন (Composer প্যাকেজ ফর্ম), `config/plugin/<name>` কনফিগ ডিরেক্টরি এবং `vendor/<name>` প্লাগইন সোর্স ডিরেক্টরি জেনারেট করুন।

**ব্যবহার:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন প্যাকেজ নাম `vendor/package` ফরম্যাটে (যেমন `foo/my-admin`); Composer প্যাকেজ নেমিং অনুসরণ করতে হবে |

**উদাহরণ:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**তৈরি স্ট্রাকচার:**
- `config/plugin/<name>/app.php`: প্লাগইন কনফিগ (`enable` সুইচ সহ)
- `vendor/<name>/composer.json`: প্লাগইন প্যাকেজ সংজ্ঞা
- `vendor/<name>/src/`: প্লাগইন সোর্স ডিরেক্টরি
- প্রজেক্ট রুট `composer.json`-এ স্বয়ংক্রিয়ভাবে PSR-4 ম্যাপিং যোগ করে
- অটোলোড রিফ্রেশ করতে `composer dumpautoload` চালায়

**নোট:**
- নাম অবশ্যই `vendor/package` ফরম্যাটে হবে: ছোট অক্ষর, সংখ্যা, `-`, `_`, `.`, এবং অবশ্যই একটি `/` থাকতে হবে
- `config/plugin/<name>` বা `vendor/<name>` ইতিমধ্যে থাকলে ব্যর্থ হয়
- আর্গুমেন্ট এবং `--name` উভয় ভিন্ন মান দিয়ে দিলে এরর

<a name="plugin-install"></a>
### plugin:install

প্লাগইন ইনস্টলেশন স্ক্রিপ্ট এক্সিকিউট করুন (`Install::install()`), প্লাগইন রিসোর্স প্রজেক্ট ডিরেক্টরিতে কপি করুন।

**ব্যবহার:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন প্যাকেজ নাম `vendor/package` ফরম্যাটে (যেমন `foo/my-admin`) |

**অপশন:**

| Option | Description |
|--------|-------------|
| `--name` | অপশন হিসেবে প্লাগইন নাম নির্দিষ্ট করুন; এটি বা আর্গুমেন্ট ব্যবহার করুন |

**উদাহরণ:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

প্লাগইন আনইনস্টলেশন স্ক্রিপ্ট এক্সিকিউট করুন (`Install::uninstall()`), প্রজেক্ট থেকে প্লাগইন রিসোর্স সরান।

**ব্যবহার:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন প্যাকেজ নাম `vendor/package` ফরম্যাটে |

**অপশন:**

| Option | Description |
|--------|-------------|
| `--name` | অপশন হিসেবে প্লাগইন নাম নির্দিষ্ট করুন; এটি বা আর্গুমেন্ট ব্যবহার করুন |

**উদাহরণ:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

প্লাগইন সক্রিয় করুন, `config/plugin/<name>/app.php`-এ `enable` `true` সেট করুন।

**ব্যবহার:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন প্যাকেজ নাম `vendor/package` ফরম্যাটে |

**অপশন:**

| Option | Description |
|--------|-------------|
| `--name` | অপশন হিসেবে প্লাগইন নাম নির্দিষ্ট করুন; এটি বা আর্গুমেন্ট ব্যবহার করুন |

**উদাহরণ:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

প্লাগইন নিষ্ক্রিয় করুন, `config/plugin/<name>/app.php`-এ `enable` `false` সেট করুন।

**ব্যবহার:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন প্যাকেজ নাম `vendor/package` ফরম্যাটে |

**অপশন:**

| Option | Description |
|--------|-------------|
| `--name` | অপশন হিসেবে প্লাগইন নাম নির্দিষ্ট করুন; এটি বা আর্গুমেন্ট ব্যবহার করুন |

**উদাহরণ:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

প্রজেক্ট থেকে প্লাগইন কনফিগ এবং নির্দিষ্ট ডিরেক্টরি `vendor/<name>/src/`-এ এক্সপোর্ট করুন, এবং প্যাকেজিং এবং রিলিজের জন্য `Install.php` জেনারেট করুন।

**ব্যবহার:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**আর্গুমেন্ট:**

| Argument | Required | Description |
|----------|----------|-------------|
| `name` | Yes | প্লাগইন প্যাকেজ নাম `vendor/package` ফরম্যাটে |

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--name` | | অপশন হিসেবে প্লাগইন নাম নির্দিষ্ট করুন; এটি বা আর্গুমেন্ট ব্যবহার করুন |
| `--source` | `-s` | এক্সপোর্ট করার পাথ (প্রজেক্ট রুটের সাপেক্ষে); একাধিকবার নির্দিষ্ট করা যেতে পারে |

**উদাহরণ:**
```bash
# Export plugin, default includes config/plugin/<name>
php webman plugin:export foo/my-admin

# Additionally export app, config, etc.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**নোট:**
- প্লাগইন নাম অবশ্যই Composer প্যাকেজ নেমিং অনুসরণ করবে (`vendor/package`)
- `config/plugin/<name>` থাকলে এবং `--source`-এ না থাকলে, স্বয়ংক্রিয়ভাবে এক্সপোর্ট লিস্টে যোগ করা হয়
- এক্সপোর্ট করা `Install.php`-এ `pathRelation` থাকে `plugin:install` / `plugin:uninstall` ব্যবহারের জন্য
- `plugin:install` এবং `plugin:uninstall`-এর জন্য প্লাগইন `vendor/<name>`-এ থাকা, `Install` ক্লাস এবং `WEBMAN_PLUGIN` কনস্ট্যান্ট থাকা প্রয়োজন

## সেবা ব্যবস্থাপনা

<a name="start"></a>
### start

Webman ওয়ার্কার প্রসেস চালু করুন। ডিফল্ট DEBUG মোড (ফোরগ্রাউন্ড)।

**ব্যবহার:**
```bash
php webman start
```

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | DAEMON মোডে চালু করুন (ব্যাকগ্রাউন্ড) |

<a name="stop"></a>
### stop

Webman ওয়ার্কার প্রসেস বন্ধ করুন।

**ব্যবহার:**
```bash
php webman stop
```

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | গ্রেসফুল স্টপ; বের হওয়ার আগে বর্তমান রিকোয়েস্ট সম্পূর্ণ হওয়ার জন্য অপেক্ষা করুন |

<a name="restart"></a>
### restart

Webman ওয়ার্কার প্রসেস পুনরায় চালু করুন।

**ব্যবহার:**
```bash
php webman restart
```

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | রিস্টার্টের পর DAEMON মোডে চালান |
| `--graceful` | `-g` | রিস্টার্টের আগে গ্রেসফুল স্টপ |

<a name="reload"></a>
### reload

ডাউনটাইম ছাড়াই কোড রিলোড করুন। কোড আপডেটের পর হট-রিলোডের জন্য।

**ব্যবহার:**
```bash
php webman reload
```

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | গ্রেসফুল রিলোড; রিলোডের আগে বর্তমান রিকোয়েস্ট সম্পূর্ণ হওয়ার জন্য অপেক্ষা করুন |

<a name="status"></a>
### status

ওয়ার্কার প্রসেস রান স্ট্যাটাস দেখুন।

**ব্যবহার:**
```bash
php webman status
```

**অপশন:**

| Option | Shortcut | Description |
|--------|----------|-------------|
| `--live` | `-d` | বিস্তারিত দেখান (লাইভ স্ট্যাটাস) |

<a name="connections"></a>
### connections

ওয়ার্কার প্রসেস সংযোগ তথ্য পান।

**ব্যবহার:**
```bash
php webman connections
```
