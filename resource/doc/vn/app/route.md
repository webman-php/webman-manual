## Tệp cấu hình định tuyến
Tệp cấu hình định tuyến của plugin nằm tại `plugin/địa chỉ_plugin/config/route.php`

## Đường dẫn mặc định
Tất cả đường dẫn url của ứng dụng plugin đều bắt đầu bằng `/app`, ví dụ `plugin\foo\app\controller\UserController` có đường dẫn url là `http://127.0.0.1:8787/app/foo/user`

## Vô hiệu hóa định tuyến mặc định
Nếu muốn vô hiệu hóa định tuyến mặc định của một ứng dụng plugin, hãy đặt trong tệp cấu hình định tuyến như sau
```php
Route::disableDefaultRoute('foo');
```

## Xử lý gọi lại 404
Nếu muốn thiết lập fallback cho một ứng dụng plugin, cần truyền tên plugin qua tham số thứ hai, ví dụ
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
