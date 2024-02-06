# Kiểm tra áp lực

### Các yếu tố nào ảnh hưởng đến kết quả kiểm tra áp lực?
* Độ trễ mạng từ máy áp lực đến máy chủ (nên kiểm tra trong mạng nội bộ hoặc trên máy áp lực)
* Băng thông từ máy áp lực đến máy chủ (nên kiểm tra trong mạng nội bộ hoặc trên máy áp lực)
* Việc bật HTTP keep-alive (nên bật)
* Số kết nối đồng thời có đủ không (khi áp lực từ bên ngoài, nên tăng số kết nối đồng thời)
* Số tiến trình máy chủ có hợp lý không (đối với dịch vụ helloworld, nên có cùng số tiến trình với số CPU; đối với dịch vụ cơ sở dữ liệu, nên có từ bốn lần số CPU trở lên)
* Hiệu suất của dịch vụ (ví dụ: liệu có sử dụng cơ sở dữ liệu bên ngoài hay không)

### HTTP keep-alive là gì?
Cơ chế HTTP Keep-Alive là một kỹ thuật dùng để gửi nhiều yêu cầu và phản hồi HTTP trên một kết nối TCP duy nhất, nó có ảnh hưởng lớn đến kết quả kiểm tra áp lực. Nếu tắt keep-alive, số yêu cầu trên giây (QPS) có thể giảm gấp đôi.

Hiện tại, trình duyệt đều mặc định bật keep-alive, nghĩa là sau khi trình duyệt truy cập vào một địa chỉ HTTP nào đó, nó sẽ tạm giữ kết nối thay vì đóng lại, và sử dụng lại kết nối này cho lần yêu cầu tiếp theo, nhằm tăng hiệu suất.
Trong quá trình kiểm tra áp lực, nên bật keep-alive.

