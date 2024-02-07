# পেমেন্ট এসডিকে

### প্রকল্প ঠিকানা

https://github.com/yansongda/pay

### ইনস্টলেশন

```php
composer require yansongda/pay -vvv
```

###ব্যবহার

**আলিপে**

```php
<?php
namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'app_id' => '2016082000295641',
        'notify_url' => 'http://yansongda.cn/notify.php',
        'return_url' => 'http://yansongda.cn/return.php',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuWJKrQ6SWvS6niI+4vEVZiYfjkCfLQfoFI2nCp9ZLDS42QtiL4Ccyx8scgc3nhVwmVRte8f57TFvGhvJD0upT4O5O/lRxmTjechXAorirVdAODpOu0mFfQV9y/T9o9hH... // (টিপ: যাচাইকরণ করুন আপনার দরকারী পাবলিক কী)
        'log' => [ // ঐচ্ছিক
            'file' => './logs/alipay.log',
            'level' => 'info', 
            'type' => 'single',
            'max_file' => 30,
        ],
        'http' => [ // ঐচ্ছিক
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
        ],
        'mode' => 'dev', // ঐচ্ছিক, এই পেরামিটারটি নির্ধারণ করুন, স্যান্ডবক্স মোডে যাওয়া হবে
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - পরীক্ষা',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); 

    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); 

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            
        }

        return $alipay->success()->send();
    }
}
```
