# 결제 SDK


### 프로젝트 주소

https://github.com/yansongda/pay

### 설치

```php
composer require yansongda/pay -vvv
```

### 사용법

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
        // 공개 키 인증서 모드인 경우 두 개의 매개변수를 설정하고 ali_public_key를 (.crt)로 끝나는 알리페이 공개 키 인증서 경로로 수정하십시오([./cert/alipayCertPublicKey_RSA2.crt] 같은).
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // 어플리케이션 공개 키 인증서 경로
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // 알리페이 루트 인증서 경로
        'log' => [ // 선택 사항
            'file' => './logs/alipay.log',
            'level' => 'info', // 상업 환경에서는 info로 권장하며, 개발 환경에서는 debug로 설정하십시오
            'type' => 'single', // 선택 사항, 매일만 선택 가능
            'max_file' => 30, // 선택 사항, 종류가 일일 때만 유효, 기본값은 30일
        ],
        'http' => [ // 선택 사항
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 더 많은 구성 항목은 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)을 참조하십시오
        ],
        'mode' => 'dev', // 선택 사항, 이 매개변수를 설정하면 샌드박스 모드로 진입
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 테스트',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // 라라벨 프레임워크에서는 직접 `return $alipay`을 사용하십시오
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 네, 검증은 이렇게 간단합니다!

        // 주문 번호: $data->out_trade_no
        // 알리페이 거래 번호: $data->trade_no
        // 주문 총액: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // 네, 검증은 이렇게 간단합니다!

            // trade_status를 자체적으로 판단하고 기타 논리를 판단해야 합니다. 알리페이의 비즈니스 알림에서는 거래 알림 상태가 TRADE_SUCCESS 또는 TRADE_FINISHED일 때에만 구매자 지불이 완료된 것으로 인정합니다.
            // 1. 상인은 알림 데이터의 out_trade_no가 상인 시스템에 생성된 주문 번호인지 확인해야 합니다.
            // 2. total_amount가 주문의 실제 금액(상인 주문 생성 시의 금액)인지 확인해야 합니다.
            // 3. 알림의 seller_id(seller_email)가 out_trade_no 이 거래의 대응 운영자인지 확인해야 합니다(상인은 경우, 하나의 상인에는 여러 개의 seller_id/seller_email이 있을 수 있는 경우도 있습니다).
            // 4. app_id는 상인 그 자체인지 확인해야 합니다.
            // 5. 기타 비즈니스 논리 상황

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // 라라벨 프레임워크에서는 직접 `return $alipay->success()`을 사용하십시오
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
        'app_id' => 'wxb3fxxxxxxxxxxx', // 공중 APPID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 소프트웨어 APPID
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // 선택사항, 환불 등에서 사용됨
        'cert_key' => './cert/apiclient_key.pem', // 선택사항, 환불 등에서 사용됨
        'log' => [ // 선택 사항
            'file' => './logs/wechat.log',
            'level' => 'info', // 생산 환경에서는 info로 설정하는 것이 좋으며, 개발 환경에서는 debug로 설정
            'type' => 'single', // 선택 사항, 매일만 선택 가능
            'max_file' => 30, // 선택 사항, 종류가 일일 때만 유효, 기본값은 30일
        ],
        'http' => [ // 선택 사항
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 더 많은 구성 항목은 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)을 참조하십시오
        ],
        'mode' => 'dev', // 선택 사항, dev/hk; `hk`로 설정하거나 홍콩 게이트웨이로 들어갈 때
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
            $data = $pay->verify(); // 네, 검증은 이렇게 간단합니다!

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// laravel 프레임워크에서는 직접 `return $pay->success()`를 사용하십시오
    }
}
```

### 더 많은 내용

https://pay.yanda.net.cn/docs/2.x/overview를 방문하세요
