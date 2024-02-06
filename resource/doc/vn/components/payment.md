# SDK Thanh toán (V3)

## Địa chỉ dự án

https://github.com/yansongda/pay

## Cài đặt

```php
composer require yansongda/pay ^3.0.0
```

## Sử dụng

> Lưu ý: Tài liệu này sẽ được viết dựa trên môi trường Sandbox của Alipay. Nếu có vấn đề, hãy phản hồi kịp thời nhé!

### Tệp cấu hình

Giả sử có tệp cấu hình sau `config/payment.php`

```php
<?php
/**
 * @desc Tệp cấu hình thanh toán
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Bắt buộc - app_id được cấp phát bởi Alipay
            'app_id' => '20160909004708941',
            // Bắt buộc - Khóa ứng dụng chuỗi hoặc đường dẫn
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Bắt buộc - Đường dẫn chứng chỉ công khai của ứng dụng
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Bắt buộc - Đường dẫn chứng chỉ công khai Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Bắt buộc - Đường dẫn chứng chỉ gốc Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Tùy chọn - Địa chỉ callback đồng bộ
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Tùy chọn - Địa chỉ callback bất đồng bộ
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Tùy chọn - ID nhà cung cấp dịch vụ trong chế độ nhà cung cấp, sử dụng tham số này khi mode là Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Tùy chọn - Mặc định là chế độ bình thường. Có thể chọn là: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Bắt buộc - Mã cửa hàng, trong trường hợp chế độ nhà cung cấp là mã cửa hàng dịch vụ
            'mch_id' => '',
            // Bắt buộc - Khóa bí mật của cửa hàng
            'mch_secret_key' => '',
            // Bắt buộc - Đường dẫn chứng chỉ cá nhân của cửa hàng
            'mch_secret_cert' => '',
            // Bắt buộc - Đường dẫn chứng chỉ công khai của cửa hàng
            'mch_public_cert_path' => '',
            // Bắt buộc
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Tùy chọn - app_id của công cộng
            'mp_app_id' => '2016082000291234',
            // Tùy chọn - app_id của ứng dụng nhỏ
            'mini_app_id' => '',
            // Tùy chọn - app_id của ứng dụng
            'app_id' => '',
            // Tùy chọn - app_id của ứng dụng kết hợp
            'combine_app_id' => '',
            // Tùy chọn - mã cửa hàng kết hợp
            'combine_mch_id' => '',
            // Tùy chọn - Dưới dạng chế độ nhà cung cấp, app_id của từng công cộng cấp dịch vụ
            'sub_mp_app_id' => '',
            // Tùy chọn - Dưới dạng chế độ nhà cung cấp, app_id của từng ứng dụng
            'sub_app_id' => '',
            // Tùy chọn - Dưới dạng chế độ nhà cung cấp, app_id của từng ứng dụng nhỏ
            'sub_mini_app_id' => '',
            // Tùy chọn - Dưới dạng chế độ nhà cung cấp, mã cửa hàng con
            'sub_mch_id' => '',
            // Tùy chọn - Đường dẫn chứng chỉ công khai Wechat, tùy chọn, rất khuyến khích cấu hình tham số này khi ở chế độ php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Tùy chọn - Mặc định là chế độ bình thường. Có thể chọn là: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Đề xuất độ cấp đoạn cho môi trường sản xuất là info, môi trường phát triển là debug
        'type' => 'single', // tùy chọn, có thể chọn hàng ngày.
        'max_file' => 30, // tùy chọn, có hiệu lực chỉ khi type là hàng ngày, mặc định 30 ngày
    ],
    'http' => [ // tùy chọn
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Để biết thêm cấu hình vui lòng tham khảo [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Lưu ý: Thư mục chứng chỉ không có quy định cụ thể, ví dụ trên đây chỉ đưa ra cấu trúc trong thư mục `payment` của framework

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Khởi tạo

Khởi tạo trực tiếp thông qua phương thức `config`
```php
// Lấy tệp cấu hình config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Lưu ý: Nếu ở chế độ sandbox của Alipay, nhớ thực hiện cấu hình mở `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, tham số mặc định này là chế độ bình thường.

### Thanh toán (trên web)

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
    // 1. Lấy tệp cấu hình config/payment.php
    $config = Config::get('payment');

    // 2. Khởi tạo cấu hình
    Pay::config($config);

    // 3. Thanh toán trên web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'thanh toán webman',
        '_method' => 'get' // Sử dụng phương thức get để chuyển hướng
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Callback

#### Callback bất đồng bộ

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: Thông báo bất đồng bộ từ 'Alipay'
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Lấy tệp cấu hình config/payment.php
    $config = Config::get('payment');

    // 2. Khởi tạo cấu hình
    Pay::config($config);

    // 3. Xử lý callback từ Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Vui lòng tự kiểm tra trade_status và kiểm tra logic khác. Chỉ khi trạng thái thông báo giao dịch là TRADE_SUCCESS hoặc TRADE_FINISHED, Alipay mới coi như người mua đã thanh toán thành công.
    // 1. Người bán cần xác minh xem out_trade_no trong dữ liệu thông báo có phải là mã đơn hàng được tạo trong hệ thống người bán hay không;
    // 2. Xác định xem total_amount có chắc chắn là số tiền thực tế của đơn hàng này không (tức là số tiền khi đơn hàng được tạo);
    // 3. Kiểm tra seller_id (hoặc seller_email) của thông báo giao dịch có phải là bên thực hiện đơn đó hay không;
    // 4. Xác thực app_id có phải là bản thân của người bán hay không.
    // 5. Các trường hợp logic kinh doanh khác
    // ===================================================================================================

    // 5. Xử lý callback từ Alipay
    return new Response(200, [], 'success');
}
```
> Lưu ý: Không sử dụng `return Pay::alipay()->success();` để phản hồi callback từ Alipay, nếu bạn sử dụng middleware sẽ gặp vấn đề. Vì vậy, để phản hồi từ Alipay cần sử dụng lớp phản hồi của webman `support\Response;`

#### Callback đồng bộ

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc:  thông báo đồng bộ từ 'Alipay'
 * @param Request $request
 * @param Author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Thông báo đồng bộ từ 'Alipay''.json_encode($request->get()));
    return 'success';
}
```

## Mã đầy đủ của ví dụ

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Nội dung thêm

Truy cập tài liệu chính thức https://pay.yansongda.cn/docs/v3/
