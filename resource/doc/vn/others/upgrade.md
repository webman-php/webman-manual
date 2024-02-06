# Phương pháp nâng cấp

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **Lưu ý**
> Vì proxy của Alibaba Cloud Composer đã ngừng đồng bộ dữ liệu từ nguồn chính thức của Composer, nên hiện tại không thể sử dụng proxy của Alibaba Cloud Composer để nâng cấp lên phiên bản mới nhất của webman. Vui lòng sử dụng lệnh sau đây để khôi phục việc sử dụng nguồn dữ liệu chính thức của Composer: `composer config -g --unset repos.packagist`
