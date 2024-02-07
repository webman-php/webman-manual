Xin chào, dưới đây là các hướng dẫn về việc kết nối mô hình trong webman:

## Kết nối mô hình

Trong webman, việc kết nối mô hình có thể được thực hiện thông qua các phương thức của lớp Model, tương tự như trong Laravel. Dưới đây là một số ví dụ về cách thực hiện kết nối mô hình trong webman:

### Một-ngoại
```php
use Workerman\Model\User;
use Workerman\Model\Post;

class User extends \Workerman\Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

### Nhiều-nhiều
```php
use Workerman\Model\User;
use Workerman\Model\Role;

class User extends \Workerman\Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
```

### Ngoại-ngoại
```php
use Workerman\Model\Post;
use Workerman\Model\Comment;

class Post extends \Workerman\Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
```

Để biết thêm thông tin chi tiết, bạn có thể tham khảo tài liệu của Laravel về quan hệ Eloquent từ liên kết sau: [https://learnku.com/docs/laravel/8.x/eloquent-relationships/9407](https://learnku.com/docs/laravel/8.x/eloquent-relationships/9407)
