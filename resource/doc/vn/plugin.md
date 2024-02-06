# Các plugin
Các plugin được chia thành **plugin cơ bản** và **plugin ứng dụng**.

#### Plugin cơ bản
Plugin cơ bản có thể hiểu là một số thành phần cơ bản của webman, nó có thể là một thư viện chung (ví dụ: webman/think-orm), có thể là một middleware chung (ví dụ: webman/cors), hoặc một tập hợp cấu hình định tuyến (như webman/auto-route), hoặc một tiến trình tùy chỉnh (ví dụ: webman/redis-queue) và nhiều hơn nữa.

Để biết thêm chi tiết, vui lòng xem [plugin cơ bản](plugin/base.md)

> **Chú ý**
> Plugin cơ bản yêu cầu webman>=1.2.0

#### Plugin ứng dụng
Plugin ứng dụng là một ứng dụng hoàn chỉnh, như hệ thống hỏi đáp, hệ thống CMS, hệ thống cửa hàng, vv.
Để biết thêm chi tiết, vui lòng xem [plugin ứng dụng](app/app.md)

> **Plugin ứng dụng**
> Plugin ứng dụng yêu cầu webman>=1.4.0
