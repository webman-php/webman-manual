# Trình xây dựng truy vấn
## Lấy tất cả các dòng
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function all(Request $request)
    {
        $users = Db::table('users')->get();
        return view('user/all', ['users' => $users]);
    }
}
```

## Lấy cột cụ thể
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Lấy 1 dòng
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Lấy 1 cột
```php
$titles = Db::table('roles')->pluck('title');
```
Chỉ định giá trị của trường id làm chỉ mục
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Lấy giá trị duy nhất(một trường)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Loại bỏ trùng lặp
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Phân tích kết quả
Nếu bạn cần xử lý hàng ngàn bản ghi trong cơ sở dữ liệu, việc đọc tất cả các dữ liệu này một lần sẽ tốn thời gian và dễ dẫn đến việc vượt quá bộ nhớ, lúc này bạn có thể xem xét việc sử dụng phương thức chunkById. Phương pháp này sẽ lấy một phần nhỏ của tập kết quả và truyền nó cho hàm đóng để xử lý. Ví dụ, chúng ta có thể chia tất cả dữ liệu bảng users thành các khối nhỏ mỗi lần xử lý 100 bản ghi:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Bạn có thể dừng việc lấy kết quả theo phần bằng cách trả về false trong hàm đóng.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Xử lý các bản ghi...

    return false;
});
```

> Lưu ý: Không nên xóa dữ liệu trong hàm gọi lại, điều này có thể dẫn đến một số bản ghi không được bao gồm trong kết quả

## Tích hợp
Trình xây dựng truy vấn cũng cung cấp các phương thức tích hợp khác nhau, chẳng hạn như đếm, max, min, avg, sum và ctd.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Kiểm tra xem bản ghi có tồn tại không
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Biểu thức nguyên thủy
Prototype
```php
selectRaw($expression, $bindings = [])
```
Đôi khi bạn có thể cần sử dụng biểu thức nguyên thủy trong truy vấn. Bạn có thể sử dụng `selectRaw()` để tạo một biểu thức nguyên thủy:

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

Tương tự, còn có `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` các phương thức biểu thức nguyên thủy.

`Db::raw($value)` cũng được sử dụng để tạo một biểu thức nguyên thủy, nhưng nó không có chức năng ràng buộc tham số, vì vậy khi sử dụng cần phải cẩn thận với vấn đề SQL injection.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```

## Mệnh đề Join
```php
// join
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// leftJoin            
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// rightJoin
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// crossJoin    
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

## Mệnh đề Union
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Mệnh đề Where
Prototype 
```php
where($column, $operator = null, $value = null)
```
Tham số đầu tiên là tên cột, tham số thứ hai là bất kỳ toán tử nào được hệ thống cơ sở dữ liệu hỗ trợ, tham số thứ ba là giá trị mà cột đó sẽ so sánh
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Khi toán tử là dấu bằng, bạn có thể bỏ qua, vì vậy câu lệnh này có tác dụng tương tự
$users = Db::table('users')->where('votes', 100)->get();

$users = Db::table('users')
                ->where('votes', '>=', 100)
                ->get();

$users = Db::table('users')
                ->where('votes', '<>', 100)
                ->get();

$users = Db::table('users')
                ->where('name', 'like', 'T%')
                ->get();
```

Bạn cũng có thể chuyển mảng điều kiện vào hàm where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

Phương thức orWhere và phương thức where nhận các tham số giống nhau:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Bạn có thể truyền một closure vào phương thức orWhere làm tham số đầu tiên:
```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$users = Db::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();

