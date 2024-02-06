# अपग्रेड करने का तरीका

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **ध्यान दें**
> क्योंकि अलीबाबा का कॉम्पोज़र प्रॉक्सी अब कॉम्पोज़र ऑफिशियल स्रोत से डेटा सिंक्रनाइज़ करना बंद कर दिया है, इसलिए वर्तमान में नवीनतम वेब्मैन को अपग्रेड करने के लिए यहां दिए गए कमांड `composer config -g --unset repos.packagist` का उपयोग करें, जिससे कॉम्पोज़र ऑफिशियल डेटा स्रोत का पुनर्स्थापन हो।
