# Về rò rỉ bộ nhớ
webman là một framework bền vững trong bộ nhớ, vì vậy chúng ta cần quan tâm đến tình trạng rò rỉ bộ nhớ. Tuy nhiên, các nhà phát triển không cần lo lắng quá nhiều, vì rò rỉ bộ nhớ xảy ra trong điều kiện cực kỳ cụ thể và dễ dàng tránh được. Việc phát triển webman cơ bản tương đương với việc phát triển framework truyền thống, không cần thực hiện quá nhiều hoạt động quản lý bộ nhớ.

> **Gợi ý**
> Tiến trình giám sát có sẵn trong webman sẽ theo dõi tình trạng sử dụng bộ nhớ của tất cả các tiến trình. Nếu sử dụng bộ nhớ của tiến trình sắp đạt đến giá trị được đặt trong `memory_limit` của php.ini, tiến trình tương ứng sẽ tự động khởi động lại một cách an toàn, đồng thời giải phóng bộ nhớ mà không ảnh hưởng đến doanh nghiệp trong thời gian này.

## Định nghĩa rò rỉ bộ nhớ
Khi số lượng yêu cầu tăng lên, bộ nhớ sử dụng của webman cũng sẽ **tăng lên không giới hạn** (lưu ý là **tăng lên không giới hạn**), đạt đến hàng trăm MB hoặc thậm chí nhiều hơn, điều này được gọi là rò rỉ bộ nhớ.
Nếu bộ nhớ tăng lên sau đó không còn tăng nữa thì không tính là rò rỉ bộ nhớ.

Thường thì việc tiến trình chiếm một số bộ nhớ vài chục MB là một tình huống rất bình thường. Khi tiến trình xử lý yêu cầu lớn hoặc duy trì kết nối lượng lớn, sẽ thường xuyên dẫn đến việc tiến trình sử dụng bộ nhớ lên đến hàng trăm MB, đây cũng là điều thường gặp. Phần sử dụng bộ nhớ này có thể không được trả về hết cho hệ điều hành sau khi sử dụng. Thay vào đó, nó sẽ được giữ lại để tái sử dụng, vì vậy có thể xảy ra tình trạng sau khi xử lý một yêu cầu lớn, tiến trình sử dụng bộ nhớ tăng lên mà không giải phóng bộ nhớ, đây là hiện tượng bình thường. (Gọi phương thức gc_mem_caches() có thể giải phóng một phần bộ nhớ trống).

## Rò rỉ bộ nhớ xảy ra như thế nào
**Rò rỉ bộ nhớ chỉ xảy ra khi đáp ứng đồng thời hai điều kiện sau đây:**
1. Tồn tại một mảng **có vòng đời dài**(lưu ý là mảng có **vòng đời dài**, mảng thông thường không có vấn đề)
2. Và mảng **có vòng đời dài** này sẽ không ngừng mở rộng (doanh nghiệp không ngừng chèn dữ liệu vào mảng này mà không dọn dẹp dữ liệu)

Nếu **đồng thời đáp ứng** điều kiện 1 và 2, thì sẽ xảy ra rò rỉ bộ nhớ. Ngược lại, không đáp ứng một trong những điều kiện trên hoặc chỉ đáp ứng một trong hai điều kiện thì sẽ không phải là rò rỉ bộ nhớ.

## Mảng có vòng đời dài

Các mảng có vòng đời dài trong webman bao gồm:
1. Mảng với từ khóa static
2. Thuộc tính mảng của đối tượng singleton
3. Mảng với từ khóa global

> **Lưu ý**
> Trong webman, cho phép sử dụng dữ liệu với vòng đời dài, nhưng cần đảm bảo rằng dữ liệu trong mảng là hữu hạn, số phần tử không thể mở rộng không giới hạn.

Dưới đây là các ví dụ để minh họa

#### Mảng static mở rộng không giới hạn
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

Mảng `$data` được định nghĩa với từ khóa `static` là một mảng có vòng đời dài, và trong ví dụ này, mảng `$data` mở rộng không ngừng theo yêu cầu, dẫn đến rò rỉ bộ nhớ.

#### Mảng thuộc tính của đối tượng singleton mở rộng không giới hạn
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

Mã nguồn

```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

`Cache::instance()` trả về một đối tượng singleton Cache, đây là một đối tượng có vòng đời dài, mặc dù thuộc tính `$data` không sử dụng từ khóa `static`, nhưng vì lớp đối tượng này có vòng đời dài, nên `$data` cũng là một mảng có vòng đời dài. Khi không ngừng thêm dữ liệu với các khóa khác nhau vào mảng `$data`, việc sử dụng bộ nhớ của chương trình cũng tăng dần, gây ra rò rỉ bộ nhớ.

> **Lưu ý**
> Nếu Cache::instance()->set(key, value) thêm key có số lượng hữu hạn, thì không có rò rỉ bộ nhớ, vì mảng `$data` không mở rộng không giới hạn.

#### Mảng global mở rộng không giới hạn
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
Mảng được định nghĩa với từ khóa global không được giải phóng sau khi hàm hoặc phương thức kết thúc, vì vậy nó là một mảng có vòng đời dài. Mã nguồn trên theo yêu cầu ngày càng tăng sẽ dẫn đến rò rỉ bộ nhớ. Tương tự, mảng được định nghĩa với từ khóa static trong hàm hoặc phương thức cũng là mảng có vòng đời dài, nếu mảng mở rộng không giới hạn cũng sẽ dẫn đến rò rỉ bộ nhớ, ví dụ:
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

## Đề xuất
Chúng tôi khuyên nhà phát triển không cần quá quan tâm đến rò rỉ bộ nhớ, vì nó xảy ra rất ít, nếu không may xảy ra, chúng ta có thể tìm ra điểm gây rò rỉ bộ nhớ thông qua kiểm tra sức chịu đựng, từ đó xác định vấn đề. Ngay cả khi không tìm ra điểm gây rò rỉ, monitor dịch vụ có sẵn trong webman sẽ khởi động lại tiến trình có rò rỉ bộ nhớ một cách an toàn và giải phóng bộ nhớ.

Nếu bạn muốn tránh rò rỉ bộ nhớ càng nhiều càng tốt, bạn có thể tham khảo các đề xuất sau.
1. Tránh sử dụng từ khóa `global`, `static` khi khai báo mảng, nếu sử dụng, hãy chắc chắn rằng chúng sẽ không mở rộng không giới hạn.
2. Đối với các lớp không quen thuộc, hãy tránh sử dụng singleton, sử dụng từ khóa `new` để khởi tạo. Nếu cần singleton, hãy kiểm tra xem có thuộc tính mảng mở rộng không giới hạn không.
