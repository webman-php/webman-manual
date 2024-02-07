# Co-routine

> **Yêu cầu về Coroutine**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Lệnh nâng cấp webman `composer require workerman/webman-framework ^1.5.0`
> Lệnh nâng cấp workerman `composer require workerman/workerman ^5.0.0`
> Cần cài đặt Fiber Coroutine `composer require revolt/event-loop ^1.0.0`

# Ví dụ
### Trả lời trễ

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Ngủ 1.5 giây
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` tương tự như hàm `sleep()` trong PHP, khác biệt là `Timer::sleep()` không chặn tiến trình.

### Gửi yêu cầu HTTP

> **Chú ý**
> Cần cài đặt composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // Gửi yêu cầu bất đồng bộ bằng cách đồng bộ hóa phương thức
        return $response->getBody()->getContents();
    }
}
```
Tương tự `$client->get('http://example.com')` là không chặn đóng, điều này có thể được sử dụng để gửi yêu cầu http không chặn trong webman, nâng cao hiệu suất ứng dụng.

Xem thêm tại [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Thêm lớp support\Context

Lớp `support\Context` được sử dụng để lưu trữ dữ liệu ngữ cảnh yêu cầu; khi yêu cầu hoàn thành, các dữ liệu ngữ cảnh tương ứng sẽ tự động bị xóa. 

Nói cách khác, chu kỳ sống của dữ liệu ngữ cảnh là theo chu kỳ sống của yêu cầu. `support\Context` hỗ trợ môi trường Coroutine, Swoole và Swow.

### Coroutine Swoole
Sau khi cài đặt ứng dụng mở rộng Swoole (yêu cầu Swoole>=5.0), bật coroutine Swoole thông qua việc cấu hình trong config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Xem thêm tại [workerman sự kiện động cơ](https://www.workerman.net/doc/workerman/appendices/event.html)

### Ô nhiễm biến toàn cục

Môi trường coroutine cấm lưu trữ thông tin trạng thái **liên quan đến yêu cầu** trong biến toàn cục hoặc biến tĩnh, vì điều này có thể dẫn đến ô nhiễm biến toàn cục, ví dụ:

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

Đặt số tiến trình là 1, khi chúng ta gửi liên tiếp hai yêu cầu  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
Chúng ta mong đợi kết quả của hai yêu cầu lần lượt là `lilei` và `hanmeimei`, nhưng thực tế trả về là `hanmeimei`.
Điều này xảy ra vì yêu cầu thứ hai đã ghi đè biến tĩnh`$name`, khi yêu cầu đầu tiên kết thúc khi tác vụ ngủ đã kết thúc, giá trị biến tĩnh`$name` đã trở thành `hanmeimei`.

**Phương pháp chính xác là sử dụng ngữ cảnh để lưu trữ dữ liệu trạng thái yêu cầu**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**Biến cục bộ không gây ra ô nhiễm dữ liệu**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
Vì `$name` là biến cục bộ, các coroutine không thể truy cập vào biến cục bộ của nhau, nên việc sử dụng biến cục bộ là an toàn trong môi trường coroutine.

# Về Coroutine
Coroutine không phải là giải pháp tự nhiên, việc sử dụng Coroutine đòi hỏi chú ý đến vấn đề ô nhiễm biến toàn cục/biến tĩnh và cần thiết lập ngữ cảnh. Ngoài ra, gỡ lỗi lỗi trong môi trường coroutine cũng phức tạp hơn so với lập trình chặn.

Lập trình chặn trong webman thực sự đã đủ nhanh, theo dữ liệu kiểm tra từ [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) từ ba vòng thi đấu gần đây, lập trình chặn trong webman mang theo dịch vụ cơ sở dữ liệu có hiệu suất gần 1 lần cao hơn so với framework web go gin, echo,ldbừng truyền thống laravel có hiệu suất cao hơn gần 40 lần.
![](../../assets/img/benchemarks-go-sw.png?)

Khi cả cơ sở dữ liệu và redis nằm trong mạng nội bộ, hiệu suất lập trình chặn nhiều tiến trình có thể thường cao hơn coroutine, điều này là do khi cơ sở dữ liệu và redis đủ nhanh, chi phí tạo, lên lịch, và hủy bỏ của coroutine có thể lớn hơn chi phí chuyển đổi tiến trình, do đó khi giới thiệu coroutine có thể không cải thiện rõ rệt hiệu suất.

# Khi nào sử dụng Coroutine
Khi có truy cập chậm trong hoạt động kinh doanh, ví dụ: cần truy cập API của bên thứ ba, có thể sử dụng [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) để chuyển đổi yêu cầu HTTP bất đồng bộ theo cách coroutine, để nâng cao khả năng đồng thời của ứng dụng.
