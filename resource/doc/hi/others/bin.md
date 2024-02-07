# बाइनरी पैकेजिंग

वेबमैन अब प्रोजेक्ट को एक बाइनरी फ़ाइल में पैकेज करने का समर्थन करता है, जिससे वेबमैन को लिनक्स सिस्टम पर भागता है और PHP परिवेश की आवश्यकता नहीं होती है।

> **ध्यान दें**
> पैकेज के बाद की फ़ाइल अब तक सिर्फ़ x86_64 की संरचना वाले लिनक्स सिस्टम में ही चल सकती है, मैक सिस्टम का समर्थन नहीं करता है
> `php.ini` की phar कॉन्फ़िगरेशन विकल्प को बंद करना चाहिए, अर्थात `phar.readonly = 0` सेट करें

## कमांड लाइन टूल की स्थापना
`कंम्पोज़र मांगे webman/console ^1.2.24`

## कॉन्फ़िगरेशन सेटिंग
`config/plugin/webman/console/app.php` फ़ाइल खोलें, सेट करें
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
जो पैकेज करते समय कुछ बेकार डायरेक्टरी और फ़ाइलों को छोड़ देता है, बड़े पैकेज साइज़ से बचने के लिए।

## पैकेजिंग
कमांड चलाएं
````
php webman build:bin
````
एक निर्दिष्ट PHP संस्करण से पैकेज करने के लिए भी संभव है, जैसे
````
php webman build:bin 8.1
````

पैकेजिंग के बाद बिल्ड डायरेक्टरी में `webman.bin` फ़ाइल उत्पन्न होगी

## शुरू करना
webman.bin को लिनक्स सर्वर पर अपलोड करें, `./webman.bin start` या `./webman.bin start -d` चलाकर चालू कर सकते हैं।

## तत्व
* पहले लोकल webman प्रोजेक्ट को एक phar फ़ाइल में पैक करें
* फिर दूरस्थ से PHP 8.x.micro.sfx फ़ाइल को डाउनलोड करें
* PHP 8.x.micro.sfx और phar फ़ाइल को एक बाइनरी फ़ाइल में जोड़ें

## ध्यान दें
* स्थानीय PHP संस्करण >= 7.2 पैकेजिंग कमांड को चला सकता है
* लेकिन केवल PHP 8 के बाइनरी फ़ाइलों में पैकेज कर सकता है
* मजबूती से आग्रह है कि स्थानीय PHP संस्करण और पैकेजिंग संस्करण मेल खायें, यानी अगर स्थानीय PHP 8.0 हो, तो पैकेजिंग भी PHP 8.0 ही उपयोग करें, संवाद समस्याएँ रोकें
* पैकेजिंग में PHP 8 का स्रोत कोड डाउनलोड होगा, लेकिन स्थानीय रूप से स्थापित नहीं होगा, स्थानीय PHP परिवेश पर प्रभाव नहीं डालेगा
* वर्तमान में webman.bin केवल x86_64 की संरचना वाले लिनक्स सिस्टम पर ही चल सकता है, मैक सिस्टम पर समर्थन नहीं करता है
* डिफ़ॉल्ट रूप से env फ़ाइल को पैकेजिंग नहीं करता है (`config/plugin/webman/console/app.php` में exclude_files नियंत्रित करता है), इसलिए शुरू करने के समय env फ़ाइल भी वेबमैन बिन के सामान डायरेक्टरी में रखनी चाहिए
* वेबमैन बिन के माध्यम से यातायात के दौरान, runtime डायरेक्टरी उत्पन्न होगी, जिसमें लॉग फ़ाइलें स्टोर होंगी
* वर्तमान में webman.bin बाहरी php.ini फ़ाइल पढ़ने के लिए कार्रवाई नहीं करता, यदि आपको कस्टम php.ini की आवश्यकता है, तो `/config/plugin/webman/console/app.php` फ़ाइल में custom_ini में सेट करें

## विशेष रूप से डाउनलोड स्थिर PHP
कभी-कभी आप बस PHP परिवेश लागू नहीं करना चाहते हैं, केवल एक PHP कार्यकारी फ़ाइल की आवश्यकता होती है, यहां [स्थिर PHP डाउनलोड](https://www.workerman.net/download) के लिए क्लिक करें

> **सुचना**
> स्थिर PHP को निर्दिष्ट php.ini फ़ाइल देने की आवश्यकता हो तो, नीचे दिए गए कमांड का उपयोग करें `php -c /your/path/php.ini start.php start -d`

## समर्थित एक्सटेंशन
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## प्रोजेक्ट की प्रारंभिक जगह
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
