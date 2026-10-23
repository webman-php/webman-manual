# बाइनरी पैकेजिंग

webman प्रोजेक्ट को एक बाइनरी फ़ाइल में पैकेज करने का समर्थन करता है, जिससे webman बिना PHP वातावरण के Linux पर चल सकता है।

> **ध्यान दें**
> पैकेज की गई फ़ाइल वर्तमान में केवल x86_64 आर्किटेक्चर वाले Linux पर चलती है। Windows और macOS समर्थित नहीं हैं।
> `php.ini` में phar विकल्प बंद करना ज़रूरी है: `phar.readonly = 0` सेट करें।

## कमांड लाइन टूल इंस्टॉल करें
`composer require webman/console`

## पैकेजिंग
कमांड चलाएं
```
php webman build:bin
```
पैकेज करने के लिए PHP संस्करण निर्दिष्ट कर सकते हैं, उदाहरण के लिए
```
php webman build:bin 8.3
```

पैकेजिंग के बाद build डायरेक्टरी में `webman.bin` फ़ाइल बनेगी।

## शुरू करना
webman.bin को Linux सर्वर पर अपलोड करें और `./webman.bin start` या `./webman.bin start -d` चलाएं।

## सिद्धांत
* पहले स्थानीय webman प्रोजेक्ट को phar फ़ाइल में पैक किया जाता है
* फिर php8.x.micro.sfx रिमोट से डाउनलोड होती है
* php8.x.micro.sfx और phar फ़ाइल को एक बाइनरी फ़ाइल में जोड़ दिया जाता है

## ध्यान दें
* संगतता से बचने के लिए स्थानीय और पैकेजिंग में वही PHP संस्करण (जैसे दोनों में PHP 8.3) इस्तेमाल करने की सलाह दी जाती है
* पैकेजिंग में PHP 8 का सोर्स कोड डाउनलोड होता है लेकिन स्थानीय रूप से इंस्टॉल नहीं होता, स्थानीय PHP पर असर नहीं पड़ता
* webman.bin अभी केवल x86_64 Linux पर चलता है, macOS समर्थित नहीं है
* पैकेज किए गए प्रोजेक्ट में reload सपोर्ट नहीं है; कोड अपडेट के लिए रीस्टार्ट ज़रूरी है
* डिफ़ॉल्ट रूप से env फ़ाइल पैकेज नहीं होती (`config/plugin/webman/console/app.php` में exclude_files द्वारा नियंत्रित)। शुरू करते समय env फ़ाइल webman.bin के साथ ही डायरेक्टरी में होनी चाहिए
* चलने के दौरान webman.bin वाली डायरेक्टरी में runtime डायरेक्टरी बनेगी, जिसमें लॉग फ़ाइलें रखी जाएंगी
* अभी webman.bin बाहरी php.ini नहीं पढ़ता। php.ini कस्टमाइज़ करने के लिए `config/plugin/webman/console/app.php` में custom_ini में सेट करें
* कुछ फ़ाइलें पैकेज करने की ज़रूरत नहीं; `config/plugin/webman/console/app.php` में बाहर रखने की सेटिंग से पैकेज साइज़ कम हो सकता है
* बाइनरी पैकेजिंग में Swoole कोरूटीन सपोर्ट नहीं है
* यूज़र द्वारा अपलोड की गई फ़ाइलें पैकेज के अंदर कभी न रखें। `phar://` प्रोटोकॉल से ऐसी फ़ाइलों पर काम करना जोखिम भरा है (phar deserialization कमजोरी)। अपलोड फ़ाइलें पैकेज के बाहर डिस्क पर अलग रखनी चाहिए
* अगर ऐप को public डायरेक्टरी में अपलोड करना है, तो public डायरेक्टरी को webman.bin के पास रखें, `config/app.php` में ऐसे सेट करें और फिर से पैकेज करें:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## स्टैटिक PHP अलग से डाउनलोड करें
कभी-कभी सिर्फ PHP एक्ज़ीक्यूटेबल चाहिए होता है, पूरा PHP वातावरण डिप्लॉय नहीं करना होता। [स्टैटिक PHP यहाँ डाउनलोड करें](https://www.workerman.net/download)

> **सुझाव**
> स्टैटिक PHP के लिए php.ini निर्दिष्ट करने के लिए: `php -c /your/path/php.ini start.php start -d`

## समर्थित एक्सटेंशन
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## प्रोजेक्ट स्रोत

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
