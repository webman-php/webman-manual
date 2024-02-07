# Bắt đầu nhanh chóng

Mô hình webman dựa trên [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Mỗi bảng cơ sở dữ liệu đều có một "mô hình" tương ứng để tương tác với bảng đó. Bạn có thể truy vấn dữ liệu trong bảng bằng mô hình và chèn bản ghi mới vào bảng.

Trước khi bắt đầu, hãy đảm bảo rằng bạn đã cấu hình kết nối cơ sở dữ liệu trong `config/database.php`.

> Lưu ý: Eloquent ORM cần import bổ sung để hỗ trợ trình quan sát mô hình `composer require "illuminate/events"` [Ví dụ](#điều_kiện_mô_hình)

## Ví dụ
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Tên bảng liên kết với mô hình
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Định nghĩa lại khóa chính, mặc định là id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Chỉ định liệu có tự động duy trì dấu thời gian hay không
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Tên bảng
Bạn có thể chỉ định bảng dữ liệu tùy chỉnh bằng cách định nghĩa thuộc tính bảng trên mô hình:
```php
class User extends Model
{
    /**
     * Tên bảng liên kết với mô hình
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Khóa chính
Eloquent cũng sẽ cho rằng mỗi bảng dữ liệu đều có một cột khóa chính có tên là id. Bạn có thể định nghĩa một thuộc tính bảo vệ $primaryKey để ghi đè quy ước.
```php
class User extends Model
{
    /**
     * Định nghĩa lại khóa chính, mặc định là id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent cho rằng khóa chính là một giá trị số nguyên tăng dần, điều này có nghĩa là mặc định, khóa chính sẽ tự động chuyển đổi thành kiểu int. Nếu bạn muốn sử dụng khóa chính không tăng hoặc không phải số, bạn cần đặt thuộc tính công cộng $incrementing là false:
```php
class User extends Model
{
    /**
     * Chỉ định liệu khóa chính của mô hình có tăng hay không
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Nếu khóa chính của bạn không phải là một số nguyên, bạn cần đặt thuộc tính bảo vệ $keyType trên mô hình thành string:
```php
class User extends Model
{
    /**
     * "Loại" ID tăng tự động.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Dấu thời gian
Mặc định, Eloquent mong đợi rằng bảng dữ liệu của bạn sẽ có created_at và updated_at. Nếu bạn không muốn Eloquent tự động quản lý hai cột này, hãy đặt thuộc tính $timestamps trong mô hình thành false:
```php
class User extends Model
{
    /**
     * Chỉ định liệu có tự động duy trì dấu thời gian hay không
     *
     * @var bool
     */
    public $timestamps = false;
}
```

Nếu bạn cần tùy chỉnh định dạng dấu thời gian, bạn có thể đặt thuộc tính $dateFormat trong mô hình của bạn. Thuộc tính này quyết định cách lưu trữ ngày tháng trong cơ sở dữ liệu và định dạng khi mô hình được serialize thành mảng hoặc JSON:
```php
class User extends Model
{
    /**
     * Định dạng lưu trữ dấu thời gian
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Nếu bạn cần tùy chỉnh tên của trường lưu trữ dấu thời gian, bạn có thể đặt giá trị cho hằng số CREATED_AT và UPDATED_AT trong mô hình:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Kết nối cơ sở dữ liệu
Theo mặc định, mô hình Eloquent sẽ sử dụng kết nối cơ sở dữ liệu mặc định của ứng dụng của bạn. Nếu bạn muốn chỉ định một kết nối khác cho mô hình, hãy đặt thuộc tính $connection:
```php
class User extends Model
{
    /**
     * Tên kết nối của mô hình
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Giá trị mặc định
Nếu bạn muốn xác định giá trị mặc định cho một số thuộc tính của mô hình, bạn có thể định nghĩa thuộc tính $attributes trên mô hình:
```php
class User extends Model
{
    /**
     * Giá trị mặc định của mô hình.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Truy xuất mô hình
Sau khi tạo mô hình và kết nối bảng dữ liệu của nó, bạn có thể truy vấn dữ liệu từ cơ sở dữ liệu. Hãy tưởng tượng mỗi mô hình Eloquent như một trình xây dựng truy vấn mạnh mẽ, bạn có thể sử dụng nó để truy vấn bảng dữ liệu một cách nhanh chóng. Ví dụ:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Gợi ý: Bởi vì mô hình Eloquent cũng là trình xây dựng truy vấn, bạn cũng nên đọc tất cả các phương thức có sẵn trong [trình xây dựng truy vấn](queries.md). Bạn có thể sử dụng các phương thức này trong truy vấn Eloquent.

## Ràng buộc bổ sung
Phương thức all của Eloquent sẽ trả về tất cả các kết quả của mô hình. Vì mỗi mô hình Eloquent đều là một trình xây dựng truy vấn, bạn cũng có thể thêm điều kiện truy vấn, sau đó sử dụng phương thức get để lấy kết quả truy vấn:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Tải lại mô hình
Bạn có thể sử dụng phương thức fresh và refresh để tải lại mô hình. Phương thức fresh sẽ tải lại mô hình từ cơ sở dữ liệu. Các thể hiện mô hình hiện có không bị ảnh hưởng:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

Phương thức refresh sử dụng dữ liệu mới từ cơ sở dữ liệu để làm mới thể hiện mô hình hiện tại. Ngoài ra, các mối quan hệ đã được tải sẽ được tải lại:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Tập hợp
Cả hai phương thức all và get của Eloquent có thể truy vấn nhiều kết quả, trả về một thể hiện `Illuminate\Database\Eloquent\Collection `. Lớp `Collection` cung cấp nhiều phương thức hỗ trợ để xử lý kết quả Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```
## Sử dụng con trỏ
Phương thức cursor cho phép bạn duyệt qua cơ sở dữ liệu bằng con trỏ, nó chỉ thực hiện một lần truy vấn. Khi xử lý một lượng lớn dữ liệu, phương thức cursor có thể giảm thiểu đáng kể lượng bộ nhớ sử dụng:
```php
foreach (app\model\User::where('sex', 1)->cursor() as $user) {
    //
}
```

cursor trả về một trường hợp `Illuminate\Support\LazyCollection`. [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) cho phép bạn sử dụng hầu hết các phương thức tập hợp trong Laravel, và chỉ tải một mô hình duy nhất vào bộ nhớ mỗi lần:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Câu truy vấn con
Eloquent cung cấp hỗ trợ câu truy vấn con cao cấp, bạn có thể trích xuất thông tin từ bảng liên quan trong một câu truy vấn duy nhất. Ví dụ, giả sử chúng ta có một bảng điểm đến destinations và một bảng chuyến bay tới điểm đến flights. Bảng flights chứa một trường arrival_at, biểu thị thời gian chuyến bay đến điểm đến.

Sử dụng phương thức select và addSelect của tính năng câu truy vấn con, chúng ta có thể truy vấn toàn bộ điểm đến destinations cùng với tên chuyến bay cuối cùng đến từng điểm đến:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Sắp xếp theo câu truy vấn con
Ngoài ra, phương thức orderBy của builder cũng hỗ trợ câu truy vấn con. Chúng ta có thể sử dụng tính năng này để sắp xếp tất cả các điểm đến theo thời gian chuyến bay cuối cùng đến điểm đến. Tương tự, điều này chỉ thực hiện một truy vấn duy nhất vào cơ sở dữ liệu:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Truy vấn một mô hình / tập hợp
Ngoài việc trích xuất tất cả các bản ghi từ bảng dữ liệu được chỉ định, bạn có thể sử dụng phương thức find, first hoặc firstWhere để truy vấn một bản ghi duy nhất. Các phương thức này trả về một trường hợp mô hình duy nhất, thay vì trả về một tập hợp mô hình:
```php
// Tìm một mô hình thông qua khóa chính...
$flight = app\model\Flight::find(1);

// Tìm mô hình đầu tiên phù hợp với điều kiện truy vấn...
$flight = app\model\Flight::where('active', 1)->first();

// Tìm mô hình đầu tiên phù hợp với điều kiện truy vấn với cú pháp ngắn gọn...
$flight = app\model\Flight::firstWhere('active', 1);
```

Bạn cũng có thể sử dụng mảng khóa chính làm đối số cho phương thức find, nó sẽ trả về một tập hợp các bản ghi phù hợp:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Đôi khi bạn có thể muốn thực hiện hành động khác khi không tìm thấy kết quả. Phương thức firstOr sẽ trả về kết quả đầu tiên khi tìm thấy kết quả, nếu không, nó sẽ thực thi callback được cung cấp. Giá trị trả về từ callback sẽ được sử dụng làm giá trị trả về của phương thức firstOr:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
Phương thức firstOr cũng chấp nhận mảng trường để truy vấn:
```php
$model = app\model\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## Ngoại lệ "Không tìm thấy"
Đôi khi bạn muốn ném một ngoại lệ khi không tìm thấy mô hình. Điều này rất hữu ích trong controller và route. Phương thức findOrFail và firstOrFail sẽ trả về kết quả truy vấn đầu tiên, nếu không tìm thấy, sẽ ném ra ngoại lệ Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```

## Trích xuất tập hợp
Bạn cũng có thể sử dụng các phương thức count, sum và max của query builder, cũng như các hàm tập hợp khác để thao tác với tập hợp. Những phương thức này chỉ trả về giá trị vô hướng phù hợp thay vì một trường hợp mô hình:
```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## Chèn
Để chèn một bản ghi mới vào cơ sở dữ liệu, bạn cần tạo một trường hợp mô hình mới, thiết lập các thuộc tính cho trường hợp đó, sau đó gọi phương thức save:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Thêm một bản ghi mới vào bảng người dùng
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Xác thực yêu cầu

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

Thời gian tạo và cập nhật sẽ được tự động thiết lập (khi thuộc tính $timestamps trong mô hình là true), không cần phải gán giá trị thủ công.


## Cập nhật
Phương thức save cũng có thể được sử dụng để cập nhật mô hình đã tồn tại trong cơ sở dữ liệu. Để cập nhật mô hình, bạn cần trước tiên trích xuất nó, thiết lập các thuộc tính cần cập nhật, sau đó gọi phương thức save. Tương tự, thời gian cập nhật sẽ tự động thay đổi, do đó cũng không cần phải gán giá trị thủ công:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Cập nhật hàng loạt
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```
## Kiểm tra thay đổi thuộc tính
Eloquent cung cấp các phương thức isDirty, isClean và wasChanged để kiểm tra trạng thái nội bộ của mô hình và xác định cách thuộc tính đã thay đổi kể từ khi ban đầu tải.
Phương thức isDirty xác định xem bất kỳ thuộc tính nào đã được thay đổi kể từ khi mô hình được tải. Bạn có thể truyền tên thuộc tính cụ thể để xác định xem một thuộc tính cụ thể đã bẩn hay không. Phương thức isClean ngược lại với isDirty, nó cũng chấp nhận tham số thuộc tính tùy chọn:
```php
$user = User::create([
'first_name' => 'Taylor',
'last_name' => 'Otwell',
'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
phương thức wasChanged xác định xem một thuộc tính nào đó đã được thay đổi khi lần cuối cùng mô hình được lưu trong chu kỳ yêu cầu hiện tại. Bạn cũng có thể truyền tên thuộc tính để xem xem một thuộc tính cụ thể đã thay đổi hay chưa:
```php
$user = User::create([
'first_name' => 'Taylor',
'last_name' => 'Otwell',
'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```
## Gán giá trị theo lô
Bạn cũng có thể sử dụng phương thức create để lưu trữ mô hình mới. Phương thức này sẽ trả về một thực thể mô hình. Tuy nhiên, trước khi sử dụng, bạn cần chỉ định thuộc tính fillable hoặc guarded trên mô hình, vì tất cả các mô hình Eloquent mặc định đều không cho phép gán giá trị theo lô.
Khi người dùng truyền các tham số HTTP không mong muốn và thay đổi các trường không cần thiết trong cơ sở dữ liệu, lỗ hổng gán giá trị theo lô sẽ xảy ra. Ví dụ: Người dùng xấu có thể thông qua yêu cầu HTTP truyền tham số is_admin, và sau đó chuyển nó cho phương thức create, điều này cho phép người dùng nâng cấp mình thành quản trị viên.
Vì vậy, trước khi bắt đầu, bạn nên xác định rõ các thuộc tính của mô hình có thể được gán giá trị theo lô. Bạn có thể thực hiện điều này thông qua thuộc tính $fillable trên mô hình. Ví dụ: Làm cho thuộc tính name của mô hình Flight có thể được gán giá trị theo lô:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
/**
* Các thuộc tính có thể được gán giá trị theo lô.
*
* @var array
*/
protected $fillable = ['name'];
}

```
Khi chúng ta đã xác định được những thuộc tính có thể được gán giá trị theo lô, bạn có thể sử dụng phương thức create để chèn dữ liệu mới vào cơ sở dữ liệu. Phương thức create sẽ trả về một thực thể mô hình đã lưu:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
Nếu bạn đã có một thực thể mô hình, bạn có thể chuyển một mảng cho phương thức fill để gán giá trị:
```php
$flight->fill(['name' => 'Flight 22']);
```
$fillable có thể được coi là một "danh sách trắng" cho việc gán giá trị theo lô, bạn cũng có thể sử dụng $guarded để thực hiện. Thuộc tính $guarded bao gồm một mảng không được phép gán giá trị theo lô. Nghĩa là, $guarded từ mặt chức năng sẽ giống như một "danh sách đen". Chú ý: bạn chỉ có thể sử dụng $fillable hoặc $guarded, không thể sử dụng cả hai đồng thời. Trong ví dụ sau, ngoại trừ thuộc tính giá ngoài ra, tất cả các thuộc tính khác đều có thể được gán giá trị theo lô:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
/**
* Các thuộc tính không được gán giá trị theo lô.
*
* @var array
*/
protected $guarded = ['price'];
}
```
Nếu bạn muốn tất cả các thuộc tính đều có thể được gán giá trị theo lô, bạn có thể định nghĩa $guarded như một mảng trống:
```php
/**
* Các thuộc tính không được gán giá trị theo lô.
*
* @var array
*/
protected $guarded = [];
```
## Các phương thức tạo khác
firstOrCreate/ firstOrNew
Ở đây có hai phương thức mà bạn có thể sử dụng để gán giá trị theo lô: firstOrCreate và firstOrNew. Phương thức firstOrCreate sẽ tìm kiếm bản ghi trong cơ sở dữ liệu thông qua cặp khóa/giá trị đã cung cấp. Nếu không tìm thấy mô hình trong cơ sở dữ liệu, nó sẽ chèn một bản ghi chứa các thuộc tính của tham số thứ nhất và các thuộc tính tùy chọn của tham số thứ hai.
Phương thức firstOrNew tương tự như phương thức firstOrCreate khi cố gắng tìm kiếm bản ghi của cơ sở dữ liệu thông qua các thuộc tính đã cung cấp. Tuy nhiên, nếu phương thức firstOrNew không tìm thấy mô hình tương ứng, nó sẽ trả về một thực thể mô hình mới. Lưu ý rằng thực thể mô hình được trả về bởi firstOrNew chưa được lưu vào cơ sở dữ liệu, bạn cần gọi phương thức save thủ công để lưu:
```php
// Tìm kiếm chuyến bay qua name, nếu không tồn tại thì tạo mới...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Tìm kiếm chuyến bay thông qua name, hoặc tạo mới với name và thuộc tính delayed và arrival_time tùy chọn...
$flight = app\modle\Flight::firstOrCreate(
['name' => 'Flight 10'],
['delayed' => 1, 'arrival_time' => '11:30']
);

// Tìm kiếm chuyến bay thông qua name, nếu không tồn tại thì tạo một thực thể mới...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Tìm kiếm chuyến bay qua name, hoặc tạo một thực thể mới với name và thuộc tính delayed và arrival_time tùy chọn...
$flight = app\modle\Flight::firstOrNew(
['name' => 'Flight 10'],
['delayed' => 1, 'arrival_time' => '11:30']
);
```
Bạn cũng có thể gặp trường hợp muốn cập nhật mô hình hiện có hoặc tạo mô hình mới nếu chưa tồn tại. Bạn có thể sử dụng phương thức updateOrCreate để thực hiện điều này trong một bước. Tương tự như phương thức firstOrCreate, updateOrCreate sẽ ghi nhớ mô hình, do đó bạn không cần gọi save():
```php
// Nếu có chuyến bay từ Oakland đến San Diego, giá định là 99 đô la.
// Nếu không tìm thấy mô hình tồn tại, thì tạo một mô hình mới.
$flight = app\modle\Flight::updateOrCreate(
['departure' => 'Oakland', 'destination' => 'San Diego'],
['price' => 99, 'discounted' => 1]
);
```
## Xóa mô hình
Bạn có thể gọi phương thức delete trên thực thể mô hình để xóa thực thể:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```
## Xóa mô hình theo khóa chính
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```
## Xóa mô hình thông qua truy vấn
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Sao chép mô hình
Bạn có thể sử dụng phương thức replicate để sao chép một phiên bản mới chưa lưu vào cơ sở dữ liệu. Khi các thông số của phiên bản mô hình được chia sẻ, phương thức này rất hữu ích.
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();
```

## So sánh mô hình
Đôi khi bạn có thể cần xác định xem hai mô hình có "giống nhau" hay không. Phương thức is có thể được sử dụng để kiểm tra nhanh xem hai mô hình có cùng khóa chính, bảng và kết nối cơ sở dữ liệu không:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Người quan sát mô hình
Sử dụng tham khảo [Sự kiện mô hình và Người quan sát trong Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Chú ý: Eloquent ORM cần hỗ trợ người quan sát mô hình, bạn cần phải nhập thêm bằng cách sử dụng composer require "illuminate/events".
```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```
