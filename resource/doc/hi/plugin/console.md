# webman/console कमांड लाइन प्लगइन

`webman/console` `symfony/console` पर आधारित है।

> प्लगइन को webman>=1.2.2 webman-framework>=1.2.1 की आवश्यकता है।

## स्थापना

```sh
कॉम्पोजर से webman/console को इनस्टॉल करें
composer require webman/console
```

## समर्थित कमांड
**उपयोग कैसे करें**  
`php webman कमांड` या `php webman कमांड` का उपयोग करें।
उदाहरण के लिए `php webman version` या `php webman version` का उपयोग करें।

### समर्थित कमांड
### version
**webman संस्करण संख्या प्रिंट करें**

### route:list
**वर्तमान रूट कॉन्फ़िगरेशन प्रिंट करें**

### make:controller
**एक कंट्रोलर फ़ाइल बनाएं** 
उदाहरण के लिए `php webman make:controller admin` यह `app/controller/AdminController.php` बनाएगा।
उदाहरण के लिए `php webman make:controller api/user` उस `app/api/controller/UserController.php` को बनाएगा।

### make:model
**एक मॉडल फ़ाइल बनाएं**
उदाहरण के लिए `php webman make:model admin` उस `app/model/Admin.php` बनाएगा।
उदाहरण के लिए `php webman make:model api/user` उस `app/api/model/User.php` बनाएगा।

### make:middleware
**एक मिडलवेयर फ़ाइल बनाएं**
उदाहरण के लिए `php webman make:middleware Auth` यह `app/middleware/Auth.php` बनाएगा।

### make:command
**अपना कस्टम कमांड फ़ाइल बनाएं**
उदाहरण के लिए `php webman make:command db:config` यह `app\command\DbConfigCommand.php` बनाएगा।

### plugin:create
**एक बेसिक प्लगइन बनाएं**
उदाहरण के लिए `php webman plugin:create --name=foo/admin` यह `config/plugin/foo/admin` और `vendor/foo/admin` दोनों निर्मित करेगी।
[बेसिक प्लगइन बनाएं](/doc/webman/plugin/create.html) को देखें

### plugin:export
**बेसिक प्लगइन निर्यात करें**
उदाहरण के लिए `php webman plugin:export --name=foo/admin` 
[बेसिक प्लगइन बनाएं](/doc/webman/plugin/create.html) को देखें

### plugin:export
**अनुप्रयोग प्लगइन निर्यात करें**
उदाहरण के लिए `php webman plugin:export shop`
[अनुप्रयोग प्लगइन](/doc/webman/plugin/app.html) को देखें

### phar:pack
**webman परियोजना को phar फ़ाइल में पैक करें**
[phar पैक](/doc/webman/others/phar.html) को देखें
> इस विशेषता के लिए webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 की आवश्यकता होती है।

## कस्टम कमांड
उपयोगकर्ता अपने खुद के कमांड निर्धारित कर सकते हैं, उदाहरण के लिए निम्नलिखित डेटाबेस कॉन्फ़िगरेशन प्रिंट करने के लिए
* `php webman make:command config:mysql` चलाएं
* `app/command/ConfigMySQLCommand.php` फ़ाइल खोलें और निम्नलिखित रूप में संपादित करें

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
    protected static $defaultDescription = 'वर्तमान MySQL सर्वर कॉन्फ़िगरेशन दिखाएं';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('माइएसक्यूएल कॉन्फ़िगरेशन जानकारी निम्नलिखित हैं:');
        $config = config('database');
        $headers = ['नाम', 'डिफ़ॉल्ट', 'ड्राइवर', 'होस्ट', 'पोर्ट', 'डेटाबेस', 'उपयोगकर्ता नाम', 'पासवर्ड', 'यूनिक्स सॉकेट', 'चारसेट', 'कॉलेशन', 'प्रीफिक्स', 'स्ट्रिक्ट', 'इंजन', 'स्कीमा', 'एसएसएलमोड'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'नाम':
                        $row[] = $name;
                        break;
                    case 'डिफ़ॉल्ट':
                        $row[] = $config['default'] == $name ? 'सही' : 'गलत';
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

## परीक्षण

कमांड प्रवेशिका में `php webman config:mysql` चलाएं

परिणाम निम्नलिखित होगा:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| नाम   | डिफ़ॉल्ट | ड्राइवर | होस्ट      | पोर्ट | डेटाबेस | उपयोगकर्ता नाम | पासवर्ड | यूनिक्स सॉकेट | चारसेट | कॉलेशन       | प्रीफिक्स | स्ट्रिक्ट | इंजन | स्कीमा | एसएसएलमोड |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| माइस्कल | सही     | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## अधिक जानकारी
http://www.symfonychina.com/doc/current/components/console.html