```

whereBetween / orWhereBetween phương thức xác nhận giá trị của trường có nằm giữa hai giá trị đưa vào:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween phương thức xác nhận giá trị của trường không nằm giữa hai giá trị đưa vào:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn phương thức xác nhận giá trị của trường phải tồn tại trong mảng đã chỉ định:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull phương thức xác nhận trường được chỉ định phải là NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull phương thức xác nhận trường được chỉ định không phải là NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime phương thức để so sánh giá trị trường với ngày đã cho:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn phương thức để so sánh giá trị của hai trường có bằng nhau không:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Bạn cũng có thể truyền một toán tử so sánh
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// Phương thức whereColumn cũng có thể truyền mảng vào
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Nhóm tham số
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
               $query->where('votes', '>', 100)
                     ->orWhere('title', '=', 'Admin');
           })
           ->get();
```

whereExists
```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$users = Db::table('users')
           ->whereExists(function ($query) {
               $query->select(Db::raw(1))
                     ->from('orders')
                     ->whereRaw('orders.user_id = users.id');
           })
           ->get();
```

## orderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Sắp xếp ngẫu nhiên
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Sắp xếp ngẫu nhiên sẽ ảnh hưởng đến hiệu suất máy chủ, không khuyến nghị sử dụng

## Nhóm theo / có
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Bạn có thể truyền nhiều tham số vào phương thức groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## offset / limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```
## Chèn
Chèn một bản ghi
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Chèn nhiều bản ghi
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Tự tăng ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Lưu ý: Khi sử dụng PostgreSQL, phương thức insertGetId mặc định sẽ sử dụng id làm tên trường tự động tăng. Nếu bạn muốn lấy ID từ một "chuỗi" khác, bạn có thể truyền tên trường như là tham số thứ hai cho phương thức insertGetId.

## Cập nhật
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Cập nhật hoặc chèn mới
Đôi khi bạn có thể muốn cập nhật bản ghi tồn tại trong cơ sở dữ liệu hoặc tạo mới nếu không có bản ghi phù hợp:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Phương thức updateOrInsert sẽ đầu tiên thử tìm bản ghi cơ sở dữ liệu phù hợp với cặp khóa và giá trị từ tham số đầu tiên. Nếu bản ghi tồn tại, nó sẽ cập nhật bản ghi bằng giá trị từ tham số thứ hai. Nếu không tìm thấy bản ghi, nó sẽ chèn một bản ghi mới với dữ liệu từ hai mảng.

## Tăng & Giảm
Cả hai phương thức đều ít nhất nhận một tham số: cột cần thay đổi. Tham số thứ hai là tùy chọn và dùng để kiểm soát sự tăng hoặc giảm của cột:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Bạn cũng có thể chỉ định cột để cập nhật trong quá trình thực hiện phép tăng hoặc giảm:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Xóa
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Nếu bạn cần xóa toàn bộ bảng, bạn có thể sử dụng phương thức truncate, nó sẽ xóa tất cả các dòng và đặt lại ID tự tăng về 0:
```php
Db::table('users')->truncate();
```

## Khóa bi quan
Cơ sở dữ liệu truy vấn cũng bao gồm một số phương thức giúp bạn áp dụng "khóa bi quan" trong cú pháp chọn. Nếu bạn muốn thực hiện "khóa chia sẻ" trong truy vấn, bạn có thể sử dụng phương thức sharedLock. Khóa chia sẻ ngăn việc các cột dữ liệu được chọn bị thay đổi cho đến khi giao dịch được xác nhận:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Hoặc bạn có thể sử dụng phương thức lockForUpdate. Sử dụng khóa "cập nhật" giúp tránh việc các hàng bị sửa đổi hoặc chọn bởi các khóa chia sẻ khác:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```


## Gỡ lỗi
Bạn có thể sử dụng phương thức dd hoặc dump để xuất kết quả truy vấn hoặc câu lệnh SQL. Sử dụng phương thức dd sẽ hiển thị thông tin gỡ lỗi và dừng thực hiện yêu cầu. Phương thức dump cũng có thể hiển thị thông tin gỡ lỗi nhưng không dừng thực hiện yêu cầu:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Lưu ý**
> Để sử dụng gỡ lỗi, bạn cần cài đặt `symfony/var-dumper`, lệnh là `composer require symfony/var-dumper`
