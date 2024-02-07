# Kiểm tra áp lực

## Các yếu tố nào ảnh hưởng đến kết quả kiểm tra áp lực?
* Độ trễ mạng từ máy áp lực đến máy chủ (đề xuất kiểm tra mạng nội bộ hoặc máy áp lực)
* Băng thông từ máy áp lực đến máy chủ (đề xuất kiểm tra mạng nội bộ hoặc máy áp lực)
* Có mở HTTP keep-alive không (đề xuất mở)
* Số lượng kết nối đồng thời có đủ không (trong trường hợp kiểm tra áp lực từ bên ngoại, hãy cố gắng tăng số lượng kết nối đồng thời)
* Số quá trình máy chủ có hợp lý không (đề xuất số quá trình dịch vụ helloworld nên bằng số CPU; số quá trình dịch vụ doanh nghiệp cơ sở dữ liệu nên là bốn lần hoặc nhiều hơn số CPU)
* Hiệu suất doanh nghiệp (ví dụ: liệu có sử dụng cơ sở dữ liệu từ bên ngoại không)


## HTTP keep-alive là gì?
Cơ chế HTTP Keep-Alive là một công nghệ dùng để gửi nhiều yêu cầu và phản hồi HTTP qua một kết nối TCP duy nhất, điều này ảnh hưởng rất lớn đến kết quả kiểm tra hiệu suất vì nếu tắt keep-alive, QPS có thể giảm đi gấp đôi. Hiện nay, trình duyệt mặc định đều mở keep-alive, tức là sau khi truy cập vào một địa chỉ HTTP, kết nối sẽ được giữ lại và không đóng lại, để sử dụng cho lần yêu cầu tiếp theo, từ đó tăng hiệu suất. Khi kiểm tra áp lực, đề xuất mở keep-alive.

## Làm thế nào để mở HTTP keep-alive khi kiểm tra áp lực?
Nếu sử dụng chương trình kiểm tra ab, cần thêm tham số -k, ví dụ như `ab -n100000 -c200 -k http://127.0.0.1:8787/`. Đối với apipost, cần trả về header nén gzip để mở keep-alive (lỗi của apipost, xem phần dưới) . Các chương trình kiểm tra áp lực khác thường mặc định mở.

## Vì sao QPS khi kiểm tra áp lực từ bên ngoại thấp?
Độ trễ từ bên ngoại lớn sẽ làm giảm QPS, đây là hiện tượng bình thường. Ví dụ, khi kiểm tra áp lực trang web baidu, QPS có thể chỉ là vài chục. Đề xuất kiểm tra áp lực từ máy nội bộ hoặc máy áp lực để loại bỏ ảnh hưởng từ độ trễ mạng. Nếu nhất định muốn kiểm tra áp lực từ bên ngoài, có thể tăng số lượng kết nối đồng thời để tăng thông lượng (cần đảm bảo có đủ băng thông).

## Tại sao hiệu suất giảm sau khi thông qua việc chuyển tiếp qua nginx?
Việc hoạt động của nginx sẽ tiêu tốn tài nguyên hệ thống. Đồng thời, việc giao tiếp giữa nginx và webman cũng sẽ tiêu tốn một số tài nguyên. Tuy nhiên, tài nguyên của hệ thống là có hạn, webman không thể lấy được tất cả tài nguyên hệ thống, do đó, việc giảm hiệu suất của toàn bộ hệ thống là điều bình thường. Để giảm thiểu ảnh hưởng của việc chuyển tiếp qua nginx đến hiệu suất, có thể xem xét tắt nhật ký của nginx (`access_log off;`), mở keep-alive từ nginx đến webman, xem thêm tại [chuyển tiếp nginx](nginx-proxy.md). Ngoài ra, so với HTTP, HTTPS sẽ tiêu tốn nhiều tài nguyên hơn, vì cần thực hiện bắt tay SSL/TLS, mã hóa và giải mã dữ liệu, kích thước gói dữ liệu tăng lên chiếm nhiều băng thông hơn, điều này sẽ làm giảm hiệu suất. Trong trường hợp kiểm tra áp lực sử dụng kết nối ngắn (không mở keep-alive HTTP), mỗi yêu cầu sẽ cần thêm thời gian để thực hiện bắt tay SSL/TLS, hiệu suất sẽ giảm đi đáng kể. Đề xuất mở keep-alive HTTP khi kiểm tra áp lực HTTPS.

