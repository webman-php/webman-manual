## Tệp cấu hình đường dẫn
Tệp cấu hình đường dẫn của plugin được lưu tại `plugin/tên_plugin/config/route.php`

## Đường dẫn mặc định
Tất cả đường dẫn URL của ứng dụng plugin bắt đầu từ `/app`, ví dụ `plugin\foo\app\controller\UserController` có đường dẫn URL là `http://127.0.0.1:8787/app/foo/user`

## Tắt đường dẫn mặc định
Nếu muốn tắt đường dẫn mặc định của một ứng dụng plugin, cấu hình trong tệp đường dẫn như sau
```php
Route::disableDefaultRoute('foo');
```

## Xử lý callback 404
Nếu muốn thiết lập fallback cho một ứng dụng plugin, cần truyền tên plugin qua tham số thứ hai, ví dụ
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
