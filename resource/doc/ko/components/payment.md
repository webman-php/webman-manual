# 결제 SDK (V3)


## 프로젝트 주소

 https://github.com/yansongda/pay

## 설치

```php
composer require yansongda/pay ^3.0.0
```

## 사용

> 설명: 아래의 내용은 알리페이 테스트 환경을 기준으로하여 문서를 작성하였으니 문제가 있을 시에 빠르게 피드백 부탁드립니다!

## 구성 파일

다음과 같은 구성 파일 `config/payment.php`가 있다고 가정합니다.

```php
<?php
/**
 * @desc 결제 구성 파일
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // 필수 - 알리페이에서 할당한 app_id
            'app_id' => '20160909004708941',
            // 필수 - 애플리케이션 개인 키 문자열 또는 경로
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // 필수 - 애플리케이션 공개 키 인증서 경로
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // 필수 - 알리페이 공개 키 인증서 경로
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // 필수 - 알리페이 루트 인증서 경로
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // 선택 - 동기 콜백 주소
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // 선택 - 비동기 콜백 주소
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // 선택 - 서비스 제공자 모드의 서비스 제공자 ID, 모드가 Pay::MODE_SERVICE 인 경우에 사용하는 매개변수
            'service_provider_id' => '',
            // 선택 - 기본적으로 정상 모드입니다. MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE로 선택할 수 있습니다
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // 필수 - 상인 번호, 서비스 제공자 모드의 경우 서비스 제공자 상인 번호입니다
            'mch_id' => '',
            // 필수 - 상인 비밀 키
            'mch_secret_key' => '',
            // 필수 - 상인 개인 키 문자열 또는 경로
            'mch_secret_cert' => '',
            // 필수 - 상인 공개 키 인증서 경로
            'mch_public_cert_path' => '',
            // 필수
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // 선택 - 공중 번호의 app_id
            'mp_app_id' => '2016082000291234',
            // 선택 - 소프트웨어 번호의 app_id
            'mini_app_id' => '',
            // 선택 - 앱의 app_id
            'app_id' => '',
            // 선택 - 결합 app_id
            'combine_app_id' => '',
            // 선택 - 결합 상인 번호
            'combine_mch_id' => '',
            // 선택 - 서비스 제공자 모드 하위 공중 번호의 app_id
            'sub_mp_app_id' => '',
            // 선택 - 서비스 제공자 모드 하위 앱의 app_id
            'sub_app_id' => '',
            // 선택 - 서비스 제공자 모드 하위 소프트웨어의 app_id
            'sub_mini_app_id' => '',
            // 선택 - 서비스 제공자 모드 하위 상인 id
            'sub_mch_id' => '',
            // 선택 - 위챗 공개 키 인증서 경로, 선택 사항, php-fpm 모드에서 이 매개변수를 설정하는 것이 강력히 권장됩니다
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // 선택 - 기본적으로 정상 모드입니다. MODE_NORMAL, MODE_SERVICE로 선택할 수 있습니다
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // 제안: 프로덕션 환경 레벨을 info로 조정하고, 개발 환경을 debug로 합니다
        'type' => 'single', // 선택 사항, daily로 설정하면 필수적입니다.
        'max_file' => 30, // 선택 사항, 타입이 daily일 때, 기본적으로 30 일입니다
    ],
    'http' => [ // 선택 사항
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 더 많은 구성 항목은 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)을 참조하십시오
    ],
    '_force' => true,
];
```
> 노트: 인증서 디렉토리는 규정되어 있지 않지만, 위의 예는 프레임워크의 `payment` 디렉토리에 배치되어 있는 것입니다.

## 초기화

`config` 메소드를 직접 호출하여 초기화합니다.
```php
// 구성 파일 config/payment.php 가져오기
$config = Config::get('payment');
Pay::config($config);
```
> 노트: 알리페이 샌드박스 모드인 경우에는 반드시 구성 파일 `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`를 활성화해야 합니다. 이 옵션은 기본적으로 정상 모드로 설정되어 있습니다.

## 결제 (웹)

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

    // 2. 초기화 구성
    Pay::config($config);

    // 3. 웹 결제
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // get 방식으로 전환
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## 비동기 콜백

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: 『알리페이』 비동기 통지
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. 구성 파일 config/payment.php 가져오기
    $config = Config::get('payment');

    // 2. 초기화 구성
    Pay::config($config);

    // 3. 알리페이 콜백 처리
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // trade_status를 확인하고 다른 로직을 판단해야 합니다. 거래 알림 상태가 TRADE_SUCCESS 또는 TRADE_FINISHED 인 경우에만 알리페이가 구매자 결제를 완료로 판단합니다.
    // 1. 상인은 알림 데이터 중의 out_trade_no가 상인 시스템에서 생성한 주문번호인지를 확인해야 합니다.
    // 2. total_amount가 해당 주문의 실제 금액(즉, 상인 주문을 생성했을 때의 금액)인지 확인해야 합니다.
    // 3. 알림의 seller_id(또는 seller_email)가 out_trade_no에 해당하는 거래의 대응하는 조작자인지 확인해야 합니다.
    // 4. app_id가 해당 상인 자신인지 확인해야 합니다.
    // 5. 다른 비즈니스 로직
    // ===================================================================================================

    // 5. 알리페이 콜백 처리
    return new Response(200, [], 'success');
}
```
> 노트: 알리페이 콜백으로 `return Pay::alipay()->success();`를 사용할 수 없습니다. 중간웨어를 사용하기 때문에 문제가 발생할 수 있습니다. 따라서 알리페이 응답은 webman의 응답 클래스 `support\Response;`를 사용해야 합니다.
## 동기 콜백

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『알리페이』 동기 통지
 * @param Request $request
 * @author Tinywan(완소보 완)
 */
public function alipayReturn(Request $request)
{
    Log::info('『알리페이』 동기 통지' . json_encode($request->get()));
    return 'success';
}
```
## 전체 예제 코드

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## 더보기

공식 문서 방문 https://pay.yansongda.cn/docs/v3/
