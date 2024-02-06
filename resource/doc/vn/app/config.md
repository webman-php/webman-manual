# Tệp Cấu hình

Cấu hình plugin giống như dự án webman thông thường, tuy nhiên cấu hình plugin thường chỉ có hiệu lực đối với plugin hiện tại, không ảnh hưởng đến dự án chính.

Ví dụ, giá trị của `plugin.foo.app.controller_suffix` chỉ ảnh hưởng đến hậu tố của controller của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.app.controller_reuse` chỉ ảnh hưởng đến việc sử dụng lại controller của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.middleware` chỉ ảnh hưởng đến middleware của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.view` chỉ ảnh hưởng đến view được sử dụng trong plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.container` chỉ ảnh hưởng đến container được sử dụng trong plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.exception` chỉ ảnh hưởng đến lớp xử lý ngoại lệ của plugin, không ảnh hưởng đến dự án chính.

Tuy nhiên, vì định tuyến là toàn cục, nên cấu hình định tuyến của plugin cũng ảnh hưởng toàn cục.

## Lấy cấu hình
Cách lấy cấu hình của một plugin nào đó là `config('plugin.{plugin}.{cấu hình cụ thể}')`; ví dụ lấy tất cả cấu hình trong `plugin/foo/config/app.php` là `config('plugin.foo.app')`.
Tương tự, dự án chính hoặc plugin khác đều có thể sử dụng `config('plugin.foo.xxx')` để lấy cấu hình của plugin `foo`.

## Cấu hình không được hỗ trợ 
Ứng dụng plugin không hỗ trợ cấu hình `server.php`, `session.php`, không hỗ trợ cấu hình `app.request_class`, `app.public_path`, `app.runtime_path`.
