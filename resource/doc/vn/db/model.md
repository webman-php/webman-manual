# Bắt đầu nhanh chóng

Mô hình webman dựa trên [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Mỗi bảng cơ sở dữ liệu đều có một "mô hình" tương ứng để tương tác với bảng đó. Bạn có thể truy vấn dữ liệu trong bảng bằng mô hình và chèn bản ghi mới vào bảng.

Trước khi bắt đầu, hãy đảm bảo rằng bạn đã cấu hình kết nối cơ sở dữ liệu trong `config/database.php`.

> Lưu ý: Để hỗ trợ quan sát mô hình, Eloquent ORM cần phải nhập thêm `composer require "illuminate/events"` [Ví dụ](#mô-hình-quan-sát)

## Ví dụ
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Tên bảng mà mô hình liên kết
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
     * Cho biết liệu có tự động duy trì dấu thời gian hay không
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Tên bảng
Bạn có thể chỉ định bảng dữ liệu tùy chỉnh bằng cách xác định thuộc tính bảng trong mô hình:
```php
class User extends Model
{
    /**
     * Tên bảng mà mô hình liên kết
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Khóa chính
Eloquent cũng sẽ giả định rằng mỗi bảng dữ liệu đều có một cột khóa chính mang tên là id. Bạn có thể xác định một thuộc tính bảo vệ $primaryKey để ghi đè lên quy ước:
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

Eloquent giả định rằng khóa chính là một giá trị số nguyên tự tăng, điều này có nghĩa là mặc định khóa chính sẽ tự động chuyển đổi thành kiểu int. Nếu bạn muốn sử dụng khóa chính không tăng hoặc không phải là số, bạn cần đặt thuộc tính công cộng $incrementing thành false:
```php
class User extends Model
{
    /**
     * Chỉ định khóa chính của mô hình có phải tăng hay không
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Nếu khóa chính của bạn không phải là một số nguyên, bạn cần đặt thuộc tính bảo vệ $keyType trên mô hình thành chuỗi:
```php
class User extends Model
{
    /**
     * "Loại" ID tự động tăng
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Dấu thời gian
Mặc định, Eloquent mong đợi bảng dữ liệu của bạn có sẵn created_at và updated_at. Nếu bạn không muốn Eloquent quản lý tự động hai cột này, hãy đặt thuộc tính $timestamps trên mô hình thành false:
```php
class User extends Model
{
    /**
     * Cho biết liệu có tự động duy trì dấu thời gian hay không
     *
     * @var bool
     */
    public $timestamps = false;
}
```
Nếu bạn cần tùy chỉnh định dạng dấu thời gian, bạn có thể đặt thuộc tính $dateFormat trong mô hình của bạn. Thuộc tính này quy định cách lưu trữ thuộc tính ngày tháng trong cơ sở dữ liệu và cách mô hình được serialize thành mảng hoặc JSON:
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

Nếu bạn cần tùy chỉnh tên các cột để lưu trữ dấu thời gian, bạn có thể đặt giá trị của các hằng số CREATED_AT và UPDATED_AT trong mô hình:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Kết nối cơ sở dữ liệu
Mặc định, mô hình Eloquent sẽ sử dụng kết nối cơ sở dữ liệu mặc định của ứng dụng của bạn. Nếu bạn muốn chỉ định một kết nối khác cho mô hình, hãy đặt thuộc tính $connection:
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
     * Giá trị mặc định của mô hình
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Truy xuất mô hình
Sau khi tạo mô hình và bảng dữ liệu liên quan, bạn có thể truy vấn dữ liệu từ cơ sở dữ liệu. Hãy tưởng tượng mỗi mô hình Eloquent như một query builder mạnh mẽ, bạn có thể sử dụng nó để truy vấn nhanh hơn dữ liệu liên quan đến bảng. Ví dụ:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Lưu ý: Vì mô hình Eloquent cũng là query builder, bạn cũng nên đọc qua [query builder](queries.md) để biết tất cả các phương thức có sẵn. Bạn có thể sử dụng các phương thức này trong truy vấn Eloquent.

## Ràng buộc bổ sung
Phương thức all của Eloquent sẽ trả về tất cả các kết quả trong mô hình. Do mỗi mô hình Eloquent đều là query builder, bạn cũng có thể thêm điều kiện truy vấn, sau đó sử dụng phương thức get để lấy kết quả truy vấn:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Tải lại mô hình
Bạn có thể sử dụng phương thức fresh và refresh để tải lại mô hình. Phương thức fresh sẽ tải lại mô hình từ cơ sở dữ liệu. Thực thể mô hình hiện có không bị ảnh hưởng:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

Phương thức refresh sử dụng dữ liệu mới từ cơ sở dữ liệu để gán lại mô hình hiện tại. Ngoài ra, mối quan hệ đã được tải sẽ được tải lại:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Bộ sưu tập
Cả hai phương thức all và get của Eloquent đều có thể truy vấn nhiều kết quả, trả về một thực thể `Illuminate\Database\Eloquent\Collection`. Lớp `Collection` cung cấp rất nhiều phương thức hỗ trợ để xử lý kết quả Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Sử dụng con trỏ
Phương thức cursor cho phép bạn duyệt qua cơ sở dữ liệu bằng con trỏ, nó chỉ thực hiện một truy vấn. Khi xử lý một lượng lớn dữ liệu, phương thức cursor có thể giảm thiểu đáng kể việc sử dụng bộ nhớ:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

phương thức cursor trả về một thực thể `Illuminate\Support\LazyCollection`. [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) cho phép bạn sử dụng hầu hết các phương thức trong bộ sưu tập Laravel, và chỉ tải một thực thể vào bộ nhớ mỗi lần:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Các câu truy vấn con
Eloquent cung cấp hỗ trợ câu truy vấn con nâng cao, bạn có thể sử dụng một câu truy vấn đơn để trích xuất thông tin từ bảng liên quan. Ví dụ, giả sử chúng ta có một bảng destinations và một bảng flights đi đến các điểm đến. Bảng flights chứa một trường arrival_at, biểu thị khi chuyến bay đến đích.

Sử dụng các phương thức select và addSelect từ tính năng câu truy vấn con, chúng ta có thể truy vấn tất cả các điểm đến destinations trong một câu lệnh duy nhất, cũng như tên chuyến bay cuối cùng đến từng điểm đến:
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
Ngoài ra, hàm orderBy của tạo truy vấn cũng hỗ trợ câu truy vấn con. Chúng ta có thể sử dụng tính năng này để sắp xếp tất cả các điểm đến theo thời gian cuối cùng chuyến bay đến đích. Tương tự, điều này có thể chỉ thực hiện một truy vấn cơ sở dữ liệu duy nhất:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Truy vấn một mô hình / bộ sưu tập
Ngoài việc trích xuất tất cả các bản ghi từ bảng chỉ định, bạn cũng có thể sử dụng phương thức find, first hoặc firstWhere để truy vấn một bản ghi duy nhất. Những phương thức này trả về một thực thể mô hình duy nhất thay vì trả về bộ sưu tập mô hình:
```php
// Tìm một mô hình theo khóa chính...
$flight = app\model\Flight::find(1);

// Tìm một mô hình đầu tiên phù hợp với điều kiện truy vấn...
$flight = app\model\Flight::where('active', 1)->first();

// Tìm một mô hình đầu tiên phù hợp với điều kiện truy vấn một cách nhanh chóng...
$flight = app\model\Flight::firstWhere('active', 1);
```

Bạn cũng có thể sử dụng mảng các khóa chính làm đối số cho phương thức find, nó sẽ trả về bộ sưu tập các bản ghi phù hợp:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Đôi khi bạn có thể muốn thực hiện các hành động khác khi không tìm thấy kết quả khi tìm kiếm kết quả đầu tiên. Phương thức firstOr sẽ trả về kết quả đầu tiên khi tìm thấy kết quả, nếu không, nó sẽ thực hiện một callback được cung cấp. Kết quả trả về từ callback sẽ được trả về từ phương thức firstOr:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
Phương thức firstOr cũng chấp nhận mảng cột để truy vấn:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## Ngoại lệ "không tìm thấy"
Đôi khi bạn muốn ném ngoại lệ khi không tìm thấy mô hình. Điều này rất hữu ích trong controller và đường dẫn. Phương thức findOrFail và firstOrFail sẽ truy vấn kết quả đầu tiên được tìm thấy, nếu không tìm thấy, sẽ ném ra ngoại lệ Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## Tập hợp truy vấn
Bạn cũng có thể sử dụng các phương thức count, sum và max cung cấp bởi Query Builder và các hàm tập hợp khác để thao tác trên các tập hợp. Những phương thức này chỉ trả về giá trị nguyên thủy thích hợp thay vì một thực thể mẫu:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Chèn
Để thêm một bản ghi mới vào cơ sở dữ liệu, trước tiên hãy tạo một thực thể mô hình mới, đặt các thuộc tính cho thực thể đó, sau đó gọi phương thức save:
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

Thời gian tạo và cập nhật (created_at và updated_at) sẽ tự động được thiết lập (khi thuộc tính $timestamps trong mô hình là true), không cần phải gán giá trị thủ công.

## Cập nhật
Phương thức save cũng có thể được sử dụng để cập nhật mô hình đã tồn tại trong cơ sở dữ liệu. Để cập nhật mô hình, bạn cần truy xuất mô hình đó, thiết lập các thuộc tính muốn cập nhật, sau đó gọi phương thức save. Tương tự, thời gian cập nhật (updated_at) cũng sẽ tự động cập nhật, do đó không cần phải gán giá trị thủ công:
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

## Kiểm tra sự thay đổi thuộc tính
Eloquent cung cấp các phương thức isDirty, isClean và wasChanged để kiểm tra trạng thái nội tại của mô hình và xác định cách thuộc tính có thay đổi từ lúc tải ban đầu. Phương thức isDirty xác định xem bất kỳ thuộc tính nào đã được thay đổi sau khi tải mô hình. Bạn có thể truyền tên thuộc tính cụ thể để xác định xem thuộc tính cụ thể có bẩn hay không. Phương thức isClean ngược lại với isDirty, nó cũng chấp nhận tham số thuộc tính tùy chọn:
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
Phương thức wasChanged xác định xem các thuộc tính đã được thay đổi từ lần lưu mô hình cuối cùng trong chu kỳ yêu cầu hiện tại. Bạn cũng có thể truyền tên thuộc tính để xem xem thuộc tính cụ thể đã bị thay đổi hay chưa:
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

## Gán hàng loạt
Bạn cũng có thể sử dụng phương thức create để lưu mô hình mới. Phương thức này sẽ trả về một thực thể mô hình. Tuy nhiên, trước khi sử dụng, bạn cần chỉ định thuộc tính fillable hoặc guarded trên mô hình, vì tất cả các mô hình Eloquent mặc định không thể được gán hàng loạt.

Khi người dùng chuyển đến các tham số HTTP không mong muốn và thay đổi các trường trong cơ sở dữ liệu mà bạn không muốn thay đổi khiến cho nảy sinh lỗ hổng gán hàng loạt. Ví dụ: người dùng xấu có thể chuyển tham số HTTP is_admin, sau đó chuyển nó vào phương thức tạo, điều này giúp người dùng tự thăng cấp lên quản trị viên.

Vì vậy, trước khi bắt đầu, bạn nên xác định rõ các thuộc tính nào có thể được gán hàng loạt trên mô hình. Bạn có thể thực hiện việc này thông qua thuộc tính $fillable trên mô hình. Ví dụ: cho phép thuộc tính name của mô hình Flight có thể được gán hàng loạt:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Thuộc tính có thể được gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
Sau khi bạn đã thiết lập được các thuộc tính có thể được gán hàng loạt, bạn có thể sử dụng phương thức create để chèn dữ liệu mới vào cơ sở dữ liệu. Phương thức create sẽ trả về một thực thể mô hình đã lưu:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
Nếu bạn đã có một thực thể mô hình, bạn có thể truyền một mảng vào phương thức fill để gán giá trị:
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable có thể được coi là một "danh sách trắng" gán hàng loạt, bạn cũng có thể sử dụng $guarded để thực hiện. Thuộc tính $guarded chứa một mảng các thuộc tính mà không được phép gán hàng loạt. Nói cách khác, $guarded hoạt động chức năng như một "danh sách đen". Lưu ý: Bạn chỉ có thể sử dụng $fillable hoặc $guarded, không thể sử dụng cả hai cùng một lúc. Trong ví dụ dưới đây, tất cả các thuộc tính ngoại trừ thuộc tính price đều có thể được gán hàng loạt:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Thuộc tính không được gán hàng loạt.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Nếu bạn muốn tất cả các thuộc tính đều có thể được gán hàng loạt, bạn có thể định nghĩa $guarded thành một mảng trống:
```php
/**
 * Thuộc tính không được gán hàng loạt.
 *
 * @var array
 */
protected $guarded = [];
```

## Các phương thức tạo khác
firstOrCreate / firstOrNew
Ở đây có hai phương thức mà bạn có thể sử dụng để gán hàng loạt: firstOrCreate và firstOrNew. Phương thức firstOrCreate sẽ khớp với các cặp khóa/giá trị được cung cấp trong cơ sở dữ liệu. Nếu không tìm thấy mô hình trong cơ sở dữ liệu, sẽ chèn một bản ghi chứa thuộc tính của tham số đầu tiên và thuộc tính tùy chọn thứ hai.

Phương thức firstOrNew tương tự như firstOrCreate, nó cố gắng tìm kiếm các bản ghi trong cơ sở dữ liệu dựa trên thuộc tính cung cấp. Tuy nhiên, nếu phương thức firstOrNew không tìm thấy mô hình tương ứng, nó sẽ trả về một thực thể mô hình mới. Lưu ý rằng thực thể mô hình được trả về bởi firstOrNew chưa được lưu vào cơ sở dữ liệu, bạn cần gọi phương thức save thủ công để lưu:
```php
// Tìm kiếm chuyến bay thông qua tên, nếu không tồn tại thì tạo mới...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Tìm kiếm chuyến bay thông qua tên, hoặc tạo mới với thuộc tính name, thuộc tính thứ hai và thuộc tính arrival_time...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Tìm kiếm chuyến bay thông qua tên, nếu không tồn tại thì tạo mới một thực thể...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Tìm kiếm chuyến bay thông qua tên, hoặc tạo mới một thực thể với thuộc tính name, thuộc tính thứ hai và thuộc tính arrival_time...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

Bạn cũng có thể gặp trường hợp muốn cập nhật mô hình hiện có hoặc tạo mô hình mới nếu không tồn tại. Phương thức updateOrCreate có thể giải quyết cả hai chỉ một lần duy nhất. Tương tự như phương thức firstOrCreate, updateOrCreate lưu mô hình tự động, do đó không cần gọi save():
```php
// Nếu có chuyến bay từ Oakland đến San Diego, giá là 99 đô la.
// Nếu không có mô hình khớp, thì tạo một mô hình mới.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## Xóa mô hình

Bạn có thể gọi phương thức delete trên một thực thể mô hình để xóa thực thể đó:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Xóa mô hình dựa trên khóa chính
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Xóa mô hình dựa trên truy vấn
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Sao chép mô hình

Bạn có thể sử dụng phương thức replicate để sao chép một thực thể mới chưa được lưu vào cơ sở dữ liệu, phương thức này rất hữu ích khi một thực thể mô hình chia sẻ nhiều thuộc tính giống nhau.
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
Đôi khi của bạn có thể cần kiểm tra xem hai mô hình có "giống nhau" hay không. Phương thức is có thể được sử dụng để nhanh chóng kiểm tra xem hai mô hình có cùng khóa chính, bảng và kết nối cơ sở dữ liệu hay không:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Trình quan sát mô hình

Sử dụng tham khảo [Sự kiện và Trình quan sát mô hình trong Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Lưu ý: Để Eloquent ORM hỗ trợ Trình quan sát mô hình, bạn cần phải nhập thêm composer require "illuminate/events"
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

