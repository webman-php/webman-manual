# Gói SDK thanh toán (V3)


## Địa chỉ dự án

 https://github.com/yansongda/pay

## Cài đặt

```php
composer require yansongda/pay ^3.0.0
```

## Sử dụng

> Lưu ý: Tài liệu dưới đây sẽ được viết dựa trên môi trường sandbox của Alipay, nếu có vấn đề, vui lòng phản hồi ngay!

## Tập tin cấu hình

Giả sử có tập tin cấu hình sau `config/payment.php`

```php
<?php
/**
 * @desc Tập tin cấu hình thanh toán
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Bắt buộc - app_id được cấp phát bởi Alipay
            'app_id' => '20160909004708941',
            // Bắt buộc - Khóa ứng dụng dùng để ký nhận chuỗi hoặc đường dẫn
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Bắt buộc - Đường dẫn của chứng chỉ công khai của ứng dụng
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Bắt buộc - Đường dẫn của chứng chỉ công khai của Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Bắt buộc - Đường dẫn của chứng chỉ gốc của Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Tùy chọn - Địa chỉ URL callback đồng bộ
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Tùy chọn - Địa chỉ URL callback bất đồng bộ
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Tùy chọn - ID nhà cung cấp dịch vụ trong trường hợp chế độ là Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Tùy chọn - Mặc định là chế độ bình thường. Có thể chọn: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Bắt buộc - Mã ngân hàng, trong trường hợp chế độ dịch vụ, là mã ngân hàng của dịch vụ
            'mch_id' => '',
            // Bắt buộc - Khóa bí mật của ngân hàng
            'mch_secret_key' => '',
            // Bắt buộc - Khóa ứng dụng dùng để ký nhận chuỗi hoặc đường dẫn
            'mch_secret_cert' => '',
            // Bắt buộc - Đường dẫn của chứng chỉ công khai của ngân hàng
            'mch_public_cert_path' => '',
            // Bắt buộc
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Tùy chọn - app_id của công chúng
            'mp_app_id' => '2016082000291234',
            // Tùy chọn - app_id của ứng dụng nhỏ
            'mini_app_id' => '',
            // Tùy chọn - app_id của ứng dụng
            'app_id' => '',
            // Tùy chọn - app_id hợp nhất
            'combine_app_id' => '',
            // Tùy chọn - Mã ngân hàng hợp nhất
            'combine_mch_id' => '',
            // Tùy chọn - Trong trường hợp chế độ dịch vụ, app_id của công chúng con
            'sub_mp_app_id' => '',
            // Tùy chọn - Trong trường hợp chế độ dịch vụ, app_id của ứng dụng con
            'sub_app_id' => '',
            // Tùy chọn - Trong trường hợp chế độ dịch vụ, app_id của ứng dụng nhỏ con
            'sub_mini_app_id' => '',
            // Tùy chọn - Trong trường hợp chế độ dịch vụ, mã ngân hàng con
            'sub_mch_id' => '',
            // Tùy chọn - Đường dẫn của chứng chỉ công khai của Wechat, tùy chọn, khuyến nghị cấu hình tham số này ở chế độ php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Tùy chọn - Mặc định là chế độ bình thường. Có thể chọn: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Đề xuất mức độ môi trường sản xuất là info, môi trường phát triển là debug
        'type' => 'single', // tùy chọn, có thể chọn hàng ngày
        'max_file' => 30, // tùy chọn, chỉ có hiệu lực khi loại là hàng ngày, mặc định 30 ngày
    ],
    'http' => [ // tùy chọn
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Để biết thêm tùy chọn cấu hình, vui lòng tham khảo [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Lưu ý: Thư mục chứng chỉ không có quy định cụ thể, ví dụ trên đây đặt trong thư mục `payment` của framework

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## Khởi tạo

Khởi tạo bằng cách gọi phương thức `config` trực tiếp
```php
// Lấy tập tin cấu hình config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Lưu ý: Nếu là chế độ sandbox của Alipay, nhớ mở tùy chọn cấu hình `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` này, mặc định là chế độ bình thường.

## Thanh toán (trang web)

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
    // 1. Lấy tập tin cấu hình config/payment.php
    $config = Config::get('payment');

    // 2. Khởi tạo cấu hình
    Pay::config($config);

    // 3. Thanh toán trang web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'thanh toán webman',
        '_method' => 'get' // Sử dụng phương thức GET để chuyển hướng
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```
## Gọi lại bất đồng bộ

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
    // 1. Lấy file cấu hình config/payment.php
    $config = Config::get('payment');

    // 2. Khởi tạo cấu hình
    Pay::config($config);

    // 3. Xử lý thông báo từ 'Alipay'
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Vui lòng tự kiểm tra trade_status và các logic khác, chỉ khi trạng thái thông báo giao dịch là TRADE_SUCCESS hoặc TRADE_FINISHED, 'Alipay' mới coi như người mua đã thanh toán thành công.
    // 1. Người bán cần xác minh dữ liệu thông báo này có phải là mã đơn hàng do hệ thống người bán tạo ra không;
    // 2. Xác định xem total_amount thực sự là số tiền thực tế của đơn hàng này (tức là số tiền mà người bán tạo khi đặt hàng);
    // 3. Xác minh seller_id (hoặc seller_email) có phải là bên thực hiện tác vụ của đơn hàng out_trade_no này không;
    // 4. Xác minh app_id có phải là của người bán không.
    // 5. Các trường hợp logic khác
    // ===================================================================================================

    // 5. Xử lý thông báo từ 'Alipay'
    return new Response(200, [], 'success');
}
```
> Lưu ý: Không nên sử dụng `return Pay::alipay()->success();` của plugin để đáp ứng thông báo từ 'Alipay', nếu bạn sử dụng middleware có thể gây ra vấn đề về middleware. Vì vậy, để đáp ứng thông báo từ 'Alipay', bạn cần sử dụng lớp phản hồi của webman `support\Response;`

## Gọi lại đồng bộ

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Thông báo đồng bộ từ 'Alipay'
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Thông báo đồng bộ từ 'Alipay''.json_encode($request->get()));
    return 'success';
}
```
## Đoạn mã đầy đủ của ví dụ

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Thêm nội dung

Truy cập tài liệu chính thức tại https://pay.yansongda.cn/docs/v3/