## Làm thế nào để biết hệ thống đã đạt đến giới hạn hiệu suất?
Nhìn chung, khi CPU đạt 100%, có nghĩa là hệ thống đã đạt đến giới hạn hiệu suất. Nếu CPU vẫn còn trống rảnh, điều này có nghĩa là chưa đạt đến giới hạn hiệu suất, lúc này có thể tăng số lượng kết nối đồng thời để tăng QPS. Nếu tăng số lượng kết nối mà vẫn không tăng QPS, có thể là do số lượng quá trình webman không đủ, cần tăng số quá trình webman. Nếu vẫn không tăng được, cần xem xét xem băng thông có đủ không.

## Tại sao kết quả kiểm tra áp lực webman lại thấp hơn Framework gin của go?
Kết quả kiểm tra áp lực từ [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) cho thấy webman vượt qua gin gần gấp đôi ở tất cả các chỉ số như văn bản thuần túy, truy vấn cơ sở dữ liệu, cập nhật cơ sở dữ liệu. Nếu kết quả của bạn khác, có thể là do bạn đã sử dụng ORM trong webman, dẫn đến mất hiệu suất lớn, hãy thử so sánh webman + PDO nguyên bản và gin + SQL nguyên bản.

## Sử dụng ORM trong webman sẽ làm giảm hiệu suất bao nhiêu?
Dưới đây là một số dữ liệu kiểm tra áp lực

**Môi trường**
Máy chủ đám mây 4 nhân 4G, trả về một bản ghi JSON ngẫu nhiên từ 100,000 bản ghi.

**Nếu sử dụng PDO nguyên bản**
QPS của webman là 1,78 vạn

**Nếu sử dụng Laravel’s Db::table()**
QPS của webman giảm xuống còn 0,94 vạn

**Nếu sử dụng model của Laravel**
QPS của webman giảm xuống còn 0,72 vạn

Kết quả của ThinkORM tương tự, không khác biệt lớn.

> **Gợi ý**
> Mặc dù sử dụng ORM sẽ làm giảm hiệu suất, nhưng với hầu hết các doanh nghiệp, đây là đủ. Chúng ta nên tìm thấy một sự cân bằng giữa hiệu suất, hiệu quả phát triển và khả năng bảo trì thay vì chỉ tập trung vào hiệu suất.

## Tại sao QPS rất thấp khi kiểm tra áp lực bằng apipost?
Mô-đun kiểm tra áp lực của apipost có lỗi, nếu máy chủ không trả về header nén gzip, thì không thể duy trì keep-alive, dẫn đến giảm hiệu suất mạnh mẽ. Phương pháp giải quyết làm trả về dữ liệu sau khi nén và thêm header gzip, ví dụ như
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
Ngoài ra, apipost có những trường hợp không thể tạo áp lực đáng tin cậy, có thể thấy sự giảm QPS khoảng 50% so với ab cùng số lượng kết nối đồng thời. Đề xuất sử dụng ab, wrk hoặc phần mềm kiểm tra áp lực chuyên nghiệp khác thay vì apipost.

## Thiết lập số quá trình hợp lý
webman mặc định mở 16 quá trình (4 lần số lõi CPU). Trên thực tế, trong trường hợp không có IO mạng, việc mở số quá trình kiểu helloworld giống với số lõi CPU sẽ có hiệu suất tốt nhất, vì có thể giảm chi phí chuyển đổi quá trình. Nếu là doanh nghiệp có cơ sở dữ liệu, redis hoặc các dịch vụ IO chậm, số quá trình có thể được đặt từ 3-8 lần số lõi CPU, vì lúc này cần nhiều quá trình hơn để tăng số lượng đồng thời, trong khi chi phí chuyển đổi quá trình so với IO chậm gần như có thể bỏ qua.

## Một số phạm vi kiểm tra áp lực tham khảo

**Máy chủ đám mây 4 nhân 4G 16 quá trình kiểm tra từ bên ngoại/bên trong mạng nội bộ**

| - | Mở keep-alive | Không mở keep-alive |
|--|-----|-----|
| hello world | 8-16 vạn QPS | 1-3 vạn QPS |
| Truy vấn cơ sở dữ liệu đơn | 1-2 vạn QPS | 1 vạn QPS |

[**Dữ liệu kiểm tra áp lực của bên thứ ba techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


## Ví dụ lệnh kiểm tra áp lực

**ab**
```php
# 100,000 yêu cầu, 200 đồng thời, mở keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100,000 yêu cầu, 200 đồng thời, không mở keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```php
# Kiểm tra áp lực trong 10 giây với 200 đồng thời, mở keep-alive (mặc định)
wrk -c 200 -d 10s http://example.com
```

