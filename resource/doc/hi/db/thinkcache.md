## थिंककैश

### थिंककैश को इंस्टॉल करें  
`कंपोजर रिक्वायर -W webman/think-cache`

इंस्टॉलेशन के बाद पुनः आरंभ (रीलोड असफल है)


> [webman/think-cache](https://www.workerman.net/plugin/15) वास्तव में एक स्वत: इंस्टॉलेशन `toptink/think-cache` की प्लगइन है।

> **ध्यान दें**
> toptink/think-cache php8.1 का समर्थन नहीं करता है

### कॉन्फ़िग फ़ाइल

कॉन्फ़िग फ़ाइल `config/thinkcache.php` है।

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
