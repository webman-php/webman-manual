# Quản lý tiến trình
Webman đi kèm với một tiến trình giám sát (monitor) để hỗ trợ hai chức năng
1. Giám sát cập nhật tệp và tự động tải lại mã kinh doanh mới (thường được sử dụng trong quá trình phát triển)
2. Giám sát việc tiêu thụ bộ nhớ của tất cả các tiến trình, nếu một tiến trình nào đó tiêu thụ bộ nhớ gần sát giới hạn `memory_limit` trong tệp `php.ini` thì sẽ tự động khởi động lại tiến trình đó một cách an toàn (không ảnh hưởng đến kinh doanh)

## Cấu hình giám sát
Tệp cấu hình `config/process.php` có phần cấu hình `monitor` như sau:
```php

global $argv;

return [
    // Phát hiện cập nhật tệp và tải lại tự động
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Giám sát các thư mục này
            'monitorDir' => array_merge([    // Các thư mục nào cần giám sát cập nhật tệp
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Các tệp có phần mở rộng sau sẽ được giám sát
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Cho phép giám sát cập nhật tệp
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Cho phép giám sát tiêu thụ bộ nhớ
            ]
        ]
    ]
];
```
`monitorDir` dùng để cấu hình giám sát cập nhật tại những thư mục nào (không nên giám sát quá nhiều tệp trong thư mục giám sát).
`monitorExtensions` dùng để cấu hình những tệp có phần mở rộng nào trong thư mục `monitorDir` cần phải được giám sát.
`options.enable_file_monitor` có giá trị `true`, thì giám sát cập nhật tệp sẽ được bật (mặc định trong khi chạy ở chế độ debug trên hệ điều hành Linux).
`options.enable_memory_monitor` có giá trị `true`, thì giám sát việc tiêu thụ bộ nhớ sẽ được bật (giám sát tiêu thụ bộ nhớ không hỗ trợ trên hệ điều hành Windows).

> **Lưu ý**
> Trên hệ điều hành Windows, giám sát cập nhật tệp chỉ được bật khi chạy `windows.bat` hoặc `php windows.php`.
