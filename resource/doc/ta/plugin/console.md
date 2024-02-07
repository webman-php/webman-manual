# webman/console கடவுச்சொல் சேர்க்கை

`webman/console` ஒன்று` symfony/console` படி

> சேருதலகள் webman>=1.2.2 webman-framework>=1.2.1 வேண்டும்

## நிறுவக்

```sh
composer require webman/console
```

## ஆதரவு சொல்
**பயன்பாடு**  
`php webman கட்டளை` அல்லது `php webman கட்டளை`. 
எ.கா. `php webman version` அல்லது `php webman version`.

## ஆதரவு சொல்
### version
**வெப்மேன் பதிப்பு எண் அச்சிடுக**

### route:list
**தற்போதைக்கு வழிகாட்டி உள்ளடக்கத்தை அச்சிக்கும்**

### make:controller
**ஒரு கட்டுப்பாட்டாளர் கோப்பு உருவாக்குக** 
எந்தவொரு `php webman make:controller admin` ஒரு` app/controller/AdminController.php` உருவாக்கும்
எந்தவொரு `php webman make:controller api/user` ஒரு` app/api/controller/UserController.php` உருவாக்கும்

### make:model
**ஒரு model கோப்பு உருவாக்குக**
எந்தவொரு `php webman make:model admin` ஒரு` app/model/Admin.php` உருவாக்கும்
எந்தவொரு `php webman make:model api/user` ஒரு` app/api/model/User.php` உருவாக்கும்

### make:middleware
**ஒரு இடைமாற்றி கோப்பு உருவாக்குக**
எந்தவொரு `php webman make:middleware Auth` ஒரு` app/middleware/Auth.php` உருவாக்கும்

### make:command
**பின்னிரண்டியும் கட்டளை கோப்பு உருவாக்குக**
எந்தவொரு `php webman make:command db:config` ஒரு` app\command\DbConfigCommand.php` உருவாக்கும்

### plugin:create
**ஒரு எளிதான சேர்க்கை உருவாக்குக**
எந்தவொரு `php webman plugin:create --name=foo/admin` ஒரு` config/plugin/foo/admin` மற்றும் `vendor/foo/admin` இரண்டு கோப்புகளை உருவாக்கும்
காண்க[எளிதான சேர்க்கை உருவாக்குக](/doc/webman/plugin/create.html)

### plugin:export
**எளிதான சேர்க்கை வெளியீட்டு**
எந்தவொரு `php webman plugin:export --name=foo/admin` 
காண்க[எளிதான சேர்க்கை உருவாக்குக](/doc/webman/plugin/create.html)

### plugin:export
**விண்ணப்ப சேர்க்கை வெளியீடு**
எந்தவொரு `php webman plugin:export shop`
காண்க[விண்ணப்ப சேர்க்கை](/doc/webman/plugin/app.html)

### phar:pack
**வெப்மேன் திட்டத்தை phar கோப்பாக்களியக்க**

[phar கோப்பாக்களியல்](/doc/webman/others/phar.html)
> இந்த அம்சம் webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 வேண்டும்

## தனித்துறை சேர்க்கைகள்
பயனர்கள் தங்கள் செயல்பாடுகளையும் உருவாக்கலாம், எம்படி மேலாண்மற்றில் தரவுத்தள அமைப்படுகின்ற அச்சிடுகளை அச்சிக்கின்றேன்

* `php webman make:command உடன் config:mysql`
* திறக்க `app/command/ConfigMySQLCommand.php` மாற்றவும் கீழ் குறிப்பிட்டதுபோல

```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigMySQLCommand extends Command
{
    protected static $defaultName = 'config:mysql';
    protected static $defaultDescription = 'தற்போதை MySQL சேவையக அமைப்பு காட்சி';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL அமைப்பு தகவல்கள் போன்றவை:');
        $config = config('database');
        $headers = ['பெயர்', 'இயல்பு', 'இயக்கி', 'ஓஸ்ட்', 'வசதி', 'தரப்புரை', 'பயனர்பெயர்', 'கடவுச்சொல்', 'யூனிக்ஸ் சாக்கெட்', 'குறியீடு', 'கோலேஷன்', 'முன்சீர்ச்சி', 'தவர்', 'என்ஜின்', 'திட்ட', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'பெயர்':
                        $row[] = $name;
                        break;
                    case 'இயல்பு':
                        $row[] = $config['default'] == $name ? 'மொத்தம்' : 'பெயர்';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['default'] == $name) {
                array_unshift($rows, $row);
            } else {
                $rows[] = $row;
            }
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}
```

## சேருக்கைக்கு பரீக்ஷணம்

கடவுச்சொல் ரன் ஆக `php webman config:mysql`

முடிவு போன்றுக்கோ:
```plaintext
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| பெயர்  | இயல்பு | இயக்கி | ஹோஸ்ட்      | வசதி | தரப்புரை | பயனர்பெயர் | கடவுச்சொல் | யூனிக்ஸ் சாக்கெட் | குறியீடு | கோலேஷன்       | முன்சீர்ச்சி | தவர்      | என்ஜின் | திட்ட   | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | மொத்தம்   | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## அதிக தகவல்களுக்கு குறைவு
http://www.symfonychina.com/doc/current/components/console.html
