# Về rò rỉ bộ nhớ
webman là một framework mãi mãi trong bộ nhớ, vì vậy chúng ta cần chú ý đến tình trạng rò rỉ bộ nhớ. Tuy nhiên, các nhà phát triển không cần quá lo lắng, vì rò rỉ bộ nhớ xảy ra trong điều kiện cực kỳ cận thận và dễ dàng tránh được. Việc phát triển webman tương tự như phát triển framework truyền thống, không cần thêm các hoạt động quản lý bộ nhớ không cần thiết.

> **Gợi ý**
> Quá trình theo dõi mặc định của webman sẽ giám sát sử dụng bộ nhớ của tất cả các quá trình. Nếu sử dụng bộ nhớ của quá trình sắp đạt đến giá trị `memory_limit` được thiết lập trong php.ini, quá trình tương ứng sẽ tự động khởi động lại một cách an toàn để giải phóng bộ nhớ, trong quá trình này không ảnh hưởng đến doanh nghiệp.

## Định nghĩa rò rỉ bộ nhớ
Với việc tăng các yêu cầu, bộ nhớ được sử dụng bởi webman cũng **tăng không giới hạn** (chú ý là **tăng không giới hạn**), đạt hàng trăm megabyte hoặc hơn, đây được coi là rò rỉ bộ nhớ. Nếu bộ nhớ tăng lên và không tăng lên sau đó không phải là rò rỉ bộ nhớ.

Bình thường, việc quá trình sử dụng vài chục megabyte bộ nhớ là tình trạng bình thường, khi quá trình xử lý yêu cầu cực lớn hoặc duy trì kết nối hàng loạt, sự sử dụng bộ nhớ của một quá trình có thể lên đến hàng trăm megabyte là chuyện thường. Một phần bộ nhớ sau khi sử dụng không nhất thiết phải trả lại toàn bộ cho hệ điều hành. Thay vào đó, một phần nhỏ có thể được giữ lại để tái sử dụng, vì vậy có thể xảy ra trường hợp sau khi xử lý một yêu cầu lớn, sử dụng bộ nhớ tăng lên nhưng không giải phóng bộ nhớ, đây là hiện tượng bình thường. (Gọi gc_mem_caches() sẽ giải phóng một số bộ nhớ trống)

## Làm thế nào để rò rỉ bộ nhớ xảy ra
**Rò rỉ bộ nhớ xảy ra khi phải đáp ứng đồng thời hai điều kiện sau đây:**
1. Tồn tại một mảng **với chu kỳ sống lâu** (chú ý là mảng với chu kỳ sống lâu, mảng thông thường không sao)
2. Và mảng **với chu kỳ sống lâu** này sẽ tăng không giới hạn (doanh nghiệp không ngừng chèn dữ liệu vào, không bao giờ dọn dẹp dữ liệu)

Nếu đáp ứng đồng thời các điều kiện 1 2 trên (chú ý là đồng thời), thì sẽ xảy ra rò rỉ bộ nhớ. Ngược lại, không đáp ứng một trong các điều kiện trên hoặc chỉ đáp ứng một điều kiện thì không phải rò rỉ bộ nhớ.

## Mảng với chu kỳ sống lâu
Các mảng với chu kỳ sống lâu trong webman bao gồm:
1. Mảng với từ khóa static
2. Thuộc tính mảng đơn lẻ của đối tượng duy nhất
3. Mảng với từ khóa global

> **Chú ý**
> Trong webman, việc sử dụng dữ liệu với chu kỳ sống lâu được phép, nhưng phải đảm bảo rằng dữ liệu trong mảng có hạn chế, số lượng phần tử không tăng không giới hạn.

Dưới đây là các ví dụ cụ thể

#### Mảng static tăng không giới hạn
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('xin chào');
    }
}
```

Dùng từ khóa `static` để định nghĩa mảng `$data` là một mảng với chu kỳ sống lâu, và mảng `$data` trong ví dụ này ngày càng tăng theo yêu cầu mà không ngừng, dẫn đến rò rỉ bộ nhớ.

#### Mảng thuộc tính đơn lẻ tăng không giới hạn
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```

Đoạn mã gọi
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('xin chào');
    }
}
```

`Cache::instance()` trả về một đối tượng Cache duy nhất, nó là một thể hiện với chu kỳ sống lâu, mặc dù thuộc tính `$data` của nó không dùng từ khóa `static`, nhưng do lớp chính nó có chu kỳ sống lâu, nên `$data` cũng là một mảng với chu kỳ sống lâu. Khi có thêm dữ liệu khác nhau vào mảng `$data`, bộ nhớ của chương trình cũng tăng lên từng ngày, gây ra rò rỉ bộ nhớ.

> **Chú ý**
> Nếu các key được thêm vào bằng `Cache::instance()->set(key, value)` chỉ là số lượng hạn chế, thì không có rò rỉ bộ nhớ, vì mảng `$data` không phát triển không giới hạn.

#### Mảng global tăng không giới hạn
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
Mảng được định nghĩa bằng từ khóa global sẽ không được thu hồi sau khi hàm hoặc phương thức lớp thực thi xong, do đó nó là một mảng với chu kỳ sống lâu, đoạn mã trên với sự tăng không ngừng của yêu cầu sẽ dẫn đến rò rỉ bộ nhớ. Tương tự, mảng được định nghĩa bằng từ khóa static bên trong hàm hoặc phương thức lớp cũng là một mảng với chu kỳ sống lâu, nếu mảng tăng giới hạn không ngừng cũng sẽ gây rò rỉ bộ nhớ, ví dụ:
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```

## Lời khuyên
Chúng tôi đề xuất cho các nhà phát triển không cần phải quá quan tâm đến rò rỉ bộ nhớ, vì nó xảy ra rất ít, nếu rủi ro xảy ra, chúng ta có thể tìm ra điểm code nào gây rò rỉ thông qua các bài kiểm tra tải, từ đó xác định được vấn đề. Ngay cả khi nhà phát triển không tìm ra điểm rò rỉ, dịch vụ theo dõi tích hợp sẵn của webman sẽ khởi động lại quá trình gây rò rỉ bộ nhớ an toàn thích hợp, giải phóng bộ nhớ.

Nếu bạn muốn cố gắng hạn chế rò rỉ bộ nhớ càng nhiều càng tốt, bạn có thể tham khảo các lời khuyên sau đây.
1. Tránh sử dụng từ khóa `global`, `static` để định nghĩa mảng, nếu phải sử dụng, đảm bảo rằng chúng sẽ không phát triển không giới hạn
2. Đối với các lớp không quen, hãy tránh sử dụng đối tượng duy nhất, sử dụng từ khóa new để khởi tạo. Nếu phải sử dụng đối tượng duy nhất, hãy xem xét xem chúng có tính chất phát triển không giới hạn của mảng không
