# Tập tin cấu hình

Cấu hình plugin tương tự như dự án webman thông thường, tuy nhiên cấu hình của plugin thường chỉ có tác dụng đối với plugin hiện tại và không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.app.controller_suffix` chỉ ảnh hưởng đến hậu tố điều khiển của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.app.controller_reuse` chỉ ảnh hưởng đến việc tái sử dụng điều khiển của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.middleware` chỉ ảnh hưởng đến middleware của plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.view` chỉ ảnh hưởng đến chế độ xem được sử dụng bởi plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.container` chỉ ảnh hưởng đến container được sử dụng bởi plugin, không ảnh hưởng đến dự án chính.
Ví dụ, giá trị của `plugin.foo.exception` chỉ ảnh hưởng đến lớp xử lý ngoại lệ của plugin, không ảnh hưởng đến dự án chính.

Tuy nhiên, do định tuyến là toàn cầu, nên cấu hình định tuyến của plugin cũng ảnh hưởng đến toàn cầu.

## Lấy cấu hình
Để lấy cấu hình của một plugin cụ thể, bạn có thể sử dụng phương pháp sau: `config('plugin.{plugin}.{cấu hình cụ thể}');`, ví dụ: để lấy tất cả các cấu hình của `plugin/foo/config/app.php`, bạn có thể sử dụng `config('plugin.foo.app')`. Tương tự, dự án chính hoặc các plugin khác cũng có thể sử dụng `config('plugin.foo.xxx')` để lấy cấu hình của plugin foo.

## Cấu hình không hỗ trợ
Ứng dụng plugin không hỗ trợ cấu hình server.php, session.php và không hỗ trợ cấu hình `app.request_class`, `app.public_path`, `app.runtime_path`.
