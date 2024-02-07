# লগ
লগ ক্লাসের ব্যবহার ডাটাবেস ক্লাসের ব্যবহার সম্পর্কিত।
```php
use support\Log;
Log::channel('plugin.admin.default')->info('টেস্ট');
```

মূল প্রকল্পের লগ কনফিগারেশন পুনরায় ব্যবহার করতে,
```php
use support\Log;
Log::info('লগ কন্টেন্ট');
// মনে করুন, মূল প্রকল্পে test লগ কনফিগারেশন আছে
Log::channel('test')->info('লগ কন্টেন্ট');
```
