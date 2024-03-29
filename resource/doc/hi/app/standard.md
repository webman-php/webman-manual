# अनुप्रयोग प्लगइन विकास मानक

## अनुप्रयोग प्लगइन आवश्यकताएँ
* प्लगइन में किसी भी अधिकार उल्लंघनकारी कोड, आइकन, चित्र आदि नहीं होना चाहिए
* प्लगइन स्रोत कोड को पूरा होना चाहिए और इसे एन्क्रिप्ट नहीं किया जा सकता
* प्लगइन को पूर्ण कार्यक्षमता के साथ होना चाहिए, यह साधारण कार्यक्षमता नहीं हो सकती
* पूर्ण कार्यक्षमता के लिए पूरा फ़ंक्शनालिटी, डॉक्यूमेंटेशन प्रदान करनी चाहिए
* प्लगइन में उप-मार्केट शामिल नहीं होना चाहिए
* प्लगइन में किसी भी प्रकार के प्रमोशनल या वेबसाइट लिंक नहीं होने चाहिए

## अनुप्रयोग प्लगइन पहचान
प्रत्येक अनुप्रयोग प्लगइन का एक अद्वितीय पहचान होता है, जो अक्षरों से बना होता है। यह पहचान प्लगइन के स्रोत कोड निर्देशिका नाम, कक्ष का नामस्थान और प्लगइन डेटाबेस तालिका प्रीफ़िक्स पर प्रभाव डालता है।

यदि डेवलपर किसी प्लगइन के रूप में `foo` का उपयोग करता है, तो प्लगइन का स्रोत कोड निर्देशिका `{मुख्य प्रोजेक्ट}/plugin/foo` होता है, संबंधित प्लगइन का नेमस्पेस `plugin\foo` होता है, और तालिका प्रीफ़िक्स `foo_` होता है।

क्योंकि पहचान समूचे इंटरनेट पर अद्वितीय होती है, इसलिए विकासकों को पहले विकास से पहले पहचान की उपलब्धता की जांच करनी चाहिए, जांच करने के लिए यह वेबसाइट प्राप्त है [अनुप्रयोग पहचान जांच](https://www.workerman.net/app/check)।

## डेटाबेस
* तालिका नाम केवल लोअरकेस अक्षर `a-z` और अंडरस्कोर `_` से बना होना चाहिए
* प्लगइन डेटा तालिकाएं प्लगइन पहचान का प्रीफ़िक्स लेनी चाहिए, उदाहरण के लिए, `foo` प्लगइन की article तालिका का नाम `foo_article` होगा
* तालिका प्राइमरी कुंजी के रूप में `id` का उपयोग करना चाहिए
* स्टोर इंजन के रूप में इन्नोडब इंजन का उपयोग करना चाहिए
* वर्ण समूह के रूप में utf8mb4_general_ci का उपयोग करना चाहिए
* डेटाबेस ORM के रूप में लारावेल या फिंक-ORM का उपयोग कर सकते हैं
* समय फ़ील्ड के रूप में DateTime का उपयोग किया जाना चाहिए

## कोड मानक

#### PSR मानक
कोड को PSR4 लोडिंग मानक के अनुसार होना चाहिए

#### वर्ग का नामाकरण बड़ी अक्षर से शुरू की गई Camel case
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### वर्ग के गुण और विधियों को लोअरकेस के साथ Camel case
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * जो ऑथ्येंटिकेशन की आवश्यकता नहीं है
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * टिप्पणियाँ प्राप्त करें
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### टिप्पणी
वर्ग के गुण और फ़ंक्शन के साथ टिप्पणी शामिल होनी चाहिए, सहित सारांश, पैरामीटर, रिटर्न टाइप

#### इंडेंटेशन
कोड को चार स्पेस चरण से इंडेंट किया जाना चाहिए, और टैब का उपयोग नहीं किया जाना चाहिए

#### प्रक्रिया नियंत्रण
प्रक्रिया नियंत्रण की कीवर्ड (जैसे if, for, while, foreach आदि) के बाद एक स्थान छोड़ना चाहिए, प्रक्रिया नियंत्रण कोड की शुरुआत वाले ब्रेसेस को वाक्यांश के अंत में होना चाहिए।
```php
foreach ($users as $uid => $user) {

}
```

#### टेम्पररी वेरिएबल नाम
लोअरकेस शुरू होने वाले Camel case स्टाइल का प्रेरणा किया जाता है (मजबूत नहीं)

```php
$articleCount = 100;
```
