# स्थापना

ऐप प्लगइन को स्थापित करने के दो तरीके हैं:

## प्लगइन बाजार से स्थापित करें
[आधिकारिक प्रबंधन डैशबोर्ड webman-admin](https://www.workerman.net/plugin/82) में ऐप प्लगइन पेज पर जाकर संबंधित ऐप प्लगइन को स्थापित करने के लिए इंस्टॉल बटन दबाएं।

## स्रोत कोड से स्थापित करें
अनुप्रयोग बाजार से ऐप प्लगइन ज़िप फ़ाइल डाउनलोड करें, उसे अनज़िप करें और उनज़िप डायरेक्टरी को `{मुख्य प्रोजेक्ट}/plugin/` निचे अपलोड करें (यदि प्लगइन निहायत में नहीं होता है तो उसे हाथ से बनाना होगा), इसके बाद `php webman app-plugin:install प्लगइननाम` कमांड चलाकर स्थापित करें।

उदाहरण के लिए डाउनलोड की गई ज़िप फ़ाइल का नाम ai.zip है, इसे `{मुख्य प्रोजेक्ट}/plugin/ai` में अनज़िप करें, फिर `php webman app-plugin:install ai` कमांड चलाकर स्थापित करें।


# अनस्थापित करें

ऐप प्लगइन को अनस्थापित करने के दो तरीके हैं:

## प्लगइन बाजार से अनस्थापित करें
[आधिकारिक प्रबंधन डैशबोर्ड webman-admin](https://www.workerman.net/plugin/82) में ऐप प्लगइन पेज पर जाकर संबंधित ऐप प्लगइन को अनस्थापित करने के लिए अनइंस्टॉल बटन दबाएं।

## स्रोत कोड से अनस्थापित करें
`php webman app-plugin:uninstall प्लगइननाम` कमांड चलाकर अनस्थापित करें, इसके बाद हाथ से `{मुख्य प्रोजेक्ट}/plugin/` नामक डायरेक्टरी में संबंधित प्लगइन डायरेक्टरी को हटा दें।
