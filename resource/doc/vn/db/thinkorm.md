## ThinkORM

### Cài đặt ThinkORM

`composer require -W webman/think-orm`

Sau khi cài đặt, cần restart lại (reload không có tác dụng)

> **Lưu ý**
> Nếu cài đặt thất bại, có thể do bạn đang sử dụng proxy của composer, thử chạy `composer config -g --unset repos.packagist` để tắt proxy của composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) thực tế là một plugin tự động cài đặt `toptink/think-orm`, nếu phiên bản webman của bạn thấp hơn `1.2`, không thể sử dụng plugin, vui lòng tham khảo bài viết [Cài đặt và cấu hình think-orm thủ công](https://www.workerman.net/a/1289).

### Tệp cấu hình
Sửa tệp cấu hình theo tình hình cụ thể `config/thinkorm.php`

### Sử dụng

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### Tạo mô hình

Mô hình ThinkOrm kế thừa từ `think\Model`, tương tự như sau
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * Bảng liên kết với mô hình.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Khóa chính liên kết với bảng.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

Bạn cũng có thể sử dụng lệnh sau để tạo mô hình dựa trên thinkorm
```
php webman make:model tên_bảng
```

> **Lưu ý**
> Lệnh này yêu cầu cài đặt `webman/console`, lệnh cài đặt là `composer require webman/console ^1.2.13`

> **Chú ý**
> Nếu lệnh make:model phát hiện dự án chính sử dụng `illuminate/database`, sẽ tạo tệp mô hình dựa trên `illuminate/database` thay vì thinkorm, lúc đó có thể sử dụng một tham số bổ sung tp để bắt buộc tạo mô hình dựa trên think-orm, lệnh tương tự như `php webman make:model tên_bảng tp` (nếu không có tác dụng, vui lòng nâng cấp `webman/console`)
