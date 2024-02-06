# ไลบรารี SDK ของ Wechat

## overtrue/wechat

### ที่อยู่โปรเจ็กต์

https://github.com/overtrue/wechat

### การติดตั้ง

```php
composer require overtrue/wechat ^5.0
```

### การใช้งาน

```php
<?php
namespace app\controller;
use support\Request;
use EasyWeChat\Factory;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class WechatController
{
    public function index(Request $request)
    {
        $config = ['app_id' => '8fhau7..', 'secret' => 'mhiw82..', ..];
        $app = Factory::officialAccount($config);
        $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
        $symfony_request->headers = new HeaderBag($request->header());
        $app->rebind('request', $symfony_request);

        $response = $app->server->serve();
        return $response->getContent();
      }
}
```

### รายละเอียดเพิ่มเติม

เข้าชม https://www.easywechat.com/5.x/