### Làm thế nào để bật HTTP keep-alive khi kiểm tra áp lực?
Nếu dùng chương trình ab để kiểm tra áp lực, cần thêm tham số -k, ví dụ `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Cần trả về header gzip cho apipost mới có thể bật keep-alive (đây là lỗi của apipost, xem thêm dưới đây).
Các chương trình kiểm tra áp lực khác thường mặc định đã bật.

### Tại sao QPS lại thấp khi kiểm tra áp lực từ bên ngoài?
Việc trễ mạng từ bên ngoài làm giảm QPS là hiện tượng bình thường. Ví dụ, khi kiểm tra áp lực trang web của baidu, QPS có thể chỉ là vài chục.
Nên thử kiểm tra áp lực trong mạng nội bộ hoặc trên máy cá nhân để loại bỏ ảnh hưởng của trễ mạng.
Nếu thực sự muốn kiểm tra áp lực từ bên ngoài, có thể tăng số kết nối đồng thời để tăng công suất xử lý (phải đảm bảo băng thông đủ).

### Tại sao hiệu năng lại giảm sau khi thông qua proxy của nginx?
Quá trình chạy của nginx tiêu tốn tài nguyên hệ thống. Đồng thời, việc giao tiếp giữa nginx và webman cũng tốn tài nguyên.
Tuy nhiên, tài nguyên của hệ thống là có hạn, webman không thể sử dụng hết tất cả tài nguyên hệ thống, nên việc hiệu suất của toàn bộ hệ thống giảm một chút là hiện tượng bình thường.
Để giảm thiểu ảnh hưởng về hiệu năng do proxy của nginx, có thể xem xét tắt nhật ký của nginx (`access_log off;`), bật keep-alive giữa nginx và webman, tham khảo [proxy của nginx](nginx-proxy.md).

Bên cạnh đó, so với HTTP, HTTPS tốn tài nguyên hơn vì phải thực hiện bắt tay SSL/TLS, mã hóa/giải mã dữ liệu, kích thước gói tin tăng lên chiếm nhiều băng thông hơn, tất cả điều này dẫn đến giảm hiệu năng.
Nếu kiểm tra áp lực dùng kết nối ngắn (không bật HTTP keep-alive), mỗi lần yêu cầu đều cần bắt tay SSL/TLS thêm, làm giảm hiệu năng đáng kể. Nên bật HTTP keep-alive khi kiểm tra áp lực dùng HTTPS.

### Làm sao biết hệ thống đã đạt đến giới hạn hiệu suất?
Thường thì khi CPU đạt 100% thì mức hiệu suất của hệ thống đã đạt đến giới hạn. Nếu CPU vẫn còn trống thì chưa đạt giới hạn, lúc này có thể tăng số kết nối đồng thời để tăng QPS.
Nếu tăng số kết nối đồng thời mà vẫn không tăng QPS thì có thể là số tiến trình của webman chưa đủ, có thể tăng số tiến trình của webman. Nếu vẫn không tăng thì cần xem xét có đủ băng thông không.

### Tại sao kết quả kiểm tra áp lực của webman lại thấp hơn framework gin của go?
Các kết quả kiểm tra áp lực của [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) cho thấy webman vượt qua gin từ 1.5 đến 2 lần ở tất cả các chỉ số như văn bản thuần túy, truy vấn cơ sở dữ liệu, cập nhật cơ sở dữ liệu, v.v.
Nếu kết quả của bạn không giống như vậy, có thể là do bạn đang sử dụng ORM trong webman gây mất mát hiệu suất lớn, có thể thử so sánh webman + PDO nguyên thủy với gin + SQL nguyên thủy.

### Sử dụng ORM trong webman làm giảm hiệu suất bao nhiêu?
Dưới đây là một bộ dữ liệu kiểm tra áp lực.

**Môi trường**
Máy chủ 4 nhân 4G trên Alibaba Cloud, truy vấn một bản ghi ngẫu nhiên từ 100,000 bản ghi rồi trả về JSON.

**Nếu sử dụng PDO nguyên thủy**
QPS của webman là 17,800

**Nếu sử dụng Db::table() của Laravel**
QPS của webman giảm xuống 9,400

**Nếu sử dụng Model của Laravel**
QPS của webman giảm xuống 7,200

Kết quả của ThinkORM tương tự, khác biệt không lớn.

> **Gợi ý**
> Mặc dù sử dụng ORM có thể làm giảm hiệu suất nhưng đối với hầu hết các dịch vụ, điều đó là đủ dùng. Chúng ta nên tìm ra một sự cân bằng giữa hiệu suất phát triển, bảo trì, và hiệu suất, thay vì chỉ tập trung duy nhất vào hiệu suất.

### Tại sao việc kiểm tra áp lực bằng apipost lại có QPS thấp?
Mô-đun kiểm tra áp lực của apipost có lỗi, nếu máy chủ không trả về header gzip thì không thể duy trì keep-alive, dẫn đến giảm sút hiệu suất.
Giải pháp là trả về dữ liệu đã nén và thêm header gzip khi trả về, ví dụ
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
Ngoài ra, trong một số trường hợp, apipost không thể tạo ra áp lực như mong muốn, cho thấy khả năng QPS của apipost có thể thấp khoảng 50% so với ab.
Khi kiểm tra áp lực, nên sử dụng ab, wrk hoặc các phần mềm kiểm tra áp lực chuyên nghiệp khác thay vì apipost.

### Thiết lập số tiến trình phù hợp
Webman mặc định mở số tiến trình là cpu*4. Trong thực tế, dịch vụ hello world mà không thực hiện IO mạng, việc đặt số tiến trình bằng số nhân CPU cho hiệu suất tối ưu nhất, bởi vì việc giảm chi phí chuyển tiến trình.
Đối với dịch vụ có IO chặn như cơ sở dữ liệu, redis, số tiến trình có thể đặt từ 3 đến 8 lần số nhân CPU, vì lúc này cần nhiều tiến trình hơn để tăng đồng thời, và chi phí chuyển tiến trình có thể bỏ qua so với IO chặn.

### Một số phạm vi tham khảo khi kiểm tra áp lực

**Máy chủ đám mây 4 nhân 4G 16 tiến trình kiểm tra áp lực trong mạng nội bộ/ở máy cá nhân**

| - | Bật keep-alive | Chưa bật keep-alive |
|--|-----|-----|
| Xin chào thế giới | 8-16 vạn QPS | 1-3 vạn QPS |
| Truy vấn cơ sở dữ liệu đơn | 1-2 vạn QPS | 1 vạn QPS |

[**Dữ liệu kiểm tra áp lực của bên thứ ba techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Ví dụ các lệnh kiểm tra áp lực

**ab**
```
# 100,000 yêu cầu, 200 đồng thời, bật keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100,000 yêu cầu, 200 đồng thời, không bật keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# 200 đồng thời, kiểm tra áp lực trong 10 giây, bật keep-alive (mặc định)
wrk -c 200 -d 10s http://example.com
```

