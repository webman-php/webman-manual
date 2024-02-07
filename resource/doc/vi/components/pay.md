# SDK thanh toán


### Địa chỉ dự án

 https://github.com/yansongda/pay

### Cài đặt

```php
composer require yansongda/pay -vvv
```

### Sử dụng

**Alipay**

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
        // Phương thức mã hóa: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Đường dẫn sử dụng chứng chỉ công khai, vui lòng đặt hai thông số dưới đây khi sử dụng chế độ chứng chỉ công khai và thay đổi ali_public_key thành đường dẫn của chứng chỉ công khai của Alipay với phần mở rộng là .crt, ví dụ (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //đường dẫn chứng chỉ công khai ứng dụng
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', //đường dẫn chứng chỉ gốc của Alipay
        'log' => [ // tùy chọn
            'file' => './logs/alipay.log',
            'level' => 'info', // Đề xuất điều chỉnh cấp độ cho môi trường production thành info, môi trường phát triển thành debug
            'type' => 'single', // tuỳ chọn, tuỳ chọn hàng ngày.
            'max_file' => 30, // tuỳ chọn, hiệu quả khi loại là hàng ngày, mặc định 30 ngày
        ],
        'http' => [ // tuỳ chọn
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Để biết thêm cấu hình, vui lòng tham khảo [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // tùy chọn, đặt tham số này, sẽ đi vào chế độ sandbox
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - thử nghiệm',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // Trong framework Laravel, vui lòng sử dụng `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Đúng vậy, xác minh chữ ký chỉ với một dòng mã!

        // Số đơn hàng: $data->out_trade_no
        // Số giao dịch Alipay: $data->trade_no
        // Tổng số tiền đơn hàng: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // Đúng vậy, xác minh chữ ký chỉ với một dòng mã!

            // Vui lòng tự kiểm tra trade_status và các logic khác, trong thông báo kinh doanh của Alipay, chỉ khi trạng thái thông báo giao dịch là TRADE_SUCCESS hoặc TRADE_FINISHED, Alipay mới coi như mua hàng thành công.
            // 1. Người bán cần xác minh xem out_trade_no trong dữ liệu thông báo này có phải là số đơn hàng được tạo trong hệ thống của người bán không;
            // 2. Kiểm tra xem total_amount có chắc chắn là số tiền thực của đơn hàng (tức là số tiền đặt hàng khi người bán tạo ra);
            // 3. Kiểm tra xem seller_id (hoặc seller_email) trong thông báo có phải là bên thực hiện tương ứng với đơn hàng này không (có lúc một người bán có nhiều seller_id/seller_email);
            // 4. Xác minh app_id có phải là người bán chính mình không.
            // 5. Tình hình logic thương mại khác

            Log::debug('Thông báo Alipay', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // Trong framework Laravel, vui lòng sử dụng `return $alipay->success()`
    }
}
```

**WeChat**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // APPID ứng dụng nhỏ
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // tùy chọn, được sử dụng trong trường hợp hoàn trả và các tình huống khác
        'cert_key' => './cert/apiclient_key.pem', // tùy chọn, được sử dụng trong trường hợp hoàn trả và các tình huống khác
        'log' => [ // tùy chọn
            'file' => './logs/wechat.log',
            'level' => 'info', // Đề xuất điều chỉnh cấp độ cho môi trường production thành info, môi trường phát triển thành debug
            'type' => 'single', // tùy chọn, hàng ngày
            'max_file' => 30, // tùy chọn, khi type là hàng ngày, mặc định 30 ngày
        ],
        'http' => [ // tùy chọn
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Để biết thêm cấu hình, vui lòng tham khảo [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // tùy chọn, dev/hk; khi là `hk`, là cổng của Hồng Kông.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **Đơn vị: xu**
            'body' => 'test body - thử nghiệm',
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
            $data = $pay->verify(); // Đúng vậy, xác minh chữ ký chỉ với một dòng mã!

            Log::debug('Thông báo Wechat', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send(); // Trong framework Laravel, vui lòng sử dụng `return $pay->success()`
    }
}
```

### Nội dung thêm

Truy cập  https://pay.yanda.net.cn/docs/2.x/overview
