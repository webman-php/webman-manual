# வெப்மேன் / கணக்கு மேற்கோ தொழில்நுட்பம்

`வெப்மேன் / கணக்கு மேற்கோ`  `சிம்போனி / கணக்கு மேற்கோ இலக்கண அடிப்படை`

> இனர் வெப்மேன் > = 1.2.2 வெப்மேன்-ஃப்ரேம்வர்க் > = 1.2.1 தேவைப்படுகின்றது

## நிறுவப்

```sh
composer require webman/console
```

## ஆதரிக்கப்படும் கட்டளைகள்
**பயன்பாடு முறை**  
`php webman கட்டளை` அல்லது `php webman கட்டளை`  
உதாரணம் `php webman version` அல்லது `php webman version`

## ஆதரிக்கப்படும் கட்டளைகள்
### பதிப்பு
**வெப் மேன் பதிப்பு எண்களை அச்சிடும்**

### ரூட்:லிஸ்ட்
**தற்போக்கு ரூட் கட்டமைப்பை அச்சிடும்**

### உருவாய்:கட்டுப்பாளர்
**ஒரு நியாய கட்டுப்பாளர் கோப்பு உருவாக்கும்** 
உதாரணம் `php webman make:controller admin` ஒரு `app/controller/AdminController.php` ஐ உருவாக்கும்
உதாரணம் `php webman make:controller api/user` ஒரு `app/api/controller/UserController.php` ஐ உருவாக்கும்

### உருவாய்:தயாரிப்பு
**ஒரு மாதிரி கோப்பு உருவாக்கும்** 
உதாரணம் `php webman make:model admin` ஒரு `app/model/Admin.php` ஐ உருவாக்கும்
உதாரணம் `php webman make:model api/user` ஒரு `app/api/model/User.php` ஐ உருவாக்கும்

### உருவாய்:இடைமறை
**ஒரு இடைமறை கோப்பு உருவாக்கும்** 
உதாரணம் `php webman make:middleware Auth` ஒரு `app/middleware/Auth.php` ஐ உருவாக்கும்

### உருவாய்:கட்டுப்பாளிகள்
**ஒரு தானியங்கி கட்டுப்பாளி கோப்பு உருவாக்கும்** 
உதாரணம் `php webman make:command db:config` ஒரு `app\command\DbConfigCommand.php` ஐ உருவாக்கும்

### மொழியை உருவாக்கு
**தலைப்பு சேர்க்க** 
உதாரணம் `php webman plugin:create --name=foo/admin` ஒரு `config/plugin/foo/admin` மற்றும் `vendor/foo/admin` இரண்டு அடைவுகளை உருவாக்கும்
மேலெழுத்து [கட்டமைக்க உள்ளது பாஸ்மப்](/doc/webman/plugin/create.html)

### மொழி:ஏற்று
**மூடக் மொழியை ஏற்றும்** 
உதாரணம் `php webman plugin:export --name=foo/admin` 
மேலெழுத்து [கட்டமைக்க உள்ளது பாஸ்மப்](/doc/webman/plugin/create.html)

### மொழி:ஏற்று
**பயன்பாடு மொழியை ஏற்றும்** 
உதாரணம் `php webman plugin:export shop` 
மேலெழுத்து [பயன்பாடு மொழி](/doc/webman/plugin/app.html)

### phar:பேக்
**வெப் மேன் திட்டத்தை phar கோப்புகளாக்குகிறது** 
மேலெழுத்து [phar கூடைகள்](/doc/webman/others/phar.html)
> இந்த அம்சம் வெப்மேன் > = 1.2.4 வெப்மேன்-ஃரேம்வர்க் > = 1.2.4 வெப்மேன்\கணக்குல் > = 1.0.5 தேவைப்படுகிறது

## தனியார் கட்டளை
பயனர்கள் தங்கள் கட்டளைகளை நிர்வகிக்கலாம், உதாரணத்திற்கு பிரிவுகளை அச்சிடுவது

* `php webman make:command config:mysql` ஐ நடவடிக்கை செய்யுங்கள்
* `app/command/ConfigMySQLCommand.php` உமின் குறிப்பிடத்தக்கவர் உருமானதாக மாற்றவும்

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
    protected static $defaultDescription = 'தற்போக்கு MySQL சேவையகத்தின் உள்ளிடம் வரைபடங்களை காட்டுகிறது';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL உள்ளிடம் பொருள்கள் போல: ');
        $config = config('database');
        $headers = ['பெயர்', 'இயல்புகள்', 'இயல்புபிரமி', 'புரல்', 'வளைவு', 'தரவுதரகம்', 'பயனர்பெயர்', 'கடவுச்சொல்', 'யுனிக்ஸ் சாக்கெட்', 'சராசரிடு', 'ஒத்திசைப்பு', 'முன் பொது', 'அமையம்', 'வணிகம்', 'தளம்', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'பெயர்':
                        $row[] = $name;
                        break;
                    case 'இயல்புகள்':
                        $row[] = $config['default'] == $name ? 'உண்மை' : 'பொய்';
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

## சோதனை

கட்டளை பருவத்தில் `php webman config:mysql` ஐ இயக்கவும்

முடிவு பெற்றிருக்கும்:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| பெயர்  | இயல்புகள் | இயல்புபிரமி | புரல் | வளைவு | தரவுதரகம் | பயனர்பெயர் | கடவுச்சொல் | யுனிக்ஸ் சாக்கெட் | சராசரிடு | ஒத்திசைப்பு | முன் பொது | அமையம் | வணிகம் | தளம் | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## மேலும் தகவல்களைப் பார்க்க
http://www.symfonychina.com/doc/current/components/console.html
