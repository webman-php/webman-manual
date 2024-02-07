# phar पैक

phar PHP में एक पैकेज फ़ाइल है, जो JAR के तरह है, आप अपनी webman परियोजना को एक ही phar फ़ाइल में पैक करने के लिए phar का उपयोग कर सकते हैं, ताकि इसे सुरक्षित डिप्लॉय किया जा सके।

**यहां [fuzqing](https://github.com/fuzqing) के PR का बहुत आभार है।**

> **ध्यान दें**
> `php.ini` में phar कॉन्फ़िगरेशन विकल्प को बंद करने की आवश्यकता है, अर्थात `phar.readonly = 0` सेट करें।

## कमांड लाइन उपकरण स्थापित करें
`composer require webman/console`

## सेटिंग कॉन्फ़िगरेशन
`config/plugin/webman/console/app.php` फ़ाइल खोलें, और `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`  सेट करें, जिससे पैक करते समय कुछ अप्रयोजनीय डायरेक्टरी और फ़ाइलों को बाहर किया जा सके, और पैक की साइज़ बड़ी नहीं हो।

## पैक
webman परियोजना रूट डायरेक्टरी में निम्नलिखित कमांड को चलाकर `php webman phar:pack`
`bulid` डायरेक्ट्री में एक `webman.phar` फ़ाइल उत्पन्न होगी।

> पैक संबंधित सेटिंग `config/plugin/webman/console/app.php` में है

## स्टार्ट और स्टॉप संबंधित कमांड
**स्टार्ट**
`php webman.phar start` या `php webman.phar start -d`

**स्टॉप**
`php webman.phar stop`

**स्टेटस देखें**
`php webman.phar status`

**कनेक्शन स्टेटस देखें**
`php webman.phar connections`

**रीस्टार्ट**
`php webman.phar restart` या `php webman.phar restart -d`

## विवरण
* webman.phar चलाने के बाद webman.phar फ़ोल्डर में `runtime` फ़ोल्डर उत्पन्न होगा, जिसमें लॉग आदि अस्थायी फ़ाइलें रखी जाएगी।

* अगर आपके परियोजना में .env फ़ाइल का उपयोग है, तो .env फ़ाइल को webman.phar फ़ोल्डर में रखना चाहिए।

* अगर आपके बिजनेस में public फ़ोल्डर में फ़ाइल अपलोड करने की आवश्यकता है, तो आपको public फ़ोल्डर को अलग से webman.phar फ़ोल्डर में रखना होगा, इसके बाद `config/app.php` को कॉन्फ़िगर करने की आवश्यकता होगी।
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
``` 
बिजनेस `public_path()` हेल्पर फ़ंक्शन का इस्तेमाल करके वास्तविक public फ़ोल्डर की स्थिति पता कर सकता है।

* वेबमैन.phar विंडोज़ पर अपनी पसंदीदा प्रक्रिया को चालू नहीं करता।
