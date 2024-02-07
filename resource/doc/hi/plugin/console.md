# webman/console कमांड लाइन प्लगइन

`webman/console` `symfony/console` पर आधारित है

> प्लगइन के लिए webman>=1.2.2 webman-framework>=1.2.1 आवश्यक है।

## स्थापना
 
```sh
composer require webman/console
```

## समर्थित कमांड
**उपयोग**  
`php webman कमांड` या `php webman कमांड`।
उदाहरण के लिए `php webman version` या `php webman version`

## समर्थित कमांड
### version
**webman संस्करण संख्या प्रिंट करें**

### route:list
**वर्तमान रूट कॉन्फ़िगरेशन प्रिंट करें**

### make:controller
**एक कंट्रोलर फ़ाइल बनाएं** 
उदाहरण के लिए `php webman make:controller admin` एक `app/controller/AdminController.php` बनाएगा।
उदाहरण के लिए `php webman make:controller api/user` एक `app/api/controller/UserController.php` बनाएगा।

### make:model
**एक मॉडल फ़ाइल बनाएं**
उदाहरण के लिए `php webman make:model admin` एक `app/model/Admin.php` बनाएगा।
उदाहरण के लिए `php webman make:model api/user` एक `app/api/model/User.php` बनाएगा।

### make:middleware
**एक मध्यवर्ती फ़ाइल बनाएं**
उदाहरण के लिए `php webman make:middleware Auth` एक `app/middleware/Auth.php` बनाएगा।

### make:command
**कस्टम कमांड फ़ाइल बनाएं**
उदाहरण के लिए `php webman make:command db:config` एक `app\command\DbConfigCommand.php` बनाएगा।

### plugin:create
**एक बेस प्लगइन बनाएं**
उदाहरण के लिए `php webman plugin:create --name=foo/admin` `config/plugin/foo/admin` और `vendor/foo/admin` दोनों निर्देशिकाएँ बनाएगा।
[बेस प्लगइन बनाएं](/doc/webman/plugin/create.html) देखें।

### plugin:export
**बेस प्लगइन निर्यात करें**
उदाहरण के लिए `php webman plugin:export --name=foo/admin` 
[बेस प्लगइन बनाएं](/doc/webman/plugin/create.html) देखें।

### plugin:export
**एप्लिकेशन प्लगइन निर्यात करें**
उदाहरण के लिए `php webman plugin:export shop`
[एप्लिकेशन प्लगइन](/doc/webman/plugin/app.html) देखें।

### phar:pack
**webman परियोजना को phar फ़ाइल में पैक करें**
[phar पैक](/doc/webman/others/phar.html) देखें।
> यह सुविधा webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 की आवश्यकता है।

## कस्टम कमांड
उपयोगकर्ता अपने खुद के कमांड परिभाषित कर सकते हैं, उदाहरण के लिए निम्नलिखित डेटाबेस कॉन्फ़िगरेशन प्रिंट करने के लिए निम्नलिखित हैं।

* `php webman make:command config:mysql` को चलाएँ
* `app/command/ConfigMySQLCommand.php` फ़ाइल खोलें और नीचे दिए गए ढंग से संशोधित करें।

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
    protected static $defaultDescription = 'वर्तमान MySQL सर्वर कॉन्फ़िगरेशन दिखाएं';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL कॉन्फ़िगरेशन जानकारी निम्नलिखित है:');
        $config = config('database');
        $headers = ['name', 'default', 'driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefix', 'strict', 'engine', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'सत्य' : 'असत्य';
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

कमांड लाइन पर `php webman config:mysql` चलाएँ

परिणाम निम्नलिखित है:

```plaintext
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | सत्य    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## अधिक जानकारी के लिए
http://www.symfonychina.com/doc/current/components/console.html
