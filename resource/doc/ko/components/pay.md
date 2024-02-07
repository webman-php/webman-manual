# 결제 SDK


### 프로젝트 주소

https://github.com/yansongda/pay

### 설치

```php
composer require yansongda/pay -vvv
```

### 사용

**알리페이**

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
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuWJKrQ6SWvS6niI+4vEVZiYfjkCfLQfoFI2nCp9ZLDS42QtiL4Ccyx8scgc3nhVwmVRte8f57TFvGhvJD0upT4O5O/lRxmTjechXAorirVdAODpOu0mFfQV9y/T9o9hHnU+VmO5spoVb3umqpq6D/Pt8p25Yk852/w01VTIczrXC4QlrbOEe3sr1E9auoC7rgYjjCO6lZUIDjX/oBmNXZxhRDrYx4Yf5X7y8FRBFvygIE2FgxV4Yw+SL3QAa2m5MLcbusJpxOml9YVQfP8iSurx41PvvXUMo49JG3BDVernaCYXQCoUJv9fJwbnfZd7J5YByC+5KM4sblJTq7bXZWQIDAQAB',
        // 암호화 방식: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // 공개 키 인증서 모드를 사용하는 경우 아래 두 매개변수를 구성하고 ali_public_key를 .crt로 끝나는 알리페이 공개 키 인증서 경로로 수정하십시오 (예 : ./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // 애플리케이션 공개 키 인증서 경로
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // 알리페이 루트 인증서 경로
        'log' => [ // 선택 사항
            'file' => './logs/alipay.log',
            'level' => 'info', // 제안되는 프로덕션 환경 레벨은 info이고, 개발 환경은 debug입니다.
            'type' => 'single', // 선택 사항, daily로 선택
            'max_file' => 30, // 선택 사항, 유형이 일일 때 유효, 기본값 30 일
        ],
        'http' => [ // 선택 사항
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 더 많은 구성 항목은 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)을 참조하십시오.
        ],
        'mode' => 'dev', // 선택 사항,이 매개변수를 설정하면 샌드박스 모드로 진입합니다.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 테스트',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// laravel 프레임워크에서는 직접 `return $alipay`를 사용하십시오.
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 네, 검사는 이렇게 간단합니다!

        // 주문 번호 : $data->out_trade_no
        // 알리페이 거래 번호 : $data->trade_no
        // 주문 총액 : $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try{
            $data = $alipay->verify(); // 네, 검사는 이렇게 간단합니다!

            // trade_status 및 기타 로직을 확인하십시오. 알리페이의 비즈니스 알림에서 TRADE_SUCCESS 또는 TRADE_FINISHED 거래 통지 상태만 성공적인 구매로 인정합니다.
            // 1. 상인은 통지 데이터의 out_trade_no가 상인 시스템에서 생성한 주문 번호인지 확인해야 합니다.
            // 2. total_amount가 주문의 실제 금액 인지 확인해야합니다 (즉, 상인 주문 생성 때의 금액).
            // 3. 판매자_ID (또는 seller_email)가 out_trade_no의 이 건의 해당 작업 측 (경우에 따라 한 상인에 여러 seller_id/seller_email이 있을 수 있음)인지 확인하십시오.
            // 4. app_id가 상점 자체인지 확인하십시오.
            // 5. 기타 비즈니스 로직 상황

            Log::debug('알리페이 알림', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// laravel 프레임워크에서는 직접 `return $alipay->success()`를 사용하십시오.
    }
}
```


**위챗**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // 앱 APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // 공개 번호 APPID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 소프트웨어 APPID
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // 선택 사항, 환불 및 기타 경우에 사용됨
        'cert_key' => './cert/apiclient_key.pem',// 선택 사항, 환불 및 기타 경우에 사용됨
        'log' => [ // 선택 사항
            'file' => './logs/wechat.log',
            'level' => 'info', // 제안되는 생산 환경 레벨은 info이고, 개발 환경은 debug입니다.
            'type' => 'single', // 선택 사항, 일일로 선택
            'max_file' => 30, // 선택 사항, 유형이 일일 경우 유효, 기본값 30 일
        ],
        'http' => [ // 선택 사항
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 더 많은 구성 항목은 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)을 참조하십시오.
        ],
        'mode' => 'dev', // 선택 사항, dev/hk; 'hk'로 설정하면 홍콩 게이트웨이입니다.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **단위: 분**
            'body' => 'test body - 테스트',
            'openid' => 'onkVf1FjWS5SBIixxxxxxx',
        ];

        $pay = Pay::wechat($this->config)->mp($order);

        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 네, 검사는 이렇게 간단합니다!

            Log::debug('위챗 알림', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// laravel 프레임워크에서는 직접 `return $pay->success()`를 사용하십시오.
    }
}
```

### 더 많은 컨텐츠

https://pay.yanda.net.cn/docs/2.x/overview에 방문하세요.
