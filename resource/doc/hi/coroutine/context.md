`support\Context`कक्षा का उपयोग अनुरोध संदर्भ डेटा को संग्रहीत करने के लिए किया जाता है, जब अनुरोध पूरा होता है, तो संबंधित संदर्भ डेटा स्वत: हटा दिया जाता है। अर्थात, संदर्भ डेटा का जीवनकाल अनुरोध के जीवनकाल के साथ चलता है। `support\Context`Fiber, Swoole, Swow कोरोना माहौल का समर्थन करता है।

अधिक जानकारी के लिए [webman कोरोना](./fiber.md) देखें।

# इंटरफेस
संदर्भ निम्नलिखित इंटरफेस प्रदान करता है

## संदर्भ डेटा सेट करें
```php
Context::set(string $name, $mixed $value);
```

## संदर्भ डेटा प्राप्त करें
```php
Context::get(string $name = null);
```

## संदर्भ डेटा हटाएं
```php
Context::delete(string $name);
```

> **ध्यान दें**
> फ़्रेमवर्क अनुरोध समाप्त होने के बाद स्वत: Context::destroy() इंटरफ़ेस को कॉल करके संदर्भ डेटा को नष्ट करेगा, व्यावसायिक मनुअल रूप से Context::destroy() को कॉल नहीं कर सकता है।

# उदाहरण
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# ध्यान दें
**कोरोना का उपयोग करते समय** , **अनुरोध संबंधित स्थिति डेटा** को ग्लोबल वेरिएबल या स्थैतिक वेरिएबल में संग्रहीत नहीं किया जा सकता, यह दुनियावी डेटा प्रदूषण का कारण बन सकता है, सही तरीके से उपयोग Context उन्हें संग्रहित और प्राप्त करने का है।
