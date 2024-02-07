# Phương pháp nâng cấp

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **Lưu ý**
> Vì proxy composer của Alibaba Cloud đã ngừng đồng bộ dữ liệu từ nguồn gốc của composer, vì vậy hiện tại không thể sử dụng proxy của Alibaba Cloud để nâng cấp lên phiên bản webman mới nhất, vui lòng sử dụng lệnh sau đây `composer config -g --unset repos.packagist` để khôi phục việc sử dụng nguồn dữ liệu chính thức của composer.
