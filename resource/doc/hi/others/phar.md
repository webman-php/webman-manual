# phar पैकेजिंग

phar PHP में JAR के तरह एक पैकेज फ़ाइल है, आप अपने webman परियोजना को एक एकल phar फ़ाइल में पैकेजिंग करने के लिए phar का उपयोग कर सकते हैं, जो उपयोग में आसान होता है।

**यहाँ fuzqing के PR के लिए बहुत धन्यवाद [fuzqing](https://github.com/fuzqing).**

> **ध्यान दें**
> `php.ini` की phar समाकृति विकल्प को बंद करने की आवश्यकता है, तथा `phar.readonly = 0` को सेट करना है।

## कमांड लाइन उपकरण स्थापित करें
`कम्पोजर इंस्टॉल webman/console`

## सेटिंग कॉन्फ़िगर
`config/plugin/webman/console/app.php` फ़ाइल खोलें और `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` सेट करें, ताकि यूजर पैकेजिंग के दौरान कुछ अप्रयोज्य निर्देशिकाओं और फ़ाइलों को बाहर कर सके, जिससे पैकेज का आकार बहुत बड़ा न हो।

## पैकेजिंग
webman परियोजना रूट निर्देशिका में निम्नलिखित कमांड को चलाएँ `php webman phar:pack`
इससे `build` निर्देशिका में `webman.phar` नाम की एक फ़ाइल उत्पन्न हो जाएगी।

> पैकेजिंग संबंधित सेटिंग `config/plugin/webman/console/app.php` में है।

## स्टार्ट और स्टॉप संबंधित कमांड
**स्टार्ट**
`php webman.phar start` या `php webman.phar start -d`

**स्टॉप**
`php webman.phar stop`

**स्थिति देखें**
`php webman.phar status`

**कनेक्शन स्थिति देखें**
`php webman.phar connections`

**रीस्टार्ट करें**
`php webman.phar restart` या `php webman.phar restart -d`


## स्पष्टीकरण
* webman.phar चलाने के बाद webman.phar के निर्देशिका में रनटाइम निर्देशिका उत्पन्न होगी, जिसमें लॉग आदि समयसार फ़ाइलें संचित की जाती हैं।

* यदि आपके प्रोजेक्ट में .env फ़ाइल का उपयोग होता है, तो आपको .env फ़ाइल को webman.phar निर्देशिका में रखना होगा।

* यदि आपका बिजनेस public निर्देशिका में फ़ाइलों को अपलोड करने की आवश्यकता है, तो आपको public निर्देशिका को webman.phar निर्देशिका में स्थानांतरित करना होगा, इस समय आपको `config/app.php` को कॉन्फ़िगर करने की आवश्यकता होगी।
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
व्यवसाय `public_path()` सहायक फ़ंक्शन का उपयोग करके वास्तविक public निर्देशिका स्थान पा सकते हैं।

* webman.phar कस्टम प्रोसेस को विंडोज़ में समर्थन नहीं करता है।
