# 결제 SDK (V3)


## 프로젝트 주소

 https://github.com/yansongda/pay

## 설치

```php
composer require yansongda/pay ^3.0.0
```

## 사용

> 설명 : 문서 작성시 알리페이 샌드박스 환경을 기준으로 하며, 문제가 발생할 경우 즉시 피드백 해주세요!

### 설정 파일

다음과 같은 설정 파일 `config/payment.php`이 있다고 가정합시다.

```php
<?php
/**
 * @desc 결제 설정 파일
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // 필수-알리페이에서 할당된 app_id
            'app_id' => '20160909004708941',
            // 필수-애플리케이션 개인 키 문자열 또는 경로
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // 필수-애플리케이션 공개 키 인증서 경로
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // 필수-알리페이 공개 키 인증서 경로
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // 필수-알리페이 루트 인증서 경로
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // 선택-동기화 콜백 주소
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // 선택-비동기 콜백 주소
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // 선택-서비스 공급자 모드에서 서비스 공급자 id, 모드가 Pay::MODE_SERVICE 인 경우에 사용되는 매개변수
            'service_provider_id' => '',
            // 선택-기본값은 정상 모드. 다음 중 하나를 선택할 수 있음 : MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // 필수-상인 번호, 서비스 공급자 모드에서는 서비스 공급자 상인 번호
            'mch_id' => '',
            // 필수-상인 비밀 키
            'mch_secret_key' => '',
            // 필수-상인 개인 키 문자열 또는 경로
            'mch_secret_cert' => '',
            // 필수-상인 공개 키 인증서 경로
            'mch_public_cert_path' => '',
            // 필수
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // 선택-공중의 app_id
            'mp_app_id' => '2016082000291234',
            // 선택-작은 프로그램의 app_id
            'mini_app_id' => '',
            // 선택-앱의 app_id
            'app_id' => '',
            // 선택-결합 app_id
            'combine_app_id' => '',
            // 선택-결합 상인 번호
            'combine_mch_id' => '',
            // 선택-서비스 공급자 모드의 하위 공중의 app_id
            'sub_mp_app_id' => '',
            // 선택-서비스 공급자 모드의 하위 앱의 app_id
            'sub_app_id' => '',
            // 선택-서비스 공급자 모드의 하위 작은 프로그램의 app_id
            'sub_mini_app_id' => '',
            // 선택-서비스 공급자 모드의 하위 상인 id
            'sub_mch_id' => '',
            // 선택-위챗 공개 키 인증서 경로, 선택 사항, php-fpm 모드에서 이 매개변수를 구성하는 것이 좋음
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // 선택-기본값은 정상 모드. 다음 중 하나를 선택할 수 있음 : MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // 라이브 환경의 경우 info를 권장하고, 개발 환경의 경우 debug를 권장합니다
        'type' => 'single', // 선택 사항, 일일로 설정 가능
        'max_file' => 30, // 선택 사항, 유형이 일일 경우에만 유효, 기본값 30일
    ],
    'http' => [ // 선택 사항
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 더 많은 구성 항목은 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) 참조
    ],
    '_force' => true,
];
```
> 주의 : 인증서 디렉토리는 규정되어 있지 않으며, 위 예제는 프레임워크의 `payment` 디렉토리에 저장되어 있는 것입니다.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### 초기화

`config` 메서드를 직접 호출하여 초기화합니다.

```php
// 구성 파일 config/payment.php 가져오기
$config = Config::get('payment');
Pay::config($config);
```
> 주의 : 알리페이 샌드박스 모드인 경우, 해당 옵션을 오픈해야 합니다. `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`이 옵션은 기본 모드로 설정되어 있습니다.

### 결제 (웹)

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @param Request $request
 * @return string
 */
public function payment(Request $request)
{
    // 1. 구성 파일 config/payment.php 가져오기
    $config = Config::get('payment');

    // 2. 구성 초기화
    Pay::config($config);

    // 3. 웹 결제
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // get 방식 사용
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### 콜백

#### 비동기 콜백

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: 『알리페이』비동기 통지
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. 구성 파일 config/payment.php 가져오기
    $config = Config::get('payment');

    // 2. 구성 초기화
    Pay::config($config);

    // 3. 알리페이 콜백 처리
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // trade_status를 자체적으로 판단하고 기타 논리를 판단해 주세요. 거래 통지 상태가 TRADE_SUCCESS 또는 TRADE_FINISHED인 경우에만 알리페이는 구매자 결제를 성공으로 인정합니다.
    // 1. 상인은 통지 데이터의 out_trade_no가 상인 시스템에 생성된 주문 번호인지를 확인해야 합니다.
    // 2. total_amount가 해당 주문의 실제 금액(즉, 상인 주문 생성시의 금액)인지 확인해야 합니다.
    // 3. 통지의 seller_id(또는 seller_email)가 out_trade_no 이 표시한 단일 품목의 상인 편의 조작자인지를 확인해야 합니다.
    // 4. app_id가 해당 상인 자체인지 확인해야 합니다.
    // 5. 다른 비즈니스 로직인 경우
    // ===================================================================================================

    // 5. 알리페이 콜백 처리
    return new Response(200, [], 'success');
}
```
> 주의 : 플러그인 자체 `return Pay::alipay()->success();`를 사용하여 알리페이의 콜백 응답을 하면 미들웨어 문제가 발생할 수 있습니다. 따라서 알리페이 응답에는 webman의 응답 클래스 `support\Response;`를 사용해야 합니다.

#### 동기 콜백

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『알리페이』동기 통지
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『알리페이』동기 통지'.json_encode($request->get()));
    return 'success';
}
```
## 완전한 예제 코드

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## 더 읽어보기

공식 문서 방문 https://pay.yansongda.cn/docs/v3/
