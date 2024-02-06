Xin chào, dưới đây là các phần chính của tài liệu về Mô hình Liên kết trong Webman:

## 1. Mô hình có nhiều chứa mô hình khác

Nếu bạn muốn một mô hình trong Webman có nhiều mô hình khác, bạn có thể sử dụng phương pháp `hasMany` hoặc `hasOne` để thiết lập mối quan hệ. Ví dụ:

```php
use Workerman\Model;

class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

Hoặc nếu bạn chỉ muốn một mô hình chứa một mô hình khác, bạn có thể sử dụng `hasOne`:

```php
use Workerman\Model;

class User extends Model
{
    public function phone()
    {
        return $this->hasOne(Phone::class);
    }
}
```

## 2. Mô hình thuộc về một mô hình khác

Nếu bạn muốn một mô hình thuộc về một mô hình khác, bạn có thể sử dụng `belongsTo` để thiết lập mối quan hệ. Ví dụ:

```php
use Workerman\Model;

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

## 3. Mô hình có nhiều mô hình thông qua bảng trung gian

Nếu bạn muốn một mô hình có nhiều mô hình thông qua bảng trung gian, bạn có thể sử dụng phương pháp `belongsToMany`. Ví dụ:

```php
use Workerman\Model;

class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
}
```

Nhớ rằng để sử dụng các mối quan hệ này, bạn cần phải đặt tên cột phù hợp với tên mặc định của Webman. Nếu không, bạn có thể cần phải chỉ định tên cột explixitly trong phương thức.

Đây là các điều cơ bản về Mô hình Liên kết trong Webman. Nếu bạn có bất kỳ câu hỏi nào, hãy để lại bình luận.
