# लॉग
लॉग वर्ग का उपयोग डेटाबेस के उपयोग की तरह होता है
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

यदि मुख्य परियोजना की लॉग विन्यास का पुनर्गुणवात करना हो, तो सीधे उपयोग करें
```php
use support\Log;
Log::info('लॉग सामग्री');
// मान लें मुख्य परियोजना में एक परीक्षण लॉग विन्यास है
Log::channel('test')->info('लॉग सामग्री');
```
