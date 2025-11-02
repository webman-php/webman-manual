# 微信SDK

## 项目地址

https://github.com/w7corp/easywechat
  
## 安装
 
```php
composer require w7corp/easywechat
```
  
## 使用

**config/wechat.php**

```
<?php
return [
    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id'  => 'your-app-id',         // AppID
    'secret'  => 'your-app-secret',     // AppSecret
    'token'   => 'your-token',          // Token
    'aes_key' => '',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

    /**
     * 是否使用 Stable Access Token
     * 默认 false
     * https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/getStableAccessToken.html
     * true 使用 false 不使用
     */
    'use_stable_access_token' => false,

    /**
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * redirect_url：OAuth授权完成后的回调页地址
     */
    'oauth' => [
        'scopes'   => ['snsapi_userinfo'],
        'redirect_url' => '/examples/oauth_callback.php',
    ],

    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
     */
    'http' => [
        'timeout' => 5.0,
        // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri

        'retry' => true, // 使用默认重试配置
        //  'retry' => [
        //      // 仅以下状态码重试
        //      'status_codes' => [429, 500]
        //       // 最大重试次数
        //      'max_retries' => 3,
        //      // 请求间隔 (毫秒)
        //      'delay' => 1000,
        //      // 如果设置，每次重试的等待时间都会增加这个系数
        //      // (例如. 首次:1000ms; 第二次: 3 * 1000ms; etc.)
        //      'multiplier' => 3
        //  ],
    ],
];
```


**app\controller\OfficialAccount.php**

```php
<?php

namespace app\controller;

use EasyWeChat\OfficialAccount\Application;
use support\Request;
use support\Redis;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

// 授权事件回调地址：http://easywechat.com/OfficialAccount/server

class OfficialAccount
{
    public function server(Request $request)
    {
        $config = config('wechat');
        $app = new Application($config);
        $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
        $symfony_request->headers = new HeaderBag($request->header());
        $app->setRequestFromSymfonyRequest($symfony_request);//必须替换服务端请求
        //$app->setCache(new Psr16Cache(new RedisAdapter(Redis::connection()->client())));//根据需要替换缓存，access_token公众号的全局唯一接口调用凭据会使用该缓存存储
        $server = $app->getServer();
        $response = $server->serve();

        return response($response->getBody()->getContents(), $response->getStatusCode(), $response->getHeaders());
    }
}
```
  
  
## 更多内容

访问 https://easywechat.com/6.x/official-account/examples.html

