# Trình xây dựng truy vấn
## Lấy tất cả các hàng
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

## Lấy một hàng
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Lấy một cột
```php
$titles = Db::table('roles')->pluck('title');
```
Chọn giá trị của trường id làm chỉ mục
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Lấy giá trị đơn (cột)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Loại bỏ bản ghi trùng lặp
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Phân chia kết quả thành các khối
Nếu bạn cần xử lý hàng ngàn bản ghi trong cơ sở dữ liệu, việc đọc tất cả dữ liệu một lần sẽ tốn nhiều thời gian và dễ dẫn đến việc vượt quá bộ nhớ, trong trường hợp này, bạn có thể xem xét việc sử dụng phương thức `chunkById`. Phương thức này sẽ lấy một phần nhỏ của tập kết quả và chuyển nó vào hàm `closure` cho xử lý. Ví dụ, chúng ta có thể chia toàn bộ dữ liệu bảng `users` thành các khối xử lý 100 bản ghi một lần:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Bạn có thể dừng việc lấy kết quả theo từng khối bằng cách trả về false trong `closure`.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Xử lý các bản ghi...

    return false;
});
```

> Lưu ý: Đừng xóa dữ liệu trong hàm `closure`, điều này có thể dẫn đến một số bản ghi không được bao gồm trong tập kết quả.

## Tích chất

Trình xây dựng truy vấn cũng cung cấp các phương thức tích hợp khác nhau như count, max, min, avg, sum, v.v.
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
Mẫu
```php
selectRaw($expression, $bindings = [])
```
Đôi khi bạn có thể cần sử dụng biểu thức nguyên thủy trong truy vấn. Bạn có thể sử dụng `selectRaw()` để tạo một biểu thức nguyên thủy:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

Tương tự, cũng cung cấp các phương thức biểu thức nguyên thủy `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()`.

`Db::raw($value)` cũng được sử dụng để tạo một biểu thức nguyên thủy, nhưng nó không có tính năng ràng buộc tham số, vui lòng sử dụng cẩn thận để tránh vấn đề tấn công SQL.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Câu lệnh Join
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

## Câu lệnh Union
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Câu lệnh Where
Cú pháp
```php
where($column, $operator = null, $value = null)
```
Tham số đầu tiên là tên cột, tham số thứ hai là bất kỳ toán tử nào được hỗ trợ bởi hệ thống cơ sở dữ liệu, tham số thứ ba là giá trị cần so sánh với cột đó.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Khi toán tử là bằng có thể được lược bỏ, vì vậy câu lệnh này tương đương với câu lệnh trước
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

Bạn cũng có thể truyền một mảng các điều kiện vào hàm where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

Phương thức orWhere và where nhận các tham số giống nhau:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Bạn có thể truyền một closure cho phương thức orWhere như là tham số đầu tiên:
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

Phương thức whereBetween / orWhereBetween kiểm tra xem giá trị cột có nằm giữa hai giá trị đã cho không:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Phương thức whereNotBetween / orWhereNotBetween kiểm tra xem giá trị cột có không nằm giữa hai giá trị đã cho không:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Phương thức whereIn / whereNotIn / orWhereIn / orWhereNotIn kiểm tra xem giá trị cột phải tồn tại trong mảng đã cho hay không:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

Phương thức whereNull / whereNotNull / orWhereNull / orWhereNotNull kiểm tra xem giá trị của cột phải là NULL hay không:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

Phương thức whereNotNull kiểm tra xem giá trị cột phải không phải là NULL hay không:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

Phương thức whereDate / whereMonth / whereDay / whereYear / whereTime được sử dụng để so sánh giá trị cột với ngày đã cho:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

Phương thức whereColumn / orWhereColumn được sử dụng để so sánh xem hai giá trị cột có bằng nhau không:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Bạn cũng có thể truyền một toán tử so sánh
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// Phương thức whereColumn cũng có thể truyền một mảng
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

WhereExists
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

## Sắp xếp theo
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
> Sắp xếp ngẫu nhiên có thể ảnh hưởng đến hiệu suất của máy chủ, không khuyến nghị sử dụng

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Bạn có thể truyền nhiều tham số cho phương thức groupBy
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

## Tăng ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Lưu ý: Khi sử dụng PostgreSQL, phương thức insertGetId mặc định sẽ lấy id làm tên trường tự tăng. Nếu bạn muốn lấy ID từ một "chuỗi" khác, bạn có thể truyền tên trường như là tham số thứ hai vào phương thức insertGetId.

## Cập nhật
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```
## Cập nhật hoặc Thêm mới
Đôi khi bạn có thể muốn cập nhật bản ghi hiện có trong cơ sở dữ liệu, hoặc tạo mới nếu không có bản ghi phù hợp:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Phương thức updateOrInsert sẽ cố gắng tìm bản ghi cơ sở dữ liệu phù hợp với cặp khóa và giá trị từ tham số đầu tiên. Nếu bản ghi tồn tại, nó sẽ cập nhật bản ghi với các giá trị từ tham số thứ hai. Nếu không tìm thấy bản ghi, nó sẽ chèn một bản ghi mới với dữ liệu từ hai mảng.

## Tăng & Giảm
Cả hai phương thức này đều nhận ít nhất một tham số: cột cần thay đổi. Tham số thứ hai là tùy chọn, để điều khiển lượng tăng hoặc giảm của cột:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Bạn cũng có thể chỉ định trường cần cập nhật trong quá trình thực hiện thao tác:
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

## Khóa Pessimistic
Trình xây dựng truy vấn cũng bao gồm một số phương thức giúp bạn thực hiện "khóa pessimistic" trên cú pháp select. Nếu bạn muốn thực hiện một "khóa chia sẻ" trong truy vấn, bạn có thể sử dụng phương thức sharedLock. Khóa chia sẻ ngăn cản các cột dữ liệu được chọn bị thay đổi cho đến khi giao dịch được xác nhận:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Hoặc, bạn có thể sử dụng phương thức lockForUpdate. Sử dụng khóa "update" tránh các hàng bị chỉnh sửa hoặc chọn bởi các khóa chia sẻ khác:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Gỡ lỗi
Bạn có thể sử dụng phương thức dd hoặc dump để in kết quả truy vấn hoặc câu lệnh SQL. Sử dụng phương thức dd sẽ hiển thị thông tin gỡ lỗi và dừng thực hiện yêu cầu. Phương thức dump cũng có thể hiển thị thông tin gỡ lỗi nhưng không dừng thực hiện yêu cầu:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Lưu ý**
> Gỡ lỗi yêu cầu cài đặt `symfony/var-dumper`, lệnh là `composer require symfony/var-dumper`
