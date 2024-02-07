## थिंककैश

### थिंककैश को स्थापित करें  
`composer require -W webman/think-cache`

स्थापना के बाद रीस्टार्ट की आवश्यकता होती है (रिलोड कार्यक्षम नहीं होता)

> [webman/think-cache](https://www.workerman.net/plugin/15) वास्तव में `toptink/think-cache` को स्वचालित रूप से स्थापित करने वाला एक प्लगइन है।

> **ध्यान दें**
> toptink/think-cache php8.1 का समर्थन नहीं करता है

### कॉन्फ़िग फ़ाइल

कॉन्फ़िग फ़ाइल का नाम `config/thinkcache.php` होता है

### उपयोग

  ```php
  <?php
  namespace app\controller;
    
  use support\Request;
  use think\facade\Cache;
  
  class UserController
  {
      public function db(Request $request)
      {
          $key = 'test_key';
          Cache::set($key, rand());
          return response(Cache::get($key));
      }
  }
  ```

### थिंक-कैश उपयोग दस्तावेज़

[ThinkCache दस्तावेज़ पता](https://github.com/top-think/think-cache)
